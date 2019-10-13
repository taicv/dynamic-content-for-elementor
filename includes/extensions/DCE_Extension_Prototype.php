<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Extension
 *
 * Class to easify extend Elementor controls and functionality
 *
 */

class DCE_Extension_Prototype {

    public $name = 'Extension';
    
    public static $docs = 'https://www.dynamic.ooo';
    
    static public function is_enabled() {
        return true;
    }

    private $is_common = true;

    private $depended_scripts = [];

    private $depended_styles = [];
    public static $depended_plugins = [];
    
    public $common_sections_actions = array(
        array(
            'element' => 'common',
            'action' => '_section_style',
        )
    );

    public function __construct() {

        $this->init();
    }

    public function init($param = null) {
        // Enqueue scripts
        add_action('elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts']);

        // Enqueue styles
        add_action('elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles']);

        // Elementor hooks

        if ($this->is_common) {
            // Add the advanced section required to display controls
            $this->add_common_sections_actions();
        }

        $this->add_actions();
    }
    
    public function get_docs() {
        return self::$docs;
    }
    
    static public function get_satisfy_dependencies($ret = false) {
        $widgetClass = get_called_class();
        //require_once( __DIR__ . '/'.$widgetClass.'.php' );
        //$myWdgt = new $widgetClass();
        return $widgetClass::satisfy_dependencies($ret);
    }
    
    public static function get_plugin_depends() {
        return self::$depended_plugins;
    }
    
    public static function satisfy_dependencies($ret = false, $deps = array()) {
        if (empty($deps)) {
            $deps = self::get_plugin_depends();
        }
        $depsDisabled = array();
        if (!empty($deps)) {
            $isActive = true;
            foreach ($deps as $pkey => $plugin) {
                if (!is_numeric($pkey)) {
                    if (!DCE_Helper::is_plugin_active($pkey)) {
                        $isActive = false;
                    }
                } else {
                    if (!DCE_Helper::is_plugin_active($plugin)) {
                        $isActive = false;
                    }
                }
                if (!$isActive) {
                    if (!$ret) {
                        return false;
                    }
                    $depsDisabled[] = $pkey;
                }
            }
        }
        if ($ret) {
            return $depsDisabled;
        }
        return true;
    }

    public function add_script_depends($handler) {
        $this->depended_scripts[] = $handler;
    }

    public function add_style_depends($handler) {
        $this->depended_styles[] = $handler;
    }

    public function get_script_depends() {
        return $this->depended_scripts;
    }

    final public function enqueue_scripts() {
        foreach ($this->get_script_depends() as $script) {
            wp_enqueue_script($script);
        }
    }

    final public function get_style_depends() {
        return $this->depended_styles;
    }

    public static function get_description() {
        return '';
    }

    final public function enqueue_styles() {
        foreach ($this->get_style_depends() as $style) {
            wp_enqueue_style($style);
        }
    }

    public final function add_common_sections($element, $args) {
        $low_name = strtolower($this->name);
        $section_name = 'dce_section_' . $low_name . '_advanced';

        if ($low_name == 'tokens' || substr($low_name,0,4) == 'form') {
            // no need settings
            return false;
        }
        
        // Check if this section exists
        $section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack($element->get_unique_name(), $section_name);

        if (!is_wp_error($section_exists)) {
            // We can't and should try to add this section to the stack
            return false;
        }
        
        if ($low_name == 'visibility') {
            
            \Elementor\Controls_Manager::add_tab(
                    'dce_'.$low_name,
                    __( $this->name, 'dynamic-content-for-elementor' )
            );
            
            $element->start_controls_section(
                $section_name, [
                    'tab' => 'dce_'.$low_name,
                    'label' => '<span class="color-dce icon icon-dyn-logo-dce pull-right ml-1"></span> '.__($this->name, 'dynamic-content-for-elementor'),
                ]
            );
            $element->end_controls_section();
            
            foreach (DCE_Extension_Visibility::$tabs as $tkey => $tlabel) {
                $section_name = 'dce_section_'.$low_name.'_'.$tkey;
                
                $condition = [
                                'enabled_'.$low_name.'!' => '',
                                'dce_'.$low_name.'_hidden' => '',
                                'dce_'.$low_name.'_mode' => 'quick',
                            ];
                if ($tkey == 'fallback') {
                    $condition = ['enabled_'.$low_name.'!' => ''];
                }
                if ($tkey == 'repeater') {
                    $condition = [
                                'enabled_'.$low_name.'!' => '',
                                'dce_'.$low_name.'_hidden' => '',
                                'dce_'.$low_name.'_mode' => 'advanced',
                            ];
                }
                
                $icon = '';
                switch ($tkey) {
                    case 'user':
                        $icon = 'user-o';
                        break;
                    case 'datetime':
                        $icon = 'calendar';
                        break;
                    case 'device':
                        $icon = 'mobile';
                        break;
                    case 'post':
                        $icon = 'file-text-o';
                        break;
                    case 'context':
                        $icon = 'crosshairs';
                        break;
                    case 'tags':
                        $icon = 'question-circle-o';
                        break;
                    case 'random':
                        $icon = 'random';
                        break;
                    case 'custom':
                        $icon = 'code';
                        break;
                    case 'events':
                        $icon = 'hand-pointer-o';
                        break;
                    case 'fallback':
                        $icon = 'life-ring';
                        break;
                    case 'advanced':
                        $icon = 'cogs';
                        break;
                    default:
                        $icon = 'cog';
                }
                if ($icon) {
                    $icon = '<i class="fa fa-'.$icon.' pull-right ml-1" aria-hidden="true"></i>';
                }
                
                $element->start_controls_section(
                    $section_name, [
                        'tab' => 'dce_'.$low_name,
                        'label' => $icon.__($tlabel, 'dynamic-content-for-elementor'),
                        'condition' => $condition,
                    ]
                );
                $element->end_controls_section();
            }
            
        } else {
            $element->start_controls_section(
                $section_name, [
                    'tab' => Controls_Manager::TAB_ADVANCED,
                    'label' => __($this->name, 'dynamic-content-for-elementor'),
                ]
            );
            $element->end_controls_section();
        }
    }

    public function add_common_sections_actions() {

        //_section_style
        //section_advanced
        //section_custom_css
        //section_custom_css_pro

        foreach ($this->common_sections_actions as $action) {

            // Activate action for elements
            add_action('elementor/element/' . $action['element'] . '/' . $action['action'] . '/after_section_end', function( $element, $args ) {
                $this->add_common_sections($element, $args);
            }, 10, 2);
        }
    }

    protected function add_actions() {
        
    }

    protected function remove_controls($element, $controls = null) {
        if (empty($controls))
            return;

        if (is_array($controls)) {
            $control_id = $controls;

            foreach ($controls as $control_id) {
                $element->remove_control($control_id);
            }
        } else {
            $element->remove_control($controls);
        }
    }
    
}
