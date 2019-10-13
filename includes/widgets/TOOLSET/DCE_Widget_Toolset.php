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
 * Dynamic Toolset Fields
 *
 * Widget Toolset for Dynamic Content for Elementor
 *
 */
class DCE_Widget_Toolset extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-toolset';
    }

    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('TOOLSET Fields', DCE_TEXTDOMAIN);
    }

    public function get_icon() {
        return 'icon-dyn-acffields';
    }

    public function get_script_depends() {
        return [ 'elementor-dialog'];
    }

    public function get_style_depends() {
        return [ 'dce-toolset' ];
    }

    public function get_plugin_depends() {
        return array('types' => 'toolset');
    }


    protected function _register_controls() {

        // ********************** Section BASE **********************
        $this->start_controls_section(
            'section_content', [
            'label' => __('Toolset', DCE_TEXTDOMAIN)
            ]
        );

        $this->add_control(
            'toolset_field_list', [
            'label' => __('Fields list', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'default' => 'empty',
            'groups' =>  $this->get_toolset_fields(),
            ]
        );

        $this->add_control(
            'toolset_field_type', [
            'label' => __('Field type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'default' => 0,
            'options' => [
                'empty' => __('Select for options', DCE_TEXTDOMAIN),
                'textfield' => __('Textfield', DCE_TEXTDOMAIN),
                'url' => __('URL', DCE_TEXTDOMAIN),
                'phone' => __('Phone', DCE_TEXTDOMAIN),
                'email' => __('Email', DCE_TEXTDOMAIN),
                'textarea' => __('Textarea', DCE_TEXTDOMAIN),
                'wysiwyg' => __('WYSIWYG', DCE_TEXTDOMAIN),
                'image' => __('Image', DCE_TEXTDOMAIN),
                'date' => __('Date', DCE_TEXTDOMAIN),
                'numeric' => __('Numeric', DCE_TEXTDOMAIN),
                'video' => __('Video', DCE_TEXTDOMAIN),
                ]
            ]
        );

        $this->add_control(
            'toolset_field_hide', [
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
            'condition' => ['toolset_field_type!' => 'video' ]
            ]
        );

        $this->add_control(
            'toolset_text_before', [
            'label' => __('Text before', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            // 'condition' => ['toolset_field_type' => ['textfield', 'url', 'phone', 'email', 'textarea','wysiwyg' ,'date', 'numeric']]
            'condition' => ['toolset_field_type!' => 'video' ]
             ]
        );

        $this->add_control(
            'toolset_text_after', [
            'label' => __('Text after', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            // 'condition' => ['toolset_field_type' => ['textfield', 'url', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric']]
            'condition' => ['toolset_field_type!' => 'video' ]
            ]

        );

        $this->add_control(
            'toolset_url_enable', [
            'label' => __('Enable link', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('No', DCE_TEXTDOMAIN),
            'label_on' => __('Yes', DCE_TEXTDOMAIN),
            'default' => 'yes',
            'condition' => ['toolset_field_type' => 'url']
            ]
        );
        $this->add_control(
            'toolset_url_custom_text', [
            'label' => __('Custom URL text', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['toolset_field_type' => 'url']
            ]
        );
        $this->add_control(
            'toolset_url_target', [
            'label' => __('Target type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '_self' => __('_self', DCE_TEXTDOMAIN),
                '_blank' => __('_blank', DCE_TEXTDOMAIN),
                '_parent' => __('_parent', DCE_TEXTDOMAIN),
                '_top' => __('_top', DCE_TEXTDOMAIN),
            ],
            'default' => '_self',
            'condition' => ['toolset_field_type' => 'url']
            ]
        );

        $this->add_control(
            'toolset_date_format', [
            'label' => __('Format', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'default' => 0,
            'options' => [
                'default' => __('Wordpress settings', DCE_TEXTDOMAIN),
                '%Y%m%d' => __('YYYYMMDD', DCE_TEXTDOMAIN),
                '%Y-%m-%d' => __('YYYY-MM-DD', DCE_TEXTDOMAIN),
                '%d/%m/%Y' => __('DD/MM/YYYY', DCE_TEXTDOMAIN),
                '%d-%m-%Y' => __('DD-MM-YYYY', DCE_TEXTDOMAIN),
                '%Y-%m-%d %H:%M:%S' => __('YYYY-MM-DD H:M:S', DCE_TEXTDOMAIN),
                '%d/%m/%Y %H:%M:%S' => __('DD/MM/YY H:M:S', DCE_TEXTDOMAIN),
                '%d/%m/%y' => __('D/M/Y', DCE_TEXTDOMAIN),
                '%d-%m-%y' => __('D-M-Y', DCE_TEXTDOMAIN),
                '%I:%M %p' => __('H:M (12 hours)', DCE_TEXTDOMAIN),
                '%A %m %B %Y' => __('Full date', DCE_TEXTDOMAIN),
                '%A %m %B %Y at %H:%M' => __('Full date with hours', DCE_TEXTDOMAIN),
                'timestamp' => __('Timestamp', DCE_TEXTDOMAIN),
                'custom' => __('Custom', DCE_TEXTDOMAIN),
                ],
            'condition' => ['toolset_field_type' => 'date']
            ]
        );
        $this->add_control(
            'toolset_date_custom_format', [
            'label' => __('Custom date format', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['toolset_date_format' => 'custom'],
            'description' => 'See PHP strftime() function reference'

            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(), [
            'name' => 'size',
            'label' => __('Image Size', DCE_TEXTDOMAIN),
            'default' => 'large',
            'condition' => ['toolset_field_type' => 'image']
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
            'condition' => ['toolset_field_type' => ['textfield', 'url','image', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric']]
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
                'toolset_field_type' => 'image',
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
                '{{WRAPPER}} .dynamic-content-for-elementor-toolset-bg' => 'background-position: {{VALUE}};',
            ],
            'condition' => [
                'toolset_field_type' => 'image',
                'use_bg' => '1',
            ],
                ]
        );

        $this->add_control(
            'bg_extend', [
            'label' => __('Extend background', DCE_TEXTDOMAIN),
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
                'toolset_field_type' => 'image',
                'use_bg' => '1',
            ],
                ]
        );
        $this->add_responsive_control(
            'height', [
            'label' => __('Minimus height', DCE_TEXTDOMAIN),
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
                '{{WRAPPER}} .dynamic-content-for-elementor-toolset-bg' => 'min-height: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'toolset_field_type' => 'image',
                'use_bg' => '1',
            ],
                ]
        );

        $this->add_control(
            'toolset_phone_number_enable', [
            'label' => __('Enable link', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('No', DCE_TEXTDOMAIN),
            'label_on' => __('Yes', DCE_TEXTDOMAIN),
            'default' => 'yes',
            'condition' => ['toolset_field_type' => 'phone']
            ]
        );
        $this->add_control(
            'toolset_phone_number_custom_text', [
            'label' => __('Custom phone number', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['toolset_field_type' => 'phone']
            ]
        );

        $this->add_control(
            'toolset_email_target', [
            'label' => __('Link mailto', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('Off', DCE_TEXTDOMAIN),
            'label_on' => __('On', DCE_TEXTDOMAIN),
            'condition' => ['toolset_field_type' => 'email']
            ]
        );

        $this->add_control(
            'toolset_numeric_currency', [
            'label' => __('Currency', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('No', DCE_TEXTDOMAIN),
            'label_on' => __('Yes', DCE_TEXTDOMAIN),
            'default' => 'no',
            'condition' => ['toolset_field_type' => 'numeric']
            ]
        );

        $this->add_control(
            'toolset_currency_symbol', [
            'label' => __('Currency symbol', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['toolset_field_type' => 'numeric', 'toolset_numeric_currency' => 'yes']
            ]
        );

        $this->add_control(
            'toolset_currency_symbol_position', [
            'label' => __('Symbol position', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'before' => ['title' => __('Before', DCE_TEXTDOMAIN), 'icon' => 'fa fa-arrow-left'],
                'after' => ['title' => __('After', DCE_TEXTDOMAIN), 'icon' => 'fa fa-arrow-right']],
            'default' => 'before',
            'toggle' => true,
            'condition' => ['toolset_field_type' => 'numeric', 'toolset_numeric_currency' => 'yes']
            ]
        );

        $this->end_controls_section();

 // ------------------------------------------------------------ [ OVERLAY Image ]
        $this->start_controls_section(
                'section_overlay', [
            'label' => __('Overlay Image', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'toolset_field_type' => 'image',
            ]
                ]
        );
        $this->add_control(
                'overlay_heading', [
            'label' => __('Overlay', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'toolset_field_type' => ['textfield', 'url','image', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric'],
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
            'condition' => ['toolset_field_type' => ['textfield', 'url','image', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric']]
            ]
        );
        $this->add_control(
            'tx_heading', [
            'label' => __('Text', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => ['toolset_field_type' => ['textfield', 'url','image', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric']]
            ]
        );
        $this->add_control(
            'color', [
            'label' => __('Text Color', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::COLOR,
            'scheme' => [ 'type' => Scheme_Color::get_type(), 'value' => Scheme_Color::COLOR_1],
            'selectors' => [
                '{{WRAPPER}} .dynamic-content-for-elementor-toolset .edc-toolset' => 'color: {{VALUE}};',
            ],
            'condition' => ['toolset_field_type' => ['textfield', 'url','image', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric']]
            ]
        );
        $this->add_control(
            'bg_color', [
            'label' => __('Background Color', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::COLOR,
            'scheme' => ['type' => Scheme_Color::get_type(), 'value' => Scheme_Color::COLOR_1],
            'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'background-color: {{VALUE}};',],
            'condition' => ['toolset_field_type' => ['textfield', 'url','image', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric']]
            ]
        );
        $this->add_responsive_control(
            'toolset_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%'],
            'selectors' => [
                '{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],]
        );
        $this->add_responsive_control(
            'toolset_space', [
            'label' => __('Space', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0,],
            'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1,],],
            'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'margin-bottom: {{SIZE}}{{UNIT}};'],]
        );
        $this->add_responsive_control(
            'toolset_shift', [
            'label' => __('Shift', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['max' => 180, 'min' => -180, 'step' => 1,],],
            'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'left: {{SIZE}}{{UNIT}};'],]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'typography_tx',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-toolset',
            'condition' => ['toolset_field_type' => ['textfield', 'url','image', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric']]
            ]
        );
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
            'name' => 'text_shadow',
            'label' => __('Text shadow', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-toolset',
            'condition' => ['toolset_field_type' => ['textfield', 'url','image', 'phone', 'email', 'textarea','wysiwyg','date', 'numeric']]
            ]
        );

        $this->end_controls_section();


    }

    protected function get_toolset_fields() {
        $toolset_groups = wpcf_admin_fields_get_groups();
        $fieldList = array();
        $fieldList[0] = 'Select the field';
        foreach ($toolset_groups as $group){
            $options = array();
            $fields = wpcf_admin_fields_get_fields_by_group( $group['id'] );
            if (!is_array($fields)) continue;
            foreach ($fields as $field_key => $field) {
                $a=array();
                $a['group'] = $group['slug'];
                $a['field'] = $field_key;
                $a['type'] = $field['type'];
                $index = json_encode($a);
                $options[json_encode($a)] = $field['name'].' ('.$field['type'].')';
                if (empty($options)) continue;
            }
            array_push($fieldList, ['label' => $group['name'], 'options' => $options]);
        }
        return $fieldList;
    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        if (empty($settings)) return;

        $post_id = get_the_ID();

        $use_bg = $settings['use_bg'];
        $wrap_effect_start = '<div class="mask"><div class="wrap-filters">';
        $wrap_effect_end = '</div></div>';
        $overlay_block = "";
        if ($settings['use_overlay'] == 'yes') {
            $overlay_block = '<div class="dce-overlay"></div>';
        }
        $overlay_hover_block = '<div class="dce-overlay_hover"></div>';


        $f = json_decode($settings['toolset_field_list']);

        $html = '';
        switch($f->type){
            case 'textfield': case 'textarea':
                $f->value = types_render_field($f->field);
                $html = '<span class="edc-toolset">' . $f->value . '</span>';
                if ($settings['toolset_text_before'] != "" || $settings['toolset_text_after'] != "")
                    $html = '<span class="tx-before">' . __($settings['toolset_text_before'], DCE_TEXTDOMAIN) . '</span>' . $html . '<span class="tx-after">' . __($settings['toolset_text_after'], DCE_TEXTDOMAIN) . '</span>';
            break;
            case 'wysiwyg':
                $f->value = types_render_field($f->field, array('suppress_filters' => true ));
                $html = $f->value;
            break;

            case 'url':
                $f->value = types_render_field($f->field);

                if (preg_match('/href="(.*?)" /', $f->value, $match) == 1) $url = $match[1];
                $text_url = $url;

                if (!empty($settings['toolset_url_custom_text']))
                    $text_url = $settings['toolset_url_custom_text'];
                if ($settings['toolset_url_enable'])
                    $html = '<a href="' . $url . '" target="' . $settings['toolset_url_target'] . '"> ' . $text_url . '</a>';
                else
                    $html = $text_url;
                if ($settings['toolset_text_before'] != "" || $settings['toolset_text_after'] != "")
                    $html = '<span class="tx-before">' . __($settings['toolset_text_before'], DCE_TEXTDOMAIN) . '</span>' . $html . '<span class="tx-after">' . __($settings['toolset_text_after'], DCE_TEXTDOMAIN) . '</span>';
                break;

            case 'phone':
                $f->value = types_render_field($f->field);
                $text_number = $f->value;
                if (!empty($settings['toolset_phone_number_custom_text']))
                    $text_number = $settings['toolset_phone_number_custom_text'];
                if ($settings['toolset_phone_number_enable'])
                    $html = '<a href="tel:' . preg_replace("/[^0-9]/", '', $f->value) . '"> ' . $text_number . '</a>';
                else $html = $text_number;
                if ($settings['toolset_text_before'] != "" || $settings['toolset_text_after'] != "")
                    $html = '<span class="tx-before">' . __($settings['toolset_text_before'], DCE_TEXTDOMAIN) . '</span>' . $html . '<span class="tx-after">' . __($settings['toolset_text_after'], DCE_TEXTDOMAIN) . '</span>';
            break;
            case 'email':
                $f->value = types_render_field($f->field);
                if ($settings['toolset_email_target']) $html = $f->value;
                elseif (preg_match('/href="mailto:(.*?)" /', $f->value, $match) == 1) $html = $match[1];
                if ($settings['toolset_text_before'] != "" || $settings['toolset_text_after'] != "")
                    $html = '<span class="tx-before">' . __($settings['toolset_text_before'], DCE_TEXTDOMAIN) . '</span>' . $html . '<span class="tx-after">' . __($settings['toolset_text_after'], DCE_TEXTDOMAIN) . '</span>';
            break;
            case 'image':
                $img_size = $settings['size_size'];
                $f->value = types_render_field($f->field);
                if (preg_match('/src="(.*?)" /', $f->value, $match) == 1) $imgSrc = $match[1];

                $img_id = DCE_Helper::get_image_id($imgSrc);
                /*
                global $wpdb;
                $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE guid='%s';", $imgSrc ));
                $img_id = $attachment[0];
                */
                $img_url = Group_Control_Image_Size::get_attachment_image_src($img_id, 'size', $settings);


                if (!$use_bg) {
                  $html = '<div class="toolset-image">' . $wrap_effect_start . '<img src="'.$img_url.'" />' .$wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                }
                else {
                $bg_featured_image = '<div class="toolset-image toolset-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-toolset-bg" style="background-image: url(\'' . $img_url . '\'); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                 $html = $bg_featured_image;
                }
                break;
            case 'date':
                $f->value = types_render_field($f->field);
                if ($timestamp = types_render_field($f->field, array('format' => 'U','style' => 'text'))):
                    switch($settings['toolset_date_format']){
                            case 'default': $data = $f->value; break;
                            case 'timestamp'; $data = $timestamp; break;
                            case 'custom': $data = strftime($settings['toolset_date_custom_format'], $timestamp); break;
                            default: $data = strftime($settings['toolset_date_format'], $timestamp); break;
                        }
                    $html = '<span class="edc-toolset">' . $data . '</span>';
                    if ($settings['toolset_text_before'] != "" || $settings['toolset_text_after'] != "")
                        $html = '<span class="tx-before">' . __($settings['toolset_text_before'], DCE_TEXTDOMAIN) . '</span>' . $html . '<span class="tx-after">' . __($settings['toolset_text_after'], DCE_TEXTDOMAIN) . '</span>';
                endif;
                break;
            case 'numeric':
                $f->value = types_render_field($f->field);
                $number = $f->value;
                if($settings['toolset_numeric_currency'] && ($settings['toolset_currency_symbol'] != '')){
                    if ($settings['toolset_currency_symbol_position'] == 'before')
                        $number = $settings['toolset_currency_symbol'].$number;
                    else
                        $number .=$settings['toolset_currency_symbol'];
                }
                $html = '<span class="edc-toolset">' . $number . '</span>';
                if ($settings['toolset_text_before'] != "" || $settings['toolset_text_after'] != "")
                    $html = '<span class="tx-before">' . __($settings['toolset_text_before'], DCE_TEXTDOMAIN) . '</span>' . $html . '<span class="tx-after">' . __($settings['toolset_text_after'], DCE_TEXTDOMAIN) . '</span>';
                break;

            case 'video':
                $f->value = types_render_field($f->field);
                $video = $f->value;
                echo '<code>'.var_export($video, true).'</code>';
                //$html = '<span class="edc-toolset">' . $video . '</span>';

                break;

        }

        if ($settings['toolset_field_hide'] && empty($f->value) && !(\Elementor\Plugin::$instance->editor->is_edit_mode())){
             $html = '<style>' . $this->get_unique_selector() . '{display:none !important;} </style>';
        }

        switch($f->type){
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
        $render = sprintf('<%1$s class="dynamic-content-for-elementor-toolset %2$s">', $settings['html_tag'], $animation_class);
        $render .= $html;
        $render .= sprintf('</%s>', $settings['html_tag']);
        echo $render;
    }
}
