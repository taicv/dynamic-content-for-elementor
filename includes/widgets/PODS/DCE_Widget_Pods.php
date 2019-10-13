<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\Controls\DCE_Group_Control_Filters_CSS;
use DynamicContentForElementor\Controls\DCE_Group_Control_Transform_Element;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dynamic PODS Fields
 *
 * Widget PODS for Dynamic Content for Elementor
 *
 */
class DCE_Widget_Pods extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-pods';
    }

    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('PODS Fields', DCE_TEXTDOMAIN);
    }

    public function get_icon() {
        return 'icon-dyn-acffields';
    }

    public function get_script_depends() {
        return [ 'elementor-dialog'];
    }
    
    public function get_style_depends() {
        return [ 'dce-pods' ];
    }

    public function get_plugin_depends() {
        return array('pods' => 'pods');
    }

    protected function _register_controls() {

        // ********************** Section BASE **********************
        $this->start_controls_section(
                'section_content', [
            'label' => __('Pods', DCE_TEXTDOMAIN)
                ]
        );
        $this->add_control(
                'pods_field_list', [
            'label' => __('Fields list', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_pods_fields(),
            'default' => 'Select the field'
                ]
        );
        $this->add_control(
                'pods_field_type', [
            'label' => __('Fields type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'empty' => __('Empty', DCE_TEXTDOMAIN),
                'text' => __('Text', DCE_TEXTDOMAIN),
                'url' => __('URL', DCE_TEXTDOMAIN),
                'phone' => __('Phone number', DCE_TEXTDOMAIN),
                'email' => __('Email', DCE_TEXTDOMAIN),
                'paragraph' => __('Paragraph', DCE_TEXTDOMAIN),
                'wysiwyg' => __('WYSIWYG editor', DCE_TEXTDOMAIN),
                'code' => __('Code', DCE_TEXTDOMAIN),
                'datetime' => __('Datetime', DCE_TEXTDOMAIN),
                'date' => __('Date', DCE_TEXTDOMAIN),
                'time' => __('Time', DCE_TEXTDOMAIN),
                'image' => __('Image', DCE_TEXTDOMAIN),
            ],
            'default' => 'text',
                ]
        );
        $this->add_control(
                'pods_field_hide', [
            'label' => __('Hide if empty', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('No', DCE_TEXTDOMAIN),
            'label_on' => __('Yes', DCE_TEXTDOMAIN),
            'default' => 'yes',
            'description' => 'Hide the field in front end layer'
                ]
        );

        $this->end_controls_section();

        // ********************************************************************************* Section SETTINGS
        $this->start_controls_section(
                'section_settings', [
            'label' => 'Settings',
            'condition' => ['pods_field_type' => ['email', 'text', 'image', 'phone']]
                ]
        );

        $this->add_control(
                'pods_text_before', [
            'label' => __('Text before', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'code', 'datetime', 'date', 'time']]
                ]
        );
        $this->add_control(
                'pods_text_after', [
            'label' => __('Text after', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'code', 'datetime', 'date', 'time']]
                ]
        );

        $this->add_control(
                'pods_field_email_target', [
            'label' => __('Link mailto', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('Off', DCE_TEXTDOMAIN),
            'label_on' => __('On', DCE_TEXTDOMAIN),
            'condition' => ['pods_field_type' => 'email']
                ]
        );

        $this->add_control(
                'pods_url_enable', [
            'label' => __('Enable link', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('No', DCE_TEXTDOMAIN),
            'label_on' => __('Yes', DCE_TEXTDOMAIN),
            'default' => 'yes',
            'condition' => ['pods_field_type' => 'url']
                ]
        );
        $this->add_control(
                'pods_url_custom_text', [
            'label' => __('Custom URL text', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['pods_field_type' => 'url']
                ]
        );
        $this->add_control(
                'pods_url_target', [
            'label' => __('Target type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '_self' => __('_self', DCE_TEXTDOMAIN),
                '_blank' => __('_blank', DCE_TEXTDOMAIN),
                '_parent' => __('_parent', DCE_TEXTDOMAIN),
                '_top' => __('_top', DCE_TEXTDOMAIN),
            ],
            'default' => '_self',
            'condition' => ['pods_field_type' => 'url']
                ]
        );

        $this->add_control(
                'pods_phone_number_enable', [
            'label' => __('Enable link', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('No', DCE_TEXTDOMAIN),
            'label_on' => __('Yes', DCE_TEXTDOMAIN),
            'default' => 'yes',
            'condition' => ['pods_field_type' => 'phone']
                ]
        );
        $this->add_control(
                'pods_phone_number_custom_text', [
            'label' => __('Custom phone number', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['pods_field_type' => 'phone']
                ]
        );

        $this->add_group_control(
                Group_Control_Image_Size::get_type(), [
            'name' => 'size',
            'label' => __('Image Size', DCE_TEXTDOMAIN),
            'default' => 'large',
            'condition' => ['pods_field_type' => 'image']
                ]
        );

        $this->add_control(
                'html_tag', [
            'label' => __('HTML Tag', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => [
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
            'condition' => ['pods_field_type' => ['email', 'text']]
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
            'prefix_class' => 'align-dce-',
            'selectors' => [
                '{{WRAPPER}}' => 'text-align: {{VALUE}};',
            ],
            'condition' => ['pods_field_type' => ['email', 'text', 'email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time', 'image']]
                ]
        );

        $this->add_control(
                'use_bg', [
            'label' => __('Background', DCE_TEXTDOMAIN),
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
            'default' => '0',
            'condition' => [
                'pods_field_type' => 'image',
            ]
                ]
        );
        $this->add_control(
                'bg_position', [
            'label' => __('Background position', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'default' => 'top center',
            'options' => [
                '' => __('Default', DCE_TEXTDOMAIN),
                'top left' => __('Top Left', DCE_TEXTDOMAIN),
                'top center' => __('Top Center', DCE_TEXTDOMAIN),
                'top right' => __('Top Right', DCE_TEXTDOMAIN),
                'center left' => __('Center Left', DCE_TEXTDOMAIN),
                'center center' => __('Center Center', DCE_TEXTDOMAIN),
                'center right' => __('Center Right', DCE_TEXTDOMAIN),
                'bottom left' => __('Bottom Left', DCE_TEXTDOMAIN),
                'bottom center' => __('Bottom Center', DCE_TEXTDOMAIN),
                'bottom right' => __('Bottom Right', DCE_TEXTDOMAIN),
            ],
            'selectors' => [
                '{{WRAPPER}} .dynamic-content-for-elementor-acfimage-bg' => 'background-position: {{VALUE}};',
            ],
            'condition' => [
                'pods_field_type' => 'image',
                'use_bg' => '1',
            ],
                ]
        );
        $this->add_control(
                'bg_extend', [
            'label' => __('Extend Background', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Show', DCE_TEXTDOMAIN),
            'label_off' => __('Hide', DCE_TEXTDOMAIN),
            'return_value' => 'yes',
            'condition' => [
                'use_bg' => '1',
            ],
            'prefix_class' => 'extendbg-',
            'condition' => [
                'pods_field_type' => 'image',
                'use_bg' => '1',
            ],
                ]
        );
        $this->add_responsive_control(
                'height', [
            'label' => __('Minimus Height', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 200,
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => [ 'px', '%', 'vh'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ],
                'px' => [
                    'min' => 1,
                    'max' => 1000,
                ],
                'vh' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .dynamic-content-for-elementor-pods-bg' => 'min-height: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'pods_field_type' => 'image',
                'use_bg' => '1',
            ],
                ]
        );


        $this->end_controls_section();

        // ------------------------------------------------------------ [ OVERLAY Image ]
        $this->start_controls_section(
                'section_overlay', [
            'label' => __('Overlay Image', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'pods_field_type' => 'image',
            ]
                ]
        );
        $this->add_control(
                'overlay_heading', [
            'label' => __('Overlay', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'pods_field_type' => [ 'text', 'image'],
            ]
                ]
        );
        $this->add_control(
                'use_overlay', [
            'label' => __('Overlay Image', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Show', DCE_TEXTDOMAIN),
            'label_off' => __('Hide', DCE_TEXTDOMAIN),
            'return_value' => 'yes',
                ]
        );
        $this->add_group_control(
                Group_Control_Background::get_type(), [
            'name' => 'background_overlay',
            'types' => [ 'classic', 'gradient'],
            'selector' => '{{WRAPPER}} .dce-overlay',
            'condition' => [
                'use_overlay' => 'yes',
            ]
                ]
        );
        $this->end_controls_section();



        // ********************** Section STYLE **********************
        $this->start_controls_section(
                'section_style', [
            'label' => __('Style', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]
                ]
        );
        $this->add_control(
                'tx_heading', [
            'label' => __('Text', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]
                ]
        );
        $this->add_control(
                'color', [
            'label' => __('Text Color', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::COLOR,
            'scheme' => [ 'type' => Scheme_Color::get_type(), 'value' => Scheme_Color::COLOR_1],
            'selectors' => [
                '{{WRAPPER}} .dynamic-content-for-elementor-pods .edc-pods' => 'color: {{VALUE}};',
            ],
            'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]
                ]
        );
        $this->add_control(
                'bg_color', [
            'label' => __('Background Color', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::COLOR,
            'scheme' => ['type' => Scheme_Color::get_type(), 'value' => Scheme_Color::COLOR_1],
            'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods' => 'background-color: {{VALUE}};',],
            'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]
                ]
        );
        $this->add_responsive_control(
                'pods_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%'],
            'selectors' => [
                '{{WRAPPER}} .dynamic-content-for-elementor-pods' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],]
        );
        $this->add_responsive_control(
                'pods_space', [
            'label' => __('Space', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0,],
            'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1,],],
            'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods' => 'margin-bottom: {{SIZE}}{{UNIT}};'],]
        );
        $this->add_responsive_control(
                'pods_shift', [
            'label' => __('Shift', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['max' => 180, 'min' => -180, 'step' => 1,],],
            'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-pods' => 'left: {{SIZE}}{{UNIT}};'],]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'typography_tx',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-pods',
            'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]]
        );
        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(), [
            'name' => 'text_shadow',
            'label' => __('Text shadow', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-pods',
            'condition' => ['pods_field_type' => ['email', 'text', 'url', 'phone', 'paragraph', 'wysiwyg', 'code', 'datetime', 'date', 'time']]
                ]
        );
    }

    // ************************************** PODS FIELDS
    /**
     * Get PODS - field e group
     */
    protected function get_pods_fields() {
        $option_list = array();
        $option_list = ['Select the Field'];
        $pods = pods_api()->load_pods();
        $post_type = get_post_type();

        //$post_type_elementor = Source_Local::get_template_type(get_the_ID());

        foreach ($pods as $pod) {
            //if ((!strcasecmp($post_type, $pod['name']))) {  // decommenta per la Versione del post corrente
                foreach ($pod['fields'] as $f) {
                    $option_list[$f['name']] = $f['label'] . '&nbsp;[' . $f['type'] . ']';
                }
                //break; // decommenta per la Versione del post corrente
            //}
        }

        return $option_list;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;
        
        // ------------------------------------------
        $demoPage = get_post_meta(get_the_ID(), 'demo_id', true);
        //
        $id_page = ''; //get_the_ID();
        $type_page = '';
        //
        global $global_ID;
        global $global_TYPE;
        global $is_blocks;
        global $global_is;

        // $podsResult = "";
        $idFields = $settings['pods_field_list'];
        if (!$idFields) {
            return;
        }
        
        $use_bg = $settings['use_bg'];

        // // ------------------------------------------

        $wrap_effect_start = '<div class="mask"><div class="wrap-filters">';
        $wrap_effect_end = '</div></div>';

        // // ------------------------------------------
        $overlay_block = "";
        if ($settings['use_overlay'] == 'yes') {
            $overlay_block = '<div class="dce-overlay"></div>';
        }
        // // ------------------------------------------
        $overlay_hover_block = '<div class="dce-overlay_hover"></div>';
        // // ------------------------------------------

        $pod_value = pods_field_display($idFields);
        //$pod_type = pods()->fields($idFields, 'type'); 
        $pod_type = $settings['pods_field_type'];
        // echo $pod_type;

        $html = '';
        switch ($pod_type) {
            case 'code':
                $settings['html_tag'] = 'code';
                break;
            case 'image':
                $settings['html_tag'] = 'div';
                break;
            default:
                $settings['html_tag'] = 'div';
                break;
        }

        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';

        if ($settings['pods_field_hide'] && empty($pod_value) && !(\Elementor\Plugin::$instance->editor->is_edit_mode()))
            $html .= '<style>' . $this->get_unique_selector() . '{display:none !important;}</style>';

        $html .= sprintf('<%1$s class="dynamic-content-for-elementor-pods pippotto %2$s">', $settings['html_tag'], $animation_class);

        // Email
        if ($pod_type == 'email' && $settings['pods_field_email_target'])
            $pod_value = '<a href="mailto:' . $pod_value . '"> ' . $pod_value . '</a>';

        // URL
        if ($pod_type == 'url') {
            $text_url = $pod_value;
            if (!empty($settings['pods_url_custom_text']))
                $text_url = $settings['pods_url_custom_text'];
            if ($settings['pods_url_enable'])
                $pod_value = '<a href="' . $pod_value . '" target="' . $settings['pods_url_target'] . '"> ' . $text_url . '</a>';
        }

        // Phone number
        if ($pod_type == 'phone') {
            $text_number = $pod_value;
            if (!empty($settings['pods_phone_number_custom_text']))
                $text_number = $settings['pods_phone_number_custom_text'];
            if ($settings['pods_phone_number_enable'])
                $pod_value = '<a href="tel:' . preg_replace("/[^0-9]/", '', $pod_value) . '"> ' . $text_number . '</a>';
        }

        // Image
        if ($pod_type == 'image') {
            if (!$use_bg) {
                $podsResult = '<div class="acf-image">' . $wrap_effect_start . '<img src="' . $pod_value . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
            } else {
                $bg_featured_image = '<div class="acf-image acf-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-acfimage-bg" style="background-image: url(' . $pod_value . '); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                $podsResult = $bg_featured_image;
            }
            $html .= $podsResult;
        } else {
            $pod_value = '<span class="edc-pods">' . $pod_value . '</span>';
            // Text before and after
            if ($settings['pods_text_before'] != "" || $settings['pods_text_after'] != "") {
                $pod_value = '<span class="tx-before">' . __($settings['pods_text_before'], DCE_TEXTDOMAIN) . '</span>' . $pod_value . '<span class="tx-after">' . __($settings['pods_text_after'], DCE_TEXTDOMAIN) . '</span>';
            }
            $html .= $pod_value;
        }

        $html .= sprintf('</%s>', $settings['html_tag']);
        echo $html;
    }
}
