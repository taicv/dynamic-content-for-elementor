<?php

namespace DynamicContentForElementor;

use Elementor\Controls_Manager;

/**
 * Widgets Class
 *
 * Register new elementor widget.
 *
 * @since 0.0.1
 */
class DCE_Extensions {

    public $extensions = [];
    static public $dir = DCE_PATH . 'includes/extensions';
    static public $namespace = '\\DynamicContentForElementor\\Extensions\\';

    public function __construct() {
        $this->init();
    }

    public function init() {
        $this->extensions = self::get_extensions();
    }
    
    public static function get_extensions() {
        $tmpExtensions = [];
        $extensions = glob(self::$dir. '/DCE_*.php');
        foreach ($extensions as $key => $value) {
            $class = pathinfo($value, PATHINFO_FILENAME);
            if ($class == 'DCE_Extension_Prototype') {
                continue;
            }
            $tmpExtensions[strtolower($class)] = $class;
        }
        return $tmpExtensions;
    }

    public static function get_form_extensions() {
        $tmpExtensions = [];
        $extensions = glob(self::$dir. '/form/DCE_*.php');
        foreach ($extensions as $key => $value) {
            $class = pathinfo($value, PATHINFO_FILENAME);
            $tmpExtensions[strtolower($class)] = $class;
        }
        return $tmpExtensions;
    }
    
    static public function get_active_extensions() {
        $tmpExtensions = self::get_extensions() + self::get_form_extensions();
        self::includes();
        $activeExtensions = array();
        foreach ($tmpExtensions as $key => $name) {
            $class = self::$namespace . $name;
            if ($class::is_enabled()) {
                $activeExtensions[$key] = $name;
            }
        }
        return $activeExtensions;
    }
    
    
    /**
    * On extensions Registered
    *
    * @since 0.0.1
    *
    * @access public
    */
    public function on_extensions_registered() {
        $this->includes();
        $this->register_extensions();
        $this->register_form_extensions();
    }
    
    public static function includes() {
        require_once( self::$dir . '/DCE_Extension_Prototype.php' ); // obbligatorio in quanto esteso dagli altri
        foreach (self::get_extensions() as $key => $value) {
            require_once self::$dir.'/'.$value.'.php';
        }
        foreach (self::get_form_extensions() as $key => $value) {
            require_once self::$dir.'/form/'.$value.'.php';
        }
    }
    
    /**
    * On Controls Registered
    *
    * @since 1.0.4
    *
    * @access public
    */
    public function register_extensions() {
        $extensions = [];
        
        $excluded_extensions = self::get_excluded_extensions();
        //var_dump($excluded_extensions);
        //var_dump($excluded_widgets);
        foreach ($this->extensions as $key => $name) {
            
            if (!isset($excluded_extensions[$name])) { // controllo se lo avevo escluso in quanto non interessante
                $class = self::$namespace . $name;
                //var_dump($aWidgetObjname);
                if ($class::is_enabled() && $class::get_satisfy_dependencies()) {
                    //echo $class;
                    $extensions[] = new $class();
                }
            }
            
        }
        
        //var_dump($extensions); die();
    }
    
    /**
    * On Controls Registered
    *
    * @since 1.0.4
    *
    * @access public
    */
    public function register_form_extensions() {
        $extensions = [];
        
        if (DCE_Helper::is_plugin_active('elementor-pro')) {
            add_action( 'elementor_pro/init', function() {
                $excluded_extensions = DCE_Extensions::get_excluded_extensions();
                
                $form_extensions = DCE_Extensions::get_form_extensions();
                foreach($form_extensions as $akey => $a_form_ext) {
                    $exc_ext = !isset($excluded_extensions[$a_form_ext]);
                    //var_dump($token_ext);
                    $a_form_ext_class = DCE_Extensions::$namespace.$a_form_ext;
                    if ($exc_ext) {
                        // Instantiate the action class
                        $extensions[$akey] = new $a_form_ext_class();
                        
                        if ($a_form_ext == 'DCE_Extension_Form_Visibility') {
                            continue;
                        }
                        // Register the action with form widget
                        \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $extensions[$akey]->get_name(), $extensions[$akey] );
                    
                        // Add specific Template Type
                        if ($a_form_ext == 'DCE_Extension_Form_Email') {
                            // Add Email Template Type
                            include_once( DCE_PATH .'modules/theme-builder/documents/DCE_Email.php' );
                            $dce_email = '\ElementorPro\Modules\ThemeBuilder\Documents\DCE_Email';
                            \Elementor\Plugin::instance()->documents->register_document_type( $dce_email::get_name_static(), \ElementorPro\Modules\ThemeBuilder\Documents\DCE_Email::get_class_full_name() );
                            \Elementor\TemplateLibrary\Source_Local::add_template_type( \ElementorPro\Modules\ThemeBuilder\Documents\DCE_Email::get_name_static() );
                            add_filter( 'elementor_pro/editor/localize_settings', '\ElementorPro\Modules\ThemeBuilder\Documents\DCE_Email::dce_add_more_types' );
                        }
                    }
                }
            });
        }
        
        //var_dump($extensions); die();
    }
    
    public static function get_excluded_extensions() {
        return json_decode(get_option(SL_PRODUCT_ID . '_excluded_extensions', '[]'), true);
    }

}
