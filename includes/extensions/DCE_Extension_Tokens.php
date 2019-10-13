<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 *
 * Animations Effects
 *
 */

class DCE_Extension_Tokens extends DCE_Extension_Prototype {
    
    public $name = 'Tokens';
    
    static public function is_enabled() {
        return false;
    }

    protected $is_common = true;

    public static function get_description() {
        return __('Add support for Tokens in every Text and Textarea.');
    }

    private function add_controls($element, $args) {

        $element_type = $element->get_type();
        //echo $element->get_ID();
        $controls = $element->get_controls();
        //var_dump($element->get_name());
        if ($element->get_name() == 'heading') {
            //var_dump($controls);
        }
        
        /*
        $element->add_control(
            'dynamic', [
                'label' => $ckey.' - '.$element->get_name().' - '.$element->get_ID(),
                'type' => Controls_Manager::HIDDEN,
                'default' => array('active' => true),
            ]
        );
        $element->add_control(
            'asd', [
                'label' => 'asd',
                'type' => Controls_Manager::HIDDEN,
                'default' => 'lol',
            ]
        );
        */
        
        $dynamic_tags = array();
        if (!empty($controls)) {
            foreach ($controls as $ckey => $control) {
                //if ($control['type'] == 'text' || $control['type'] == 'textarea') {
                    /*$element->add_control(
                        \Elementor\Core\DynamicTags\Manager::DYNAMIC_SETTING_KEY.'_'.$ckey, [
                            'label' => $ckey.' - '.$element->get_name().' - '.$element->get_ID(),
                            'type' => Controls_Manager::TEXT,
                            'default' => '[dce value="token"]',
                        ]
                    );*/
                    //var_dump($control['type']);
                    $dynamic_tags[$ckey] = '[dce value="token"]';
                //}
            }
        }
        if (!empty($dynamic_tags)) {
            //var_dump($dynamic_tags);
            //$element->set_settings[\Elementor\Core\DynamicTags\Manager::DYNAMIC_SETTING_KEY] = $dynamic_tags;
        }
        
    }

    protected function add_actions() {

        // Activate controls for widgets
        add_action('elementor/element/common/dce_section_tokens_advanced/before_section_end', function( $element, $args ) {
            $this->add_controls($element, $args);
        }, 10, 2);
        
    }

}
