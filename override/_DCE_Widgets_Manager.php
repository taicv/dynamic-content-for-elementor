<?php
namespace Elementor;
 
use \Elementor\Widgets_Manager;

require_once ELEMENTOR_PATH . 'includes/base/element-base.php';

/**
 * Description of DCE_Widgets_Manager
 *
 * @author fra
 */
class DCE_Widgets_Manager extends Widgets_Manager {
    /**
    * Require files.
    *
    * Require Elementor widget base class.
    *
    * @since 2.0.0
    * @access private
   */
    
   private function require_files() {
       if (!class_exists('Elementor\\Widget_Base')) {
           require_once ELEMENTOR_PATH . 'includes/base/widget-base.php';
       }
   }
}

