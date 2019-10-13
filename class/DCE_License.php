<?php
// https://woosoftwarelicense.com/documentation/wordpress-plugin-autoupdate-api-integration-code-example/

// https://shop.dynamic.ooo/wp-update-server/?action=get_metadata&slug=dynamic-content-for-elementor
namespace DynamicContentForElementor;

//https://woosoftwarelicense.com/documentation/api-methods/
class DCE_License {
    
    public $license_key;
    
    public function __construct() {
        $this->init();
    }

    public function init() {
        $this->activation_advisor();
        
        // gestisco lo scaricamento dello zip aggiornato inviando i dati della licenza
        add_filter( 'upgrader_pre_download', array($this, 'filter_upgrader_pre_download'), 10, 3 );
    }
    
    static public function set_constant() {
        define('SL_APP_DEMO_URL', 'https://www.dynamic.ooo');
        //the url where the WooCommerce Software License plugin is being installed
        define('SL_APP_API_URL', 'https://shop.dynamic.ooo');
        //the Software Unique ID as defined within product admin page
        define('SL_PRODUCT_ID', 'WP-DCE-1');
        //A code variable constant is required, which is the user application code version. This will be used by API to compare against the new version on shop server.
        define('SL_VERSION', DCE_VERSION);
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        define('SL_INSTANCE', str_replace($protocol, "", get_bloginfo('wpurl')));
        $license = get_option( SL_PRODUCT_ID . '_license_key' );
        define('SL_LICENSE', $license);
    }
    
    public function activation_advisor() {
        $license_activated = get_option( SL_PRODUCT_ID . '_license_activated' );
        //var_dump($license_activated);
        $tab_license = (isset($_GET['tab']) && $_GET['tab'] == 'license') ? true : false;
        if (!$license_activated && !$tab_license) {
            add_action( 'admin_notices', '\DynamicContentForElementor\DCE_Notice::dce_admin_notice__license' );
            add_filter( 'plugin_action_links_' . DCE_PLUGIN_BASE,  '\DynamicContentForElementor\DCE_License::dce_plugin_action_links_license' );
        }
    }
    
    // define the upgrader_pre_download callback
    public function filter_upgrader_pre_download( $false, $package, $instance ) {
        //var_dump($package);
        //var_dump($instance);
        //die();
        // ottengo lo slug del plugin corrente
        $plugin = false;
        if (property_exists($instance, 'skin')) {
            if ($instance->skin) {
                if (property_exists($instance->skin, 'plugin')) {
                    // aggiornamento da pagina
                    if ($instance->skin->plugin) {
                        $pezzi = explode('/', $instance->skin->plugin);
                        $plugin = reset($pezzi);
                    }
                }
                if (!$plugin && isset($instance->skin->plugin_info["TextDomain"])) {
                    // aggiornamento ajax
                    $plugin = $instance->skin->plugin_info["TextDomain"];
                }
            }
        }
        //var_dump($plugin); die();
        // agisco solo per il mio plugin
        if ($plugin == DCE_TEXTDOMAIN) {
            return $this->upgrader_pre_download($package);
            //\DynamicContentForElementor\DCE_license::upgraderPreDownload();
        }
        return $false;
    }
    
    public function upgrader_pre_download($package) {
        //solo se stò aggiornando lo shop stesso (caso isolato)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $shopInstance = str_replace($protocol, "", SL_APP_API_URL);
        if (SL_INSTANCE == $shopInstance) {
            global $wp_filesystem;
            $file = $wp_filesystem->abspath() . '.maintenance';
            $wp_filesystem->delete($file);
        }
        // ora verifico la licenza
        $license_status = self::call_api('status-check', SL_LICENSE, false);
        if (!$license_status) {
            if (!SL_LICENSE) {
                // l'utente non ha ancora impostato alcun codice di licenza
                return new \WP_Error('no_license', __('You have not entered the license.', DCE_TEXTDOMAIN).' <a target="_blank" href="'.SL_APP_API_URL.'">'.__('If you do not have one then buy it now', DCE_TEXTDOMAIN).'</a>');
            }
            // qualcosa è andato storto...stampo tutti gli errori
            $license_dump = self::call_api('status-check', SL_LICENSE, false, true);
            if(is_wp_error( $license_dump ) || $license_dump['response']['code'] != 200) {
                return new \WP_Error('no_license', __('Error connecting to the server.', DCE_TEXTDOMAIN).' -- KEY: '.SL_LICENSE.' - DOMAIN: '.SL_INSTANCE. ' - STATUS-CHECK: '.var_export($license_dump, true));
            }
            // oppure semplicemente la licenza utilizzata non è attiva o valida
            return new \WP_Error('no_license', __('The license is not valid.', DCE_TEXTDOMAIN).' <a href="./admin.php?page=dce_opt&tab=license&licence_check=1">'.__('Check it in the plugin settings', DCE_TEXTDOMAIN).'</a>.');
        }
        // aggiungo quindi le info aggiuntive della licenza alla richiesta per abilitarmi al download
        $package .= '?license_key='.SL_LICENSE.'&license_instance='.SL_INSTANCE;
        if (get_option('dce_beta', false)) {
            $package .= '&beta=true';
        }
        //return new WP_Error('no_license', $package);
        $download_file = download_url($package);
        if ( is_wp_error($download_file) )
                return new \WP_Error('download_failed', __('Error downloading the update package', DCE_TEXTDOMAIN), $download_file->get_error_message());
        return $download_file;
    }

    //https://shop.dynamic.ooo/?woo_sl_action=status-check&licence_key=dce--34af115d-ba032b43-6387e80d&product_unique_id=WP-DCE-1&domain=dynami.co%22
    static public function call_api($action, $license_key, $iNotice = false, $debug = false) {
        $args = array(
            'woo_sl_action' => $action,
            'licence_key'  => $license_key,
            'product_unique_id' => SL_PRODUCT_ID,
            'domain' => SL_INSTANCE
        );
        $request_uri = SL_APP_API_URL . '?' . http_build_query( $args );
        $data = wp_remote_get( $request_uri );
        //var_dump($args); //die();

        if(is_wp_error( $data ) || $data['response']['code'] != 200) {
            //echo '-- ERROR 200 --'; var_dump($data);
            if ($debug) {
                return $data;
            }
            //there was a problem establishing a connection to the API server
            add_action( 'admin_notices', 'DCE_Notice::dce_admin_notice__server_error' );
            return false;
        }

        $data_body = json_decode($data['body']);
        if (is_array($data_body)) {
            $data_body = reset($data_body);
        }
        //var_dump($data_body);
        if(isset($data_body->status)) {
            if($data_body->status == 'success') {
                if (($action == 'status-check' && ($data_body->status_code == 's200' || $data_body->status_code == 's205')) ||
                    ($action == 'activate' && ($data_body->status_code == 's100' || $data_body->status_code == 's101')) ||
                    ($action == 'deactivate' && $data_body->status_code == 's201') ||
                    ($action == 'plugin-update' && $data_body->status_code == '401')) {
                    //the license is active and the software is active
                    if ($debug) {
                        return $data;
                    }
                    if ($iNotice) { 
                        DCE_Notice::dce_admin_notice__success($data_body->message); 
                    } else {
                        add_option('dce_notice', $data_body->message);
                        add_action( 'admin_notices', 'DCE_Notice::dce_admin_notice__success' );
                    }
                    //doing further actions like saving the license and allow the plugin to run
                    //var_dump($data_body);
                    return true;
                } else {
                    if ($debug) {
                        return $data;
                    }
                    if ($iNotice) { 
                        DCE_Notice::dce_admin_notice__warning($data_body->message); 
                    } else {
                        add_option('dce_notice', $data_body->message.' - domain: '.SL_INSTANCE);
                        add_action( 'admin_notices', 'DCE_Notice::dce_admin_notice__warning' );
                    }
                    //var_dump($data_body); //die();
                    //return $data_body;
                    return false;
                }
            } else {
                if ($debug) {
                    return $data;
                }
                //there was a problem activating the license
                if ($iNotice) { 
                    DCE_Notice::dce_admin_notice__warning($data_body->message); 
                } else {
                    add_option('dce_notice', $data_body->message.' - domain: '.SL_INSTANCE);
                    add_action( 'admin_notices', 'DCE_Notice::dce_admin_notice__warning' );
                }
                //var_dump($data_body); //die();
                //return $data_body;
                return false;
            }
        } else {
            //echo '-- ERROR status --'; //var_dump($data);
            if ($debug) {
                return $data;
            }
            //there was a problem establishing a connection to the API server
            add_action( 'admin_notices', 'DCE_Notice::dce_admin_notice__server_error' );
            return false;
        }
    }
    
    static public function show_license_form() {
        $licence_key = SL_LICENSE;
        if (isset($_POST['licence_key'])) {
            if (intval($_POST['licence_status'])) {
                $res = self::call_api('deactivate', $licence_key);
                if ($res) {
                    update_option(SL_PRODUCT_ID.'_license_activated', 0);
                }
            }
            $licence_key = $_POST['licence_key'];
            if (SL_LICENSE != $licence_key || !intval($_POST['licence_status'])) {
                // aggiorno la chiave di licenza inserita
                update_option(SL_PRODUCT_ID.'_license_key', $licence_key);
                // provo ad attivare con la nuova chiave
                $res = self::call_api('activate', $licence_key, true);
                // mi salvo lo stato della licenza per non effettuare troppe chiamate al server
                if ($res) {
                    update_option(SL_PRODUCT_ID.'_license_activated', 1);
                    update_option(SL_PRODUCT_ID.'_license_domain', base64_encode(SL_INSTANCE));
                } else {
                    update_option(SL_PRODUCT_ID.'_license_activated', 0);
                    $licence_key = '';
                }
            }
        }
        
        if (isset($_POST['beta_status'])) {
            if (isset($_POST['dce_beta'])) {
                update_option('dce_beta', 1);
            } else {
                update_option('dce_beta', 0);
            }
        }
        $licence_check = isset($_GET['licence_check']) ? $_GET['licence_check'] : false;
        $licence_status = ($licence_key && self::call_api('status-check', $licence_key, $licence_check));
        
        $licence_key_hidden = '';
        $licence_pieces = explode('-', $licence_key);
        if (isset($licence_pieces[1]) && isset($licence_pieces[2])) {
            $licence_pieces[1] = $licence_pieces[2] = 'xxxxxxxx';
            $licence_key_hidden = implode('-', $licence_pieces);
        }
        
        $dce_domain = base64_decode(get_option(SL_PRODUCT_ID.'_license_domain'));
        //var_dump($dce_domain);
        $dce_activated = intval(get_option(SL_PRODUCT_ID.'_license_activated', 0));
        $classes = ($licence_status) ? 'dce-success dce-notice-success' : 'dce-error dce-notice-error';
        if ($dce_activated && $licence_status && $dce_domain && $dce_domain != SL_INSTANCE) { 
            $classes = 'dce-warning dce-notice-warning';
        }

        ?>
        <div class="dce-notice <?php echo $classes; ?>">
            <h2>LICENSE Status <a href="?<?php echo $_SERVER['QUERY_STRING']; ?>&licence_check=1"><span class="dashicons dashicons-info"></span></a></h2>
            <form action="" method="post">
                <?php _e('Your key', DCE_TEXTDOMAIN ); ?>: <input type="text" name="licence_key" value="<?php if ($dce_activated) { echo $licence_key_hidden; } ?>" id="licence_key" placeholder="dce-xxxxxxxx-xxxxxxxx-xxxxxxxx" style="width: 240px; max-width: 100%;">
                <input type="hidden" name="licence_status" value="<?php echo $licence_status; ?>" id="licence_status">
                <?php ($licence_status) ? submit_button('Deactivate', 'cancel') : submit_button('Save Key and Activate'); ?>
            </form>
        <?php if ($licence_status) {
            if ($dce_domain && $dce_domain != SL_INSTANCE) { ?>
                <p><strong style="color:#f0ad4e;"><?php _e('Your license is valid but there is something wrong: <b>License Mismatch</b>.', DCE_TEXTDOMAIN ); ?></strong></p>
                <p><?php _e('Your license key doesn\'t match your current domain. This is most likely due to a change in the domain URL. Please deactivate the license and then reactivate it again.', DCE_TEXTDOMAIN ); ?></p>
            <?php } else { ?>
                <p><strong style="color:#46b450;"><?php _e('Your license is valid and active.', DCE_TEXTDOMAIN ); ?></strong></p>
                <p><?php _e('Thank you for choosing to use our plugin.', DCE_TEXTDOMAIN ); ?><br><?php _e('Feel free to create your new dynamic and creative website.', DCE_TEXTDOMAIN ); ?><br><?php _e('If you think that our widgets are fantastic do not forget to recommend it to your friends.', DCE_TEXTDOMAIN ); ?></p>
            <?php }
            } else { ?>
                <p><?php _e('Enter your license here to keep the plugin updated, obtaining new widgets, future compatibility, more stability and security.', DCE_TEXTDOMAIN ); ?></p>
                <p><?php _e('Do not you have one yet? Get it right away:', DCE_TEXTDOMAIN ); ?> <a href="http://www.dynamic.ooo" class="button button-small" target="_blank"><?php _e('visit our official page', DCE_TEXTDOMAIN ); ?></a></p>
        <?php } ?>
        </div>

        <?php if ($licence_status) {
            $dce_beta = get_option('dce_beta'); ?>
            <div class="dce-notice dce-warning dce-notice-warning">
                <h3><?php _e('Beta release', DCE_TEXTDOMAIN ); ?></h3>
                <form action="" method="post">
                    <label><input type="checkbox" name="dce_beta" value="beta"<?php if ($dce_beta) { ?> checked="checked"<?php } ?>> <?php _e('Enable BETA releases (IMPORTANT: do NOT enable if you need a stable version).', DCE_TEXTDOMAIN ); ?></label>
                    <input type="hidden" name="beta_status" value="1" id="beta_status">
                    <?php submit_button('Save my preference'); ?>
                </form>
            </div>
        <?php
        }
    }
    
    public static function dce_plugin_action_links_license($links){
        $links['license'] = '<a style="color:brown;" title="Activate license" href="'.admin_url().'admin.php?page=dce_opt&tab=license"><b>'.__('License', DCE_TEXTDOMAIN).'</b></a>';
        return $links;
    }
    
    public static function dce_active_domain_check() {
        $dce_activated = intval(get_option(SL_PRODUCT_ID.'_license_activated', 0));
        $dce_domain = base64_decode(get_option(SL_PRODUCT_ID.'_license_domain'));
        if ($dce_activated && $dce_domain && $dce_domain != SL_INSTANCE) {
            DCE_Notice::dce_admin_notice__warning(__('<b>License Mismatch</b><br>Your license key doesn\'t match your current domain. This is most likely due to a change in the domain URL. Please deactivate the license and then reactivate it again. <a class="btn button" href="?page=dce_opt&tab=license">Reactivate License</a>', DCE_TEXTDOMAIN));
            return false;
        }
        return true;
    }
}


