<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Utils;
use Elementor\Repeater;

use DynamicContentForElementor\DCE_Widgets;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\Group_Control_Outline;
use DynamicContentForElementor\Controls\DCE_Group_Control_Filters_CSS;
use DynamicContentForElementor\Controls\DCE_Group_Control_Transform_Element;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * DCE_Widget_Prototype Base widget class
 *
 * Base class for Dinamic Content Elements
 *
 * @since 0.4.0
 */

class DCE_Widget_Prototype extends Widget_Base {
    
    public $docs = 'https://www.dynamic.ooo';

    public function get_name() {
        return 'dce-prototype';
    }

    public function get_title() {
        return __('Prototype', DCE_TEXTDOMAIN);
    }
    
    public function get_description() {
        return __('Another Dynamic Widget', DCE_TEXTDOMAIN);
    }
    
    public function get_docs() {
        return $this->docs;
    }

    public function get_icon() {
        return 'eicon-animation';
    }
    
    static public function is_enabled() {
        return false;
    }

    public function get_categories() {
        $grouped_widgets = DCE_Widgets::get_widgets_by_group();
        $fullname = basename(get_class($this));
        $pieces = explode('\\', $fullname);
        $name = end($pieces);
        //var_dump($name); die();
        foreach ($grouped_widgets as $gkey => $group) {
            foreach ($group as $wkey => $widget) {
                if ($widget == $name) {
                    //var_dump($gkey); die();
                    return [ 'dynamic-content-for-elementor-'.  strtolower($gkey) ];
                }
            }
        }
        return [ 'dynamic-content-for-elementor' ];
    }
    
    static public function get_position() {
        return 666;
    }
    
    static public function get_satisfy_dependencies($ret = false) {
        $widgetClass = get_called_class();
        //require_once( __DIR__ . '/'.$widgetClass.'.php' );
        $myWdgt = new $widgetClass();
        return $myWdgt->satisfy_dependencies($ret);
    }
    
    public function get_plugin_depends() {
        return array();
    }
    
    public function satisfy_dependencies($ret = false, $deps = array()) {
        if (empty($deps)) {
            $deps = $this->get_plugin_depends();
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

    /**
     * A list of scripts that the widgets is depended in
     * */
    public function get_script_depends() {
        return [ ];
    }
    
    /*
    public function get_settings_for_display() {
        
    }
    */
    public function get_settings_for_display($setting_key = null, $original = false) {
        $settings = parent::get_settings_for_display($setting_key);
        if ($original) {
            return $settings;
        }
        if (!empty($settings)) {
            foreach ($settings as $key => $value) {
                if (is_string($value)) {
                    $value = \DynamicContentForElementor\DCE_Tokens::do_tokens($value);
                    $settings[$key] = $value;
                }
                // repeater
                if (is_array($value)) {

                    foreach ($value as $akey => $avalue) {
                        if (is_array($avalue)) {
                            foreach ($avalue as $rkey => $rvalue) {
                                if (is_string($rvalue)) {
                                    $rvalue = \DynamicContentForElementor\DCE_Tokens::do_tokens($rvalue);
                                    $settings[$key][$akey][$rkey] = $rvalue;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $settings;
    }
    
    protected function _register_controls() {
        $this->start_controls_section(
            'section_prototype', [
                'label' => __('Prototype', DCE_TEXTDOMAIN),
            ]
        );
        // Raw HTML - Display HTML content in the panel
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_raw-html.md
        $this->add_control(
          'html_trototype',
          [
             'type'    => Controls_Manager::RAW_HTML,
             'raw' => __( 'Prototype raggruppa un esempio di tutti i widget elementor, utile allo sviluppo dei futuri.', DCE_TEXTDOMAIN ),
             'content_classes' => 'prototype-class',
          ]
        );
        $this->end_controls_section();
        // ===================================================== TEXT ==============================================
        $this->start_controls_section(
            'section_Text', [
                'label' => __('Prototype TEXT', DCE_TEXTDOMAIN),
            ]
        );

        // Text - Simple text field
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_text.md
        $this->add_control(
          'widget_text',
          [
             'label'       => __( 'Text', DCE_TEXTDOMAIN ),
             'type'        => Controls_Manager::TEXT,
             'default'     => __( 'Default title text', DCE_TEXTDOMAIN ),
             'placeholder' => __( 'Type your title text here', DCE_TEXTDOMAIN ),
             
          ]
        );
        
        // Textarea - Textarea field
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_textarea.md
        $this->add_control(
          'widget_textarea',
          [
             'label'   => __( 'TextArea', DCE_TEXTDOMAIN ),
             'type'    => Controls_Manager::TEXTAREA,
             'default' => __( 'Default description', DCE_TEXTDOMAIN ),
          ]
        );

        // WYSIWYG - The WordPress text editor (TinyMCE)
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_wysiwyg.md
        $this->add_control(
          'widget_TinyMCE',
          [
             'label'   => __( 'TinyMCE WYSIWYG', DCE_TEXTDOMAIN ),
             'type'    => Controls_Manager::WYSIWYG,
             'default' => __( 'Default description', DCE_TEXTDOMAIN ),
          ]
        );
        $this->end_controls_section();
        // ===================================================== NUMBERS 
        $this->start_controls_section(
            'section_numbers', [
                'label' => __('Prototype NUMBERS', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'numbers_heading',
            [
                'label' => __( 'Numbers', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Number - Simple number field
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_number.md
        $this->add_control(
          'widget_number',
          [
             'label'   => __( 'Number', DCE_TEXTDOMAIN ),
             'type'    => Controls_Manager::NUMBER,
             'default' => 10,
             'min'     => 5,
             'max'     => 100,
             'step'    => 5,
             
          ]
        );
        $this->add_responsive_control(
          'widget_responsive_number',
          [
            'label'   => __( 'Responsive Number', DCE_TEXTDOMAIN ),
            'type'    => Controls_Manager::NUMBER,
            
            'default' => 10,
            'tablet_default' => '',
            'mobile_default' => '',
            
            'min'     => 5,
            'max'     => 100,
            'step'    => 5,

            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} .your-element' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'separator'   => 'after'
          ]
        );
        // Slider - A draggable scale to choose between a range of numeric values
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_slider.md
        $this->add_control(
            'widget_slider',
            [
                'label' => __( 'Slider', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .your-element' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'widget_responsive_slider',
            [
                'label' => __( 'Responsive Slider', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'unit' => 'em',
                    'size' => 1
                ],
                'tablet_default' => [
                    'unit' => 'em',
                ],
                'mobile_default' => [
                    'unit' => 'em',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 160,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .your-element' => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'separator'   => 'after'
            ]
        );

         // Dimensions - A component with 4 number fields, suitable for setting top/right/bottom/left settings
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_dimensions.md
        $this->add_control(
          'widget_dimension',
          [
             'label' => __( 'Dimension', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::DIMENSIONS,
             'size_units' => [ 'px', '%', 'em' ],
             'selectors' => [
                    '{{WRAPPER}} .your-class' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
             ],
          ]
        );
        $this->add_control(
          'widget_limit_dimension',
          [
             'label' => __( 'Limited Dimension', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::DIMENSIONS,
             'size_units' => [ 'px', '%', 'em' ],
             'selectors' => [
                    '{{WRAPPER}} .your-class' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
             ],
             'allowed_dimensions' => ['top','bottom']
          ]
        );
        $this->add_responsive_control(
          'widget_responsive_dimension',
          [
             'label' => __( 'Responsive Dimension', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::DIMENSIONS,
             'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'unit' => 'em',
                    'size' => '1'
                ],
                'tablet_default' => [
                    'unit' => 'em',
                ],
                'mobile_default' => [
                    'unit' => 'em',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 160,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .your-element' => 'padding: {{SIZE}}{{UNIT}};',
                ],
          ]
        );
        $this->end_controls_section();
        // ===================================================== MEDIA ==============================================
        $this->start_controls_section(
            'section_media', [
                'label' => __('Prototype MEDIA', DCE_TEXTDOMAIN),
            ]
        );
        // URL - Text field to add link + button to open the link in an external tab (target=_blank)
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_url.md
        $this->add_control(
          'widget_link',
          [
             'label' => __( 'Website URL', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::URL,
             'default' => [
                'url' => 'http://',
                'is_external' => '',
             ],
             'show_external' => true, // Show the 'open in new tab' button.
          ]
        );
        // ----------------------------- MEDIA -------------------------------
        $this->add_control(
            'media_heading',
            [
                'label' => __( 'Media Image', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Media (Image) - Choose an image from the WordPress media library
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_media.md
        $this->add_control(
          'widget_image',
          [
             'label' => __( 'Choose Image', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::MEDIA,
             'default' => [
                'url' => Utils::get_placeholder_image_src(),
             ],
          ]
        );
        // ----------------------------- GALLERY -------------------------------
        $this->add_control(
            'gallery_heading',
            [
                'label' => __( 'Gallery', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Gallery - Create a gallery of images from the WordPress media library
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_gallery.md
        $this->add_control(
          'widget_gallery',
          [
             'label' => __( 'Add Images', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::GALLERY,
          ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'exclude' => [ 'custom' ],
            ]
        );
        // ----------------------------- BACKGROUND -------------------------------
        $this->add_control(
            'background_heading',
            [
                'label' => __( 'Background', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient', 'video' ],
                'fields_options' => [
                    'background' => [
                        'frontend_available' => true,
                    ],
                    'video_link' => [
                        'frontend_available' => true,
                    ],
                ],
            ]
        );
        $this->end_controls_section();
        // ===================================================== CONTROLS 
        $this->start_controls_section(
            'section_controls', [
                'label' => __('Prototype CONTROLS', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'controls_heading',
            [
                'label' => __( 'Controls', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Select - A classic select input
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_select.md
        $this->add_control(
          'widget_select',
          [
             'label'       => __( 'Select', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::SELECT,
             'default' => 'solid',
             'options' => [
                'solid'  => __( 'Solid', DCE_TEXTDOMAIN ),
                'dashed' => __( 'Dashed', DCE_TEXTDOMAIN ),
                'dotted' => __( 'Dotted', DCE_TEXTDOMAIN ),
                'double' => __( 'Double', DCE_TEXTDOMAIN ),
                'none'   => __( 'None', DCE_TEXTDOMAIN ),
             ],
             'selectors' => [ // You can use the selected value in an auto-generated css rule.
                '{{WRAPPER}} .your-element' => 'border-style: {{VALUE}}',
             ],
          ]
        );
        // Switcher - A Switcher control (on/off) - basically a fancy UI representation of a checkbox.
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_switcher.md
        $this->add_control(
            'widget_switcher',
            [
                'label' => __( 'Switcher', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __( 'Show', DCE_TEXTDOMAIN ),
                'label_off' => __( 'Hide', DCE_TEXTDOMAIN ),
                'return_value' => 'yes',
            ]
        );
        // Choose - A component that represents radio buttons as a stylized group of buttons with icons
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_choose.md
        $this->add_control(
            'widget_choose',
            [
                'label' => __( 'Choose', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' => __( 'Left', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
            ]
        );
        // Select2 - A select field based on the select2 plugin.
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_select2.md
        $this->add_control(
          'widget_select2',
          [
             'label' => __( 'Select2', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::SELECT2,
             'options' => [
                'title' => __( 'Title', DCE_TEXTDOMAIN ),
                'description' => __( 'Description', DCE_TEXTDOMAIN ),
                'button' => __( 'Button', DCE_TEXTDOMAIN ),
             ],
             'multiple' => true,
          ]
        );
        // Date-Time picker - A field that opens up a calendar + hours
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_date.md
        $this->add_control(
          'widget_date',
          [
             'label' => __( 'Date', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::DATE_TIME,
          ]
        );
        $this->end_controls_section();
        // ===================================================== TYPOGRAPY 
        $this->start_controls_section(
            'section_typpograpy', [
                'label' => __('Prototype TYPOGRAPY', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'typograpy_heading',
            [
                'label' => __( 'Typography', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Font - Choose a font from the Google font library.
        $this->add_control(
            'widget_fontfamily',
            [
                'label' => __( 'Font Family', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::FONT,
                'default' => "'Open Sans', sans-serif",
                'selectors' => [
                    '{{WRAPPER}} .your-element' =>  'font-family: {{VALUE}}',
                 ],
            ]
        );
        $this->add_group_control(
          Group_Control_Typography::get_type(), [
            'name' => 'widget_typography',
            'label' => __('Typography numeri', DCE_TEXTDOMAIN),
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .your-element',
          ]
        );
        // Text Shadow - Add a shadow effect to a text inside your element.
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_text-shadow.md
        $this->add_group_control(
             Group_Control_Text_Shadow::get_type(),
                [
                    'name'      => 'text_shadow',
                    'selector'  => '{{WRAPPER}} .your-element',
                ]
            );
        $this->end_controls_section();
        // ===================================================== DESIGN 
        $this->start_controls_section(
            'section_design ', [
                'label' => __('Prototype DESIGN', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'color_heading',
            [
                'label' => __( 'Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Color - A Color-Picker control with an alpha slider. Includes a customizable color palette that can be preset by the user
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_color.md
        $this->add_control(
            'widget_color',
            [
                'label' => __( 'Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .your-element' => 'color: {{VALUE}}',
                ],
            ]
        );
         $this->add_control(
            'icons_heading',
            [
                'label' => __( 'Icons', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Icon - Choose icon within the font-awesome library.
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_icon.md
        $this->add_control(
            'widget_all_icon',
            [
                'label' => __( 'Icons', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::ICON,
            ]
        );
        $this->add_control(
            'widget_social_icon',
            [
                'label' => __( 'Social Icon', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::ICON,
                'include' => [
                    'fa fa-facebook',
                    'fa fa-flickr',
                    'fa fa-google-plus',
                    'fa fa-instagram',
                    'fa fa-linkedin',
                    'fa fa-pinterest',
                    'fa fa-reddit',
                    'fa fa-twitch',
                    'fa fa-twitter',
                    'fa fa-vimeo',
                    'fa fa-youtube',
                ],
            ]
        );
        $this->add_control(
            'border_heading',
            [
                'label' => __( 'Border', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'widget_border',
                'label' => __('Borders', DCE_TEXTDOMAIN),
                'selector' => '{{WRAPPER}} .your-element',
                'condition' => [
                    'use_bg' => '0',
                ],
            ]
        );
        $this->add_control(
            'boxshadow_heading',
            [
                'label' => __( 'Box Shadow', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
          'box_shadow',
            [
                'label' => __( 'Box Shadow', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::BOX_SHADOW,
                'default' => [
                    'color' => '',
                    'blur'  => '',

                ],
                /*'exclude' => [
                    'box_shadow_position',
                ],*/
                'selectors' => [
                    '{{WRAPPER}} .your-element' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow_hover',
                'selector' => '{{WRAPPER}} .your-element',
            ]
        );
        $this->end_controls_section();
        // ===================================================== CODE 
        $this->start_controls_section(
            'section_code ', [
                'label' => __('Prototype CODE', DCE_TEXTDOMAIN),
            ]
        );

        $this->add_control(
            'code_html_heading',
            [
                'label' => __( 'HTML Code', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
          'custom_html',
          [
             'label'   => __( 'Custom HTML', DCE_TEXTDOMAIN ),
             'type'    => Controls_Manager::CODE,
             'language' => 'html',
          ]
        );
        /*$this->add_control(
            'code_css_heading',
            [
                'label' => __( 'CSS Code', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
         $this->add_control(
          'custom_css',
          [
             'label'   => __( 'Custom CSS', DCE_TEXTDOMAIN ),
             'type'    => Controls_Manager::CODE,
             'language' => 'css',
          ]
        );*/
        $this->add_control(
            'code_javascript_heading',
            [
                'label' => __( 'JS Code', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
          'custom_javascript',
          [
             'label'   => __( 'Custom JS', DCE_TEXTDOMAIN ),
             'type'    => Controls_Manager::CODE,
             'language' => 'javascript',
          ]
        );
         /*$this->add_control(
            'code_php_heading',
            [
                'label' => __( 'JS Code', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
          'custom_php',
          [
             'label'   => __( 'Custom PHP', DCE_TEXTDOMAIN ),
             'type'    => Controls_Manager::CODE,
             'language' => 'php',
          ]
        );*/
        // Code Editor - Ace Code editor - This includes syntax highlighting for HTML/CSS/JavaScript and other programming languages.



        $this->end_controls_section();
        // ===================================================== REPEATER 
        $this->start_controls_section(
            'section_repeater ', [
                'label' => __('Prototype REPEATER', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'repeater_heading',
            [
                'label' => __( 'Repeater', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Repeater - Repeater controls allow you to build repeatable blocks of fields. You can create for example a set of fields that will contain a checkbox and a textfield. The user will then be able to add “rows”, and each row will contain a checkbox and a textfield.
        // https://github.com/pojome/elementor/blob/master/docs/content/controls/_repeater.md
        $this->add_control(
            'prototype_tabs',
            [
                'label' => __( 'Repeater List', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'list_title' => __( 'Title #1', DCE_TEXTDOMAIN ),
                        'list_content' => __( 'Item content. Click the edit button to change this text.', DCE_TEXTDOMAIN ),
                    ],
                    [
                        'list_title' => __( 'Title #2', DCE_TEXTDOMAIN ),
                        'list_content' => __( 'Item content. Click the edit button to change this text.', DCE_TEXTDOMAIN ),
                    ],
                ],
                'fields' => [
                    [
                        'name' => 'list_title',
                        'label' => __( 'Title', DCE_TEXTDOMAIN ),
                        'type' => Controls_Manager::TEXT,
                        'default' => __( 'List Title' , DCE_TEXTDOMAIN ),
                        'label_block' => true,
                    ],
                    [
                        'name' => 'list_content',
                        'label' => __( 'Content', DCE_TEXTDOMAIN ),
                        'type' => Controls_Manager::WYSIWYG,
                        'default' => __( 'List Content' , DCE_TEXTDOMAIN ),
                        'show_label' => false,
                    ],
                ],
                'title_field' => '{{{ list_title }}}',
            ]
        );

        // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

        $repeater = new Repeater();

        $repeater->start_controls_tabs('tabs_repeater');

        $repeater->start_controls_tab('tab_content', [ 'label' => __('Item', DCE_TEXTDOMAIN)]);
        
        $repeater->end_controls_tab();
        $repeater->start_controls_tab('tab_style', [ 'label' => __('Style', DCE_TEXTDOMAIN)]);       

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'prototype_tabs_2', [
                'label' => __('Tabs Repeater', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                ],
                'fields' => array_values($repeater->get_controls()),
                'title_field' => 'Item'
            ]
        );
        $this->end_controls_section();
        // ===================================================== TABS 
        $this->start_controls_section(
            'section_tabs',
            [
                'label' => __( 'Tabs Prototype', DCE_TEXTDOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_prototype' );

        $this->start_controls_tab(
            "xxxxx",
            [
                'label' => __('xxxxx', DCE_TEXTDOMAIN),
            ]
        );

        $this->end_controls_tab();
        
        $this->start_controls_tab(
            "yyyyy",
            [
                'label' => __('yyyyy', DCE_TEXTDOMAIN),
            ]
        );

        $this->end_controls_tab();
        

        $this->end_controls_tabs();
        
        $this->end_controls_section();

        // ===================================================== Elementor GROUPS 
        $this->start_controls_section(
            'section_elementorGroups',
            [
                'label' => __( 'Elementor Groups fields', DCE_TEXTDOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        // Box Shadow - Add a shadow effect to your element.

        // Entrance Animation - Choose an entrance animation type from a list of animations.

        // Hover Animation - Choose a hover animation type from a list of animations. (coming soon)


        $this->end_controls_section();
        // ===================================================== DCE GROUPS 
        $this->start_controls_section(
            'section_dceGroups',
            [
                'label' => __( 'DCE Groups fields', DCE_TEXTDOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        // Outline
        $this->add_group_control(
          Group_Control_Outline::get_type(),
          [
            'name' => 'widget_outline',
            'label' => 'Outline',
            'selector' => '{{WRAPPER}} .your-element',
          ]
        );
        // Effects
        $this->add_group_control(
          DCE_Group_Control_Filters_CSS::get_type(),
          [
            'name' => 'widget_filters',
            'description' => 'DynamicContentForElementor FILTERS',
            'label' => 'Filters',
            'selector' => '{{WRAPPER}} .your-element',
          ]
        );
        $this->add_group_control(
          Group_Control_Css_Filter::get_type(),
          [
            'name' => 'css_filters',
            'description' => 'ELEMENTOR native FILTERS',
            'selector' => '{{WRAPPER}} iframe',
          ]
        );
        // Transform
        $this->add_group_control(
          DCE_Group_Control_Transform_Element::get_type(),
          [
            'name' => 'transform_transform',
            'label' => 'Transform',
            'selector' => '{{WRAPPER}} .your-element',
          ]
        );

        $this->end_controls_section();
        
        // ALTRO ..........
        // Separator - (Not really a control) Display a separator between fields
        $this->add_control(
          'hr',
          [
            'type' => \Elementor\Controls_Manager::DIVIDER,
            'style' => 'thick',
          ]
        );

    }

    protected function render() {}

    protected function _content_template() {}
    
    final public function update_settings( $key, $value = null ) {
        $widget_id = $this->get_id();
        DCE_Helper::set_settings_by_id($widget_id, $key, $value);
        
        $this->set_settings($key, $value);
    }

}
