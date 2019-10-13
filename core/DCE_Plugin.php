<?php

namespace DynamicContentForElementor;

use Elementor\Core\Settings\Manager as SettingsManager;

use DynamicContentForElementor\Documents\PageSettings_Scrollify;
use DynamicContentForElementor\Documents\PageSettings_InertiaScroll;
use DynamicContentForElementor\DCE_Helper;

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 0.0.1
 */
class DCE_Plugin {

    static public $namespace = '\\DynamicContentForElementor\\';
    public $dce_classes = [];
    public $dce_overrides = [];

    /**
     * Constructor
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function __construct() {
        $this->init();
    }

    public function init() {
        
        // Include extensions
        $this->includes();
        
        //add_action('admin_init', 'dynContEl_updater');
        add_action('admin_menu', array($this, 'add_dce_menu'), 200);

        // Global Settings Panel
        // $this->register_settings_managers();

        // fire actions
        add_action('elementor/init', array($this, 'add_dce_to_elementor'));
        
        // Add a custom category for panel widgets
        add_action( 'elementor/init', function() {
            /*
            // DYNAMIC TAGS with TOKENS support
            \Elementor\Plugin::$instance->dynamic_tags = new \Elementor\DCE_Dynamic_Tags_Manager();
            \Elementor\Plugin::$instance->controls_manager = new \Elementor\DCE_Controls_Manager();
            */
            //\Elementor\Plugin::$instance->widgets_manager = new DCE_Widgets_Manager();
        } );
        
        /*add_action( 'elementor/editor/after_save', function( $post_id, $editor_data = array() ) {
            //var_dump($post_id); die();
            $editor_data = DCE_Helper::get_settings_by_id(null, $post_id);
            //var_dump($editor_data); die();
            $editor_data = DCE_Helper::set_dynamic_tag($editor_data);
            DCE_Helper::set_all_settings_by_id(null, $editor_data, $post_id);
        } );*/

        /*add_filter( 'wp_die_handler', function( $message, $title, $args ){
            echo 'lol';
            return false;
        });*/
        
        add_filter( 'plugin_action_links_' . DCE_PLUGIN_BASE,  '\DynamicContentForElementor\DCE_Plugin::dce_plugin_action_links_config' );
        
        // add token to 
        add_filter('widget_text', [$this, 'add_dce_to_widget']);
    }
    
    /**
     * Includes
     *
     * @since 0.5.0
     *
     * @access private
     */
    private function includes() {
        $classes = glob(DCE_PATH . '/class/DCE_*.php');
        // include all classes
        foreach ($classes as $key => $value) {
            require_once $value;
        }
        // instance all classes
        foreach ($classes as $key => $value) {
            $name = pathinfo($value, PATHINFO_FILENAME);
            $class = self::$namespace . $name;
            $this->dce_classes[strtolower($name)] = new $class();
        }
        
        // activate Token Shorcode
        $this->dce_classes['dce_tokens']->add_shortcode();
        
        
        $overrides = glob(DCE_PATH . '/override/DCE_*.php');
        // include all classes
        foreach ($overrides as $key => $value) {
            require_once $value;
        }
        // instance all classes
        foreach ($overrides as $key => $value) {
            $name = pathinfo($value, PATHINFO_FILENAME);
            $class = '\\Elementor\\' . $name;
            $this->dce_overrides[strtolower($name)] = new $class();
        }
    }
    /**
     * Add Manager for Global Settings
     *
     * @since 1.3.0
     *
     * @access private
     */
    private function register_settings_managers() {
        include_once( DCE_PATH . 'includes/settings/DCE_Settings_DCE.php' );
        include_once( DCE_PATH . 'includes/settings/manager.php' );
        
        //var_dump(\Elementor\Plugin::instance());
        
        SettingsManager::add_settings_manager( new \DynamicContentForElementor\Includes\Settings\Manager );
    }
    /**
     * Add Actions
     *
     * @since 0.0.1
     *
     * @access private
     */
    public function add_dce_to_elementor() {

        // Controls
        add_action('elementor/controls/controls_registered', [ $this->dce_classes['dce_controls'], 'on_controls_registered']);

        // Extensions
        $this->dce_classes['dce_extensions']->on_extensions_registered();
        //add_action('elementor/init', [ $this->dce_extensions, 'on_extensions_registered'] );
        
        // Documents
        $this->dce_classes['dce_documents']->on_documents_registered();

        // Widgets
        add_action('elementor/widgets/widgets_registered', [ $this->dce_classes['dce_widgets'], 'on_widgets_registered']);

    }

    public function add_dce_menu() {
        
        // TemplateSystem sotto Template
        if(defined('\Elementor\TemplateLibrary\Source_Local::ADMIN_MENU_SLUG') && current_user_can('administrator')) {
            add_submenu_page( 
                \Elementor\TemplateLibrary\Source_Local::ADMIN_MENU_SLUG, 
                __('Dynamic Template System', DCE_TEXTDOMAIN), 
                __('Template System', DCE_TEXTDOMAIN), 
                'publish_posts', 
                'dce_templatesystem', 
                array(
                    $this->dce_classes['dce_settings'], 
                    'dce_setting_templatesystem' 
                )        
            );
        }
        
        // Dynamic Content sotto Elementor
        add_submenu_page(
            \Elementor\Settings::PAGE_ID, 
            __('Dynamic Content Settings', DCE_TEXTDOMAIN), 
            __('Dynamic Content', DCE_TEXTDOMAIN), 
            'manage_options', 
            'dce_opt',
            [
                $this->dce_classes['dce_settings'], 
                'dce_setting_page'
            ]
        );
        
        // La pagina Informazioni che appare alla prima attivazione del plugin.
        add_submenu_page(
                'admin.php', __('Dynamic Content for Elementor', DCE_TEXTDOMAIN), __('Dynamic Content for Elementor', DCE_TEXTDOMAIN), 'manage_options', 'dce_info', array(
            $this->dce_classes['dce_info'],
            'dce_information_plugin'
            )
        );
    }
    
    public function add_dce_to_widget($text) {
        $new_content = DCE_Tokens::do_tokens($text);
        //$new_content .= '<br>:)';
        return $new_content;
    }   
    
    public static function dce_plugin_action_links_config($links){
        $links['config'] = '<a title="Configuration" href="'.admin_url().'admin.php?page=dce_opt">'.__('Configuration', DCE_TEXTDOMAIN).'</a>';
        return $links;
    }

}
