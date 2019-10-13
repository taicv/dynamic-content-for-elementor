<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 *
 * Reveals - Opening effect
 *
 */

class DCE_Extension_Reveal extends DCE_Extension_Prototype {

    public $name = 'Reveal';

    /**
     * Reveal: magic for parallax
     *
     * @since 1.0.6
     * */
    public static function get_description() {
        return __('Reveal rules for Widgets');
    }

    public function get_script_depends() {
        //return ['dce-revealjs-lib'];
	return ['dce-anime-lib','dce-revealFx'];
    }

    private function add_controls($element, $args) {

        $element_type = $element->get_type();

        $element->add_control(
            'enabled_reveal', [
                'label' => __('Enabled Reveal', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
            //            
            ]
        );
        $element->add_control(
            'reveal_direction',
            [
                'label' => __( 'Direction', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'c',
                'options' => [
                    'c' => __( 'Center', DCE_TEXTDOMAIN ),
                    'lr' => __( 'Left to Right', DCE_TEXTDOMAIN ),
                    'rl' => __( 'Right to Left', DCE_TEXTDOMAIN ),
                    'tb' => __( 'Top to Bottom', DCE_TEXTDOMAIN ),
                    'bt' => __( 'Bottom to top', DCE_TEXTDOMAIN ),
                ],
                'frontend_available' => true,
                'condition' => [
                    'enabled_reveal' => 'yes'
                ]
            ]
        );
        $element->add_control(
            'reveal_speed', [
                'label' => __('Speed', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1
                    ],
                ],
                'default' => [
                    'size' => 5,
                    
                ],
                'frontend_available' => true,
                'condition' => [
                    'enabled_reveal' => 'yes'
                ]
            ]
        );
        $element->add_control(
            'reveal_delay', [
                'label' => __('Delay', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 0.1
                    ],
                ],
                'default' => [
                    'size' => 0,
                    
                ],
                'frontend_available' => true,
                'condition' => [
                    'enabled_reveal' => 'yes'
                ]
            ]
        );
        $element->add_control(
            'reveal_bgcolor', [
                'label' => __('Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'frontend_available' => true,
                'default' => '#ccc',
                /*'selectors' => [
                    '{{WRAPPER}} .cd-modal-action .btn' => 'color: {{VALUE}};',
                ],*/
                'condition' => [
                    'enabled_reveal' => 'yes'
                ]
            ]
        );
        
        
    }

    protected function add_actions() {

        // Activate controls for widgets
        add_action('elementor/element/common/dce_section_reveal_advanced/before_section_end', function( $element, $args ) {
            $this->add_controls($element, $args);
        }, 10, 2);

        add_filter('elementor/widget/print_template', array($this, 'reveal_print_template'), 9, 2);

        add_action('elementor/widget/render_content', array($this, 'reveal_render_template'), 9, 2);

    }

    public function reveal_print_template($content, $widget) {
        if (!$content)
            return '';

        $id_item = $widget->get_id();

        $content = "<# if ( '' !== settings.enabled_reveal ) { #><div id=\"reveal-{{id}}\" class=\"reveal\">" . $content . "</div><# } else { #>" . $content . "<# } #>";
        return $content;
    }

    public function reveal_render_template($content, $widget) {
        $settings = $widget->get_settings_for_display();
        //return var_export($widget, true);
        //echo $widget['ID'];
        if ($settings['enabled_reveal']) {

            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                
            }
            $id_item = $widget->get_id();
            //echo 'reveal is enable';
            $content = '<div id="reveal-' . $id_item . '" class="revealFx">' . $content . '</div>';
        }
        return $content; // mostro il widget
    }

}
