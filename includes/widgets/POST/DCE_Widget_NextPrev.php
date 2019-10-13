<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;

use DynamicContentForElementor\DCE_Helper;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Elelentor Post NextPrev
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */
class DCE_Widget_NextPrev extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-post-nextprev';
    }
    static public function is_enabled() {
        return true;
    }
    public function get_title() {
        return __('Prev Next', DCE_TEXTDOMAIN);
    }
    public function get_description() {
        return __('Access pages adjacent the selected post based on a category/taxonomy or tag', DCE_TEXTDOMAIN);
    }
    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/prevnext/';
    }
    public function get_icon() {
        return 'icon-dyn-prevnext';
    }
     public function get_script_depends() {
        return []; //['ajaxify','dce-nextPrev'];
    }
    static public function get_position() {
        return 4;
    }
    /*public function get_style_depends() {
        return [ 'dce-nextPrev' ];
    }*/
    protected function _register_controls() {

        $post_type_object = get_post_type_object(get_post_type());

        $this->start_controls_section(
            'section_content', [
                'label' => __('PrevNext', DCE_TEXTDOMAIN)
            ]
        );
        $this->add_control(
            'show_title', [
                'label' => __('Show Title', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show', DCE_TEXTDOMAIN),
                'label_off' => __('Hide', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'show_prevnext', [
                'label' => __('Show PrevNext Text', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show', DCE_TEXTDOMAIN),
                'label_off' => __('Hide', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'icon_right', [
                'label' => __('Icon Right', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::ICON,
                'default' => 'fa fa-long-arrow-right',
                'include' => [
                    'fa fa-arrow-right',
                    'fa fa-angle-right',
                    'fa fa-chevron-circle-right',
                    'fa fa-caret-square-o-right',
                    'fa fa-chevron-right',
                    'fa fa-caret-right',
                    'fa fa-angle-double-right',
                    'fa fa-hand-o-right',
                    'fa fa-arrow-circle-right',
                    'fa fa-long-arrow-right',
                    'fa fa-arrow-circle-o-right',
                ],
            ]
        );
        $this->add_control(
            'icon_left', [
                'label' => __('Icon Left', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::ICON,
                'default' => 'fa fa-long-arrow-left',
                'include' => [
                    'fa fa-arrow-left',
                    'fa fa-angle-left',
                    'fa fa-chevron-circle-left',
                    'fa fa-caret-square-o-left',
                    'fa fa-chevron-left',
                    'fa fa-caret-left',
                    'fa fa-angle-double-left',
                    'fa fa-hand-o-left',
                    'fa fa-arrow-circle-left',
                    'fa fa-long-arrow-left',
                    'fa fa-arrow-circle-o-left',
                ],
            ]
        );
        $this->add_control(
            'prev_label',
            [
                'label' => __( 'Previous Label', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Previous', DCE_TEXTDOMAIN ),
                'condition' => [
                    'show_prevnext' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'next_label',
            [
                'label' => __( 'Next Label', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Next', DCE_TEXTDOMAIN ),
                'condition' => [
                    'show_prevnext' => 'yes',
                ],
            ]
        );
         $this->add_control(
            'same_term', [
                'label' => __('Same term', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('Yes', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __('No', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-ban',
                    ]
                ],
                'default' => '0'
            ]
        );
        $this->add_control(
            'taxonomy_type', [
                'label' => __('Taxonomy Type', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'options' => DCE_Helper::get_taxonomies(),
                'default' => '',
                'description' => ' if "Same term" is true.',
                'condition' => [
                    'same_term' => '1',
                ]
            ]
        );
        $this->add_control(
            'invert_prevnext', [
                'label' => __('Invert order', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show', DCE_TEXTDOMAIN),
                'label_off' => __('Hide', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
            ]
        );
         $this->add_control(
            'Navigation_heading',
            [
                'label' => __( 'Space', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );
        $this->add_control(
            'navigation_space', [
                'label' => __('Navigation Space', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} nav.post-navigation' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'fluttua' => '',
                ]
            ]
        );
        $this->add_control(
            'space', [
                'label' => __('Navigation Padding', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%'],

                'selectors' => [
                    '{{WRAPPER}} .nav-links a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'custom_width', [
                'label' => __('Custom Width', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('Custom Width', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-arrows-h',
                    ],
                    '0' => [
                        'title' => __('No', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-ban',
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .nav-links > div' => 'width: auto;',
                ],
                'default' => '1'
            ]
        );
        $this->add_control(
            'width', [
                'label' => __('Width', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px'],
                'default' => [
                    'size' => 50,
                    'unit' => '%'
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 300,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .nav-links > div' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'custom_width' => '1',
                ]
            ]
        );
        /* $this->add_control(
          'width',
          [
          'label' => __( 'Largezza elementi', DCE_TEXTDOMAIN ),
          'type' => Controls_Manager::SLIDER,
          'default' => [
          'size' => 5,
          ],
          'range' => [
          'px' => [
          'min' => 0,
          'max' => 100,
          ],
          ],
          'selectors' => [
          '{{WRAPPER}} .nav-links > div' => 'padding: {{SIZE}}{{UNIT}};',
          ],
          ]
          ); */
        $this->end_controls_section();


        $this->start_controls_section(
            'section_position', [
                'label' => 'Position',
            ]
        );
        $this->add_control(
            'fluttua', [
                'label' => __('Floating', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Show', DCE_TEXTDOMAIN),
                'label_off' => __('Hide', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'prefix_class' => 'float',
            ]
        );
        $this->add_control(
            'verticale', [
                'label' => __('Vertical', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Show', DCE_TEXTDOMAIN),
                'label_off' => __('Hide', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'prefix_class' => 'vertical',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_icons', [
                'label' => 'Icons',
            ]
        );
        $this->add_responsive_control(
            'icon_size', [
                'label' => __('Size', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 80,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .nav-links span .fa' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_space', [
                'label' => __('Space', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} nav.post-navigation .nav-next .fa' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} nav.post-navigation .nav-previous .fa' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_verticalalign', [
                'label' => __('Shift', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 3,
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .nav-links .fa' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style', [
                'label' => 'NextPrev',
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'color_1', [
                'label' => __('Color Navigation', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nav-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} a .nav-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'color_2', [
                'label' => __('Post title Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nav-post-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} a .nav-post-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'color_3', [
                'label' => __('Post icon Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nav-title i.fa' => 'color: {{VALUE}};',
                    '{{WRAPPER}} a .nav-title i.fa' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'color_bg', [
                'label' => __('Background Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} nav.post-navigation .nav-next,
                        {{WRAPPER}} nav.post-navigation .nav-previous' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'typography_1',
                'label' => 'Typography prev/next',
                'selector' => '{{WRAPPER}} .nav-title',
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'typography_2',
                'label' => 'Typography post title',
                'selector' => '{{WRAPPER}} .nav-post-title',
            ]
        );
        $this->add_control(
            'rollhover_heading',
            [
                'label' => __( 'Rollover', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );
        $this->add_control(
            'hover_color', [
                'label' => __('Hover Text Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:hover span' => 'color: {{VALUE}};',
                ]
            ]
        );
        $this->add_control(
            'hover_color_title', [
                'label' => __('Hover title Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:hover .nav-post-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'hover_color_icon', [
                'label' => __('Hover icon Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:hover .nav-title i.fa' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'hover_animation', [
                'label' => __('Hover Animation', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );
       
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_active_settings();
        if ( empty( $settings ) )
            return;
        //
        // ------------------------------------------
        $dce_data = DCE_Helper::dce_dynamic_data();
        $id_page = $dce_data['id'];
        $global_is = $dce_data['is'];
        $type_page = $dce_data['type'];
        // ------------------------------------------


        //$settings['link_to']
        $taxonomy_type = $settings['taxonomy_type'];

        //$this->plugin_url         = plugin_dir_url( __FILE__ );
        //$this->plugin_path        = plugin_dir_path( __FILE__ );

        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        $same_term = $settings['same_term'];
        //$html = sprintf( '<div class="dce-nextprev %1$s">', $animation_class );
        //echo 'navigazione '.$taxonomy_type.'  '.get_the_ID();
        $title_nav = '';
        $prev_nav_tx = '';
        $next_nav_tx = '';
        if ($settings['show_title'] == 'yes') {
            $title_nav = '<span class="nav-post-title">%title</span>';
        }
        if ($settings['show_prevnext'] == 'yes') {
            if($settings['prev_label'] != ''){
                $prev_nav_tx = __($settings['prev_label'], DCE_TEXTDOMAIN.'_texts');
            }else{
                $prev_nav_tx = esc_html__('Previous', DCE_TEXTDOMAIN);
            }
            if($settings['next_label'] != ''){
                $next_nav_tx = __($settings['next_label'], DCE_TEXTDOMAIN.'_texts');
            }else{
                $next_nav_tx = esc_html__('Next', DCE_TEXTDOMAIN);
            }
            
           $prev_nav_tx = '<span class="nav-post-label">'.$prev_nav_tx.'</span>';
           $next_nav_tx = '<span class="nav-post-label">'.$next_nav_tx.'</span>';
        }
        $icon_right = $settings['icon_right'];
        $icon_left = $settings['icon_left'];

        if ($taxonomy_type != '') {
            $html = the_post_navigation(array(
                'prev_text' => '<span class="nav-title"><i class="' . $icon_left . '"></i><span>' . $prev_nav_tx . $title_nav.'</span>',
                'next_text' => '<span class="nav-title"><i class="' . $icon_right . '"></i><span>' . $next_nav_tx . $title_nav.'</span>',
                'in_same_term' => $same_term,
                //'excluded_terms'  => array('18'),
                'taxonomy' => $taxonomy_type,
                'screen_reader_text' => '', //esc_html__('Continue Reading', 'oceanwp'),
                    ));
        } else {
            the_post_navigation(array(
                'prev_text' => '<span class="nav-title"><i class="' . $icon_left . '"></i><span>' . $prev_nav_tx . $title_nav.'</span>',
                'next_text' => '<span class="nav-title"><i class="' . $icon_right . '"></i><span>' . $next_nav_tx . $title_nav.'</span>',
                //'in_same_term' => $same_term,
                //'excluded_terms'  => array('18'),
                //'taxonomy'              => $taxonomy_type,
                'screen_reader_text' => '', //esc_html__('Continue Reading', 'oceanwp'),
            ));
            // -------------------------------------------------------------------------------------------
        }

        //$html .= '</div>';
        //echo $html;
        ?>

        <?php

    }

}
