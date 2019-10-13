<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Controls\DCE_Group_Control_Ajax_Page;
use DynamicContentForElementor\DCE_Helper;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Elementor Button Readmore
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */
class DCE_Widget_ReadMore extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-readmore';
    }
    
    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('Read More', DCE_TEXTDOMAIN);
    }
    public function get_description() {
        return __('Add a “read more” button below your post or on the block in the archive, create a call-to-action. The button is pure code based, which will allow you to write formatted text and also an SVG directly inside the CLICK', DCE_TEXTDOMAIN);
    }
    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/read-more-button/';
    }
    public function get_icon() {
        return 'icon-dyn-readmore';
    }
    static public function get_position() {
        return 3;
    }
    protected function _register_controls() {

        $post_type_object = get_post_type_object(get_post_type());

        $this->start_controls_section(
            'section_readmore', [
                    'label' => __('Settings', DCE_TEXTDOMAIN)
            ]
        );

        $this->add_control(
            'html_tag', [
                'label' => __('HTML Tag', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'button' => __('Button', DCE_TEXTDOMAIN),
                    'h1' => __('H1', DCE_TEXTDOMAIN),
                    'h2' => __('H2', DCE_TEXTDOMAIN),
                    'h3' => __('H3', DCE_TEXTDOMAIN),
                    'h4' => __('H4', DCE_TEXTDOMAIN),
                    'h5' => __('H5', DCE_TEXTDOMAIN),
                    'h6' => __('H6', DCE_TEXTDOMAIN),
                    'p' => __('p', DCE_TEXTDOMAIN),
                    'div' => __('div', DCE_TEXTDOMAIN),
                    'span' => __('span', DCE_TEXTDOMAIN),
                ],
                'default' => 'div',
                'condition' => [
                    'link_to' => 'none',
                ]
            ]
        );
        $this->add_control(
            'type_of_button', [
                'label' => __('Button type', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'default' => 'html',
                'options' => [
                    'text' => __('Text', DCE_TEXTDOMAIN),
                    'html' => __('Html', DCE_TEXTDOMAIN),
                    'image' => 'Image',
                ],
            ]
        );

        $this->add_control(
            'button_text', [
                'label' => __('Button Text', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'default' => __('Read More', DCE_TEXTDOMAIN),
                'placeholder' => __('Read More', DCE_TEXTDOMAIN),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'type_of_button' => 'text',
                ]
            ]
        );
        $this->add_control(
            'button_html', [
                'label' => __('Button HTML', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::CODE,
                'language' => 'html',
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'Read <b>more</b>',
                'condition' => [
                    'type_of_button' => 'html',
                ],
            ]
        );
        $this->add_control(
          'button_image',
          [
             'label' => __( 'Button Image', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::MEDIA,
             'default' => [
                'url' => '',
             ],
             'condition' => [
                    'type_of_button' => 'image',
                ]
          ]
        );
        $this->add_responsive_control(
            'align', [
                'label' => __('Alignment', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'prefix_class' => 'rmbtn-align-',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'align_justify', [
                'label' => __('Justify Alignment', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => '',
                
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'align' => 'justify',
                     'icon_rm' => '',
                ],
            ]
        );
        $this->add_responsive_control(
          'rm_width', [
            'label' => __('Width', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => '%'
            ],
            'size_units' => [ '%', 'px' ],
            'range' => [
                '%' => [
                    'max' => 100,
                    'min' => 0,
                    'step' => 1,
                ],
                'px' => [
                    'max' => 300,
                    'min' => 0,
                    'step' => 1,
                ],
                
            ],
            'selectors' => [
                '{{WRAPPER}} .dce-btn-readmore' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                    'align!' => 'justify',
                ],
          ]
        );
        $this->add_control(
            'icon_rm',
            [
                'label' => __( 'Icons', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::ICON,
            ]
        );
        $this->add_control(
            'icon_rm_position',
            [
                'label' => __( 'Icon Position', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __( 'Before', DCE_TEXTDOMAIN ),
                    'right' => __( 'After', DCE_TEXTDOMAIN ),
                ],
                'prefix_class' => 'icon-',
            ]
        );
        $this->add_control(
            'space_icon_rm',
            [
                'label' => __( 'Space', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                        'size' => 7,
                ],
                'range' => [
                        'px' => [
                                'min' => 0,
                                'max' => 100,
                        ],
                ],
                'selectors' => [
                        '{{WRAPPER}}.icon-left .icon-rm' => 'padding-right: {{SIZE}}{{UNIT}} !important;',
                        '{{WRAPPER}}.icon-right .icon-rm' => 'padding-left: {{SIZE}}{{UNIT}} !important;',
                ],
                'condition' => [
                    'icon_rm!' => '',
                ],
            ]
        );
        $this->add_control(
            'link_to', [
                'label' => __('Link to', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'default' => 'post',
                'options' => [
                    'none' => __('None', DCE_TEXTDOMAIN),
                    'home' => __('Home URL', DCE_TEXTDOMAIN),
                    'post' => 'Post URL',
                    'custom_field' => 'Custom Field',
                    'custom' => __('Custom URL', DCE_TEXTDOMAIN),
                ],
            ]
        );

        $this->add_control(
            'link', [
                'label' => __('Link', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::URL,
                'placeholder' => __('http://your-link.com', DCE_TEXTDOMAIN),
                'condition' => [
                    'link_to' => 'custom',
                ],
                'default' => [
                    'url' => '',
                ],
                'show_label' => false,
            ]
        );


        $metas = DCE_Helper::get_post_metas(true);
        //var_dump($metas); die();

        $i = 0;
        $metas_select = array();
        foreach ($metas as $mkey => $ameta) {
            //if($mkey == 'file' || $mkey == 'url' || $mkey == 'text'){
            ksort($ameta);
            $metas_select[$mkey]['label'] = $mkey;
            $metas_select[$mkey]['options'] = $ameta;
            $i++;
            //}
        }

        $this->add_control(
                'custom_field_id', [
            'label' => __('META Field', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            //'options' => $this->get_acf_field(),
            'groups' => $metas_select,
            //'description' => 'Select the Field',
            'condition' => [
                    'link_to' => 'custom_field',
                ],
            ]
        );
        $this->add_control(
            'custom_field_target',
            [
              'label' => __( 'Target', DCE_TEXTDOMAIN ),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __( 'Blank', DCE_TEXTDOMAIN ),
              'label_off' => __( 'Same', DCE_TEXTDOMAIN ),
              'return_value' => 'yes',
             
              'condition' => [
                    'link_to' => 'custom_field',
                ],
            ]
        );
        

         $this->add_control(
            'data_source',
            [
              'label' => __( 'Source', DCE_TEXTDOMAIN ),
              'description' => __( 'Select the data source', DCE_TEXTDOMAIN ),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __( 'Same', DCE_TEXTDOMAIN ),
              'label_off' => __( 'other', DCE_TEXTDOMAIN ),
              'return_value' => 'yes',
              'separator' => 'before',
              'condition' => [
                    'link_to' => 'post',
                ],
            ]
        );
        $this->add_control(
            'other_post_source', [
              'label' => __('Select from other source post', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SELECT,
              
              'groups' => DCE_Helper::get_all_posts(get_the_ID(), true),
              'default' => '',
              'condition' => [
                    'link_to' => 'post',
                    'data_source' => '',
                ], 
            ]
        );
        $this->end_controls_section();


         // ------------------------------------------- [SECTION Ajax]

        $this->start_controls_section(
            'section_ajax', [
                'label' => __('Ajax', DCE_TEXTDOMAIN),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'link_to' => 'post',
                ],
            ]
        );
        $this->add_group_control(
            DCE_Group_Control_Ajax_Page::get_type(),
            [
                'name' => 'ajax_page',
                'label' => 'Ajax PAGE',
                'selector' => $this->get_id(),
            ]
          );
        $this->end_controls_section();


        // ------------------------------------------- [SECTION STYLE]
        $this->start_controls_section(
            'section_style', [
                'label' => __('Button', DCE_TEXTDOMAIN),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'readmore_color', [
                'label' => __('Text Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore > span, {{WRAPPER}} .dce-btn-readmore .icon-rm,  {{WRAPPER}} .dce-btn-readmore:before,  {{WRAPPER}} .dce-btn-readmore:after' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .button--asolo:before, {{WRAPPER}} .button--asolo:after' => 'border-color: {{VALUE}};'
                ],
                
            ]
        );

        $this->add_control(
            'readmore_bgcolor', [
                'label' => __('Background Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore:not(.button--pipaluk), {{WRAPPER}} .button--pipaluk:after, {{WRAPPER}} .button--tamaya:before, {{WRAPPER}} .button--tamaya:after' => 'background-color: {{VALUE}};',
                ],
                
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .dce-btn-readmore',
            ]
        );
        
        $this->add_control(
            'readmore_space_heading',
            [
                'label' => __( 'Space', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'readmore_padding', [
                'label' => __('Padding', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%'],
                'default' => [
                    'top' => 10,
                    'right' => 20,
                    'bottom' => 10,
                    'left' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore > span, {{WRAPPER}} .dce-btn-readmore:after, {{WRAPPER}} .dce-btn-readmore:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .dce-btn-readmore.icon_button .icon-rm' => 'top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{LEFT}}{{UNIT}};'
                ],
                
            ]
        );
        $this->add_responsive_control(
            'readmore_margin',
                [
                'label'         => __( 'Margin', DCE_TEXTDOMAIN ),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => [ 'px', '%' ],
                'selectors'     => [
                        '{{WRAPPER}} .dce-btn-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_control(
            'readmore_style_heading',
            [
                'label' => __( 'Style', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'readmore_border',
                'label' => __('Border', DCE_TEXTDOMAIN),            
                'selector' => '{{WRAPPER}} .dce-btn-readmore, {{WRAPPER}} .button--asolo:after, {{WRAPPER}} .button--asolo:before',
                
            ]
        );
        
        $this->add_control(
            'readmore_border_radius', [
                'label' => __('Border Radius', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore, {{WRAPPER}} .dce-btn-readmore:before, {{WRAPPER}} .dce-btn-readmore:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                
            ]
        );
        $this->add_group_control(
             Group_Control_Text_Shadow::get_type(),
                [
                    'name'      => 'text_shadow',
                    'selector'  => '{{WRAPPER}} .dce-btn-readmore',
                ]
            );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name' => 'box_shadow_readmore',
                'selector' => '{{WRAPPER}} .dce-btn-readmore',
                
            ]
        );
        
        $this->end_controls_section();
 
        // ------------------------------------------- [SECTION STYLE - ICON]
        $this->start_controls_section(
            'section_icon_style', [
                'label' => __('Icon', DCE_TEXTDOMAIN),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'icon_rm!' => '',
                ],
            ]
        );
        
        $this->add_control(
            'readmore_icon_color', [
                'label' => __('Icon Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore .icon-rm' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'size_icon_rm',
            [
                'label' => __( 'Size', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                        'px' => [
                                'min' => 0,
                                'max' => 100,
                        ],
                ],
                'selectors' => [
                        '{{WRAPPER}} .dce-btn-readmore .icon-rm' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'ypos_icon_rm',
            [
                'label' => __( 'Position Y', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                        'px' => [
                                'min' => -100,
                                'max' => 100,
                        ],
                ],
                'selectors' => [
                        '{{WRAPPER}} .dce-btn-readmore .icon-rm' => 'top: {{SIZE}}{{UNIT}} !important;',
                ],
                
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_rolhover_style', [
                'label' => __('Roll-Hover', DCE_TEXTDOMAIN),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'link_to!' => 'none',
                ]
            ]
        );
        $this->add_control(
            'readmore_hover_heading',
            [
                'label' => __( 'Roll-Hover', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                
            ]
        );
        $this->add_control(
            'readmore_color_hover', [
                'label' => __('Text Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore:hover > span, {{WRAPPER}} .dce-btn-readmore:hover:after,  {{WRAPPER}} .dce-btn-readmore:hover:before, {{WRAPPER}} .dce-btn-readmore:hover .icon-rm' => 'color: {{VALUE}};',
                ],
                
            ]
        );

        $this->add_control(
            'readmore_bgcolor_hover', [
                'label' => __('Background Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore:not(.button--pipaluk):not(.button--isi):not(.button--aylen):hover, {{WRAPPER}} .dce-btn-readmore:not(.button--pipaluk):hover:after, {{WRAPPER}} .dce-btn-readmore:not(.button--pipaluk):not(.button--wapasha):not(.button--nina):hover:before, {{WRAPPER}} .button--pipaluk:hover:after, {{WRAPPER}} .button--moema:before, {{WRAPPER}} .button--aylen:after, {{WRAPPER}} .button--aylen:before' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .button--pipaluk:before, {{WRAPPER}} .button--wapasha:before, {{WRAPPER}} .button--antiman:before, {{WRAPPER}} .button--itzel:before' => 'border-color: {{VALUE}};'
                ],
                
            ]
        );
        $this->add_control(
            'readmore_bordercolor_hover', [
                'label' => __('Border Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                
                'selectors' => [
                    '{{WRAPPER}} .dce-btn-readmore:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    
                    'readmore_border_border!' => '',
                ]
            ]
        );
        $this->add_control(
            'style_effect', [
                'label' => __('Effect', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'separator' => 'before',
                'options' => [
                    '' => __('None', DCE_TEXTDOMAIN),
                    'button--asolo' => __('Asolo', DCE_TEXTDOMAIN),
                    'button--winona' => __('Winona', DCE_TEXTDOMAIN),
                    'button--ujarak' => __('Ujarak', DCE_TEXTDOMAIN),
                    'button--wayra' => __('Wayra', DCE_TEXTDOMAIN),
                    'button--tamaya' => __('Tamaya', DCE_TEXTDOMAIN),
                    'button--rayen' => __('Rayen', DCE_TEXTDOMAIN),
                    'button--pipaluk' => __('Pipaluk', DCE_TEXTDOMAIN),
                    'button--nuka' => __('Nuka', DCE_TEXTDOMAIN),
                    'button--moema' => __('Moema', DCE_TEXTDOMAIN),
                    'button--isi' => __('Isi', DCE_TEXTDOMAIN),
                    'button--aylen' => __('Aylen', DCE_TEXTDOMAIN),
                    'button--saqui' => __('Saqui', DCE_TEXTDOMAIN),
                    'button--wapasha' => __('Wapasha', DCE_TEXTDOMAIN),
                    'button--nina' => __('Nina', DCE_TEXTDOMAIN),
                    'button--nanuk' => __('Nanuk', DCE_TEXTDOMAIN),
                    'button--antiman' => __('Antiman', DCE_TEXTDOMAIN),
                    'button--itzel' => __('Itzel', DCE_TEXTDOMAIN),
                    // 'button--naira' => __('Naira', DCE_TEXTDOMAIN),
                    // 'button--quidel' => __('Quidel', DCE_TEXTDOMAIN),
                    // 'button--sacnite' => __('Sacnite', DCE_TEXTDOMAIN),
                    // 'button--shikoba' => __('Shikoba', DCE_TEXTDOMAIN),
                ],
                'default' => '',
                'condition' => [
                    'link_to!' => 'none',
                    'hover_animation' => '',
                    'type_of_button' => 'text'
                ]
            ]
        );
        $this->add_control(
            'hover_animation', [
                'label' => __('Hover Animation', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'condition' => [
                    'link_to!' => 'none',
                    'style_effect' => ''
                ],
                'separator' => 'before',
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if ( empty( $settings ) )
            return;
        //
        // ------------------------------------------
        $dce_data = DCE_Helper::dce_dynamic_data($settings['other_post_source']);
        $id_page = $dce_data['id'];
        $global_is = $dce_data['is'];
        $type_page = $dce_data['type'];
        // ------------------------------------------
        
        if ($settings['ajax_page_enabled'] == 'open'){
            ?>
            <script type='text/javascript'>
            /* <![CDATA[ */
            var dceAjaxPath = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' ); ?>"};
            /* ]]> */
            </script>
            
            <?php
        }

        // -----------------------------------------
        $content_button_raw = '';
        if( $settings['type_of_button'] == 'text' ){
            $content_button_raw = __($settings['button_text'], DCE_TEXTDOMAIN.'_texts');
        }else if( $settings['type_of_button'] == 'html' ){
            $content_button_raw = __($settings['button_html'], DCE_TEXTDOMAIN.'_texts');
        }else if( $settings['type_of_button'] == 'image' ){
            $content_button_raw = $settings['button_image']['url'];
        }
        
        $title = '<span>'.$content_button_raw.'</span>';
        //var_dump(get_post_meta($id_page,$settings['custom_field_id'],true));
        switch ($settings['link_to']) {
            case 'custom' :
                if ( $settings['link']['url'] ) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = '#';
                }
                break;

            case 'post' :
                $link = esc_url(get_the_permalink($id_page));
                break;

            case 'custom_field' :
                $link = get_post_meta($id_page,$settings['custom_field_id'],true);

                if(is_numeric($link)) $link = wp_get_attachment_url($link);

                break;

            case 'home' :
                $link = esc_url(get_home_url());
                break;

            case 'none' :
            default:
                $link = false;
                break;
        }
        $target = $settings['link']['is_external'] ? 'target="_blank"' : '';
        if( $settings['custom_field_target'] ) $target = 'target="_blank"';

        $animation_class = !empty($settings['hover_animation']) ? ' elementor-animation-' . $settings['hover_animation'] : '';
        $effect_class = !empty($settings['style_effect']) ? ' eff_button '.$settings['style_effect'] : '';
        
        $icon_class = !empty($settings['icon_rm']) ? ' icon_button' : '';

        if($settings['icon_rm']){
            $title = '<i class="icon-rm '.$settings['icon_rm'].'"></i>'.$title;
            //$title = '<span><i class="icon-rm '.$settings['icon_rm'].'"></i>'.$content_button_raw.'</span>';
        }

        if (empty($title))
            return;

        $html = '';

        $data_text_effect = '';
        if( $settings['style_effect'] != '' && $settings['type_of_button'] == 'text' ) $data_text_effect = ' data-text="'.$content_button_raw.'"';
       
        if ($link) {
            $html .= sprintf('<a class="dce-btn-readmore%4$s%5$s%6$s" href="%1$s" %2$s%7$s>%3$s</a>', $link, $target, $title, $animation_class, $effect_class, $icon_class, $data_text_effect);
        } else {
            $html_tag = $settings['html_tag'] ? $settings['html_tag'] : 'span';
            $html .= sprintf('<%1$s class="dce-btn-readmore%2$s%3$s%4$s">%5$s</%s>', $html_tag, $animation_class, $effect_class, $icon_class, $title);
        }
        $scriptLetters = '<script>jQuery(".button--nina > span, .button--nanuk > span").each(function(){
                        jQuery(this).html(jQuery(this).text().replace(/([^\x00-\x80]|\w)/g, "<span>$&</span>"));
                    });</script>';
        echo '<div class="dce-wrapper">';
        echo $html.$scriptLetters;
        echo '</div>';
    }

    protected function _content_template() {
        
    }

}
