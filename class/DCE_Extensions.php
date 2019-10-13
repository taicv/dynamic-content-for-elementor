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
            if ($class != 'DCE_Extension_Prototype') {
                $tmpExtensions[strtolower($class)] = $class;
            }
        }
        return $tmpExtensions;
    }
    
    static public function get_active_extensions() {
        $tmpExtensions = self::get_extensions();
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
    }
    
    public static function includes() {
        require_once( self::$dir . '/DCE_Extension_Prototype.php' ); // obbligatorio in quanto esteso dagli altri
        foreach (self::get_extensions() as $key => $value) {
            require_once self::$dir.'/'.$value.'.php';
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
        
        $excluded_extensions = json_decode(get_option(SL_PRODUCT_ID . '_excluded_extensions', '[]'), true);
        //var_dump($excluded_extensions);
        //var_dump($excluded_widgets);
        foreach ($this->extensions as $key => $name) {
            
            if (!isset($excluded_extensions[$name])) { // controllo se lo avevo escluso in quanto non interessante
                $class = self::$namespace . $name;
                //var_dump($aWidgetObjname);
                if ($class::is_enabled()) {
                    //echo $class;
                    $extensions[] = new $class();
                }
            }
            
        }
        
        //var_dump($extensions); die();
    }

}
