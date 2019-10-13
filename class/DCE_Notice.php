<?php
namespace DynamicContentForElementor;

/**
 * DCE Notice Class
 *
 * @since 0.1.0
 */
class DCE_Notice {
    
    /**
     * Constructor
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function __construct() {
        
    }

    public static function dce_admin_notice__license() { 
        if (did_action('elementor/loaded')) { ?>
        <div class="error notice-error notice dce-generic-notice">
            <div class="img-responsive pull-left" style="float: left; margin-right: 20px;"><img src="<?php echo DCE_URL; ?>/assets/media/dce.png" title="Dynamic Content for Elementor" height="65" width="65"></div>
            <p><strong><?php  _e( 'Welcome to Dynamic Content for Elementor', DCE_TEXTDOMAIN ); ?></strong></p>
            <p><?php _e( 'Your copy seems to be not activated, please <a href="'.admin_url().'admin.php?page=dce_opt&tab=license">activate</a> or <a href="https://shop.dynamic.ooo" target="blank">buy a new license code</a>.', DCE_TEXTDOMAIN ); ?></p>
        </div>
    <?php }
    }
    
    public static function dce_admin_notice__server_error($msg = '') { ?>
        <div class="error notice-error notice dce-generic-notice is-dismissible">
            <p><strong>Dynamic Content for Elementor:</strong> 
            <?php if ($msg) { echo $msg; } else { _e( 'There was a problem establishing a connection to the API server', DCE_TEXTDOMAIN ); } ?></p>
        </div>
    <?php }
    
    public static function dce_admin_notice__success($msg = '') { ?>
        <div class="success notice-success notice dce-generic-notice is-dismissible updated">
            <p><strong>Dynamic Content for Elementor:</strong> 
            <?php if ($msg) { echo $msg; } else { _e(get_option('dce_notice'), DCE_TEXTDOMAIN ); } ?></p>
        </div>
    <?php }
    
    public static function dce_admin_notice__warning($msg = '') { ?>
        <div class="warning notice-warning notice dce-generic-notice is-dismissible update-nag">
            <p><strong>Dynamic Content for Elementor:</strong> 
            <?php if ($msg) { echo $msg; } else { _e(get_option('dce_notice'), DCE_TEXTDOMAIN ); } ?></p>
        </div>
    <?php }
    
}