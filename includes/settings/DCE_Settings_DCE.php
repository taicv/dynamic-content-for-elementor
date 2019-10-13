<?php

namespace DynamicContentForElementor\Includes\Settings;

use Elementor\Controls_Manager;
use Elementor\Core\Settings\General\Model as GeneralModel;
use Elementor\Scheme_Color;
use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Model extends GeneralModel {

    public function get_name() {
        return 'dce-settings_dce';
    }

    public function get_css_wrapper_selector() {
        return '';
    }

    public function get_panel_page_settings() {
        return [
            'title' => __('Dynamic Content', DCE_TEXTDOMAIN),
            'menu' => [
                'icon' => 'icon-dyn-logo-dce',
                'beforeItem' => 'elementor-settings',
            ],
        ];
    }

    public static function get_controls_list() {

        return [
            Manager::PANEL_TAB_SETTINGS => [
                /* 'settings_barbajs' => [
                  'label' => __( 'Barba', DCE_TEXTDOMAIN ),
                  'controls' => [
                  'dce_barba_note' => [
                  'type' 				=> Controls_Manager::RAW_HTML,
                  'raw' 				=> __( 'Barba.js.', DCE_TEXTDOMAIN ),
                  'content_classes' 	=> '',
                  ],
                  'enable_barbajs' => [
                  'label' => __('Enable Barbajs', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::SWITCHER,
                  'label_off' => __('Yes', DCE_TEXTDOMAIN),
                  'label_on' => __('No', DCE_TEXTDOMAIN),
                  'return_value' => 'yes',
                  'default' => '',
                  'frontend_available' => true
                  ],
                  'barbajs_duration' => [
                  'label' 		=> __( 'Duration', DCE_TEXTDOMAIN ),
                  'type' 			=> Controls_Manager::SLIDER,
                  'default' 	=> [
                  'size' 	=> 0.2,
                  ],
                  'range' 	=> [
                  'px' 	=> [
                  'min' 	=> 0,
                  'max' 	=> 2,
                  'step'	=> 0.1,
                  ],
                  ],
                  'condition' => [
                  'enable_barbajs' => 'yes'
                  ],
                  'frontend_available' => true
                  ],

                  ]
                  ], */
                // SWUP
                'settings_animsition' => [
                    'label' => __('Smooth navigation', DCE_TEXTDOMAIN),
                    'controls' => [
                        'dce_swup_note' => [
                            'type' => Controls_Manager::RAW_HTML,
                            'raw' => __('<div><i class="icon-dyn-logo-dce" style="font-size: 8em;text-align: center;display: block;"></i></div>', DCE_TEXTDOMAIN),
                            'content_classes' => '',
                        ],
                        'id_wrapper' => [
                            'label' => __('Wrapper ID', DCE_TEXTDOMAIN),
                            'type' => Controls_Manager::TEXT,
                            'default' => '',
                            'placeholder' => 'Write ID...',
                            'frontend_available' => true,
                            'separator' => 'before'
                        ],
                        /* 'header_site' => [
                          'label' => __('Header ID', DCE_TEXTDOMAIN),
                          'type' => Controls_Manager::TEXT,
                          'default' => '',
                          'placeholder' => 'Write ID of header...',
                          'frontend_available' => true
                          ],
                          'main_site' => [
                          'label' => __('Main ID', DCE_TEXTDOMAIN),
                          'type' => Controls_Manager::TEXT,
                          'default' => '',
                          'placeholder' => 'Write ID of main...',
                          'frontend_available' => true
                          ],
                          'footer_site' => [
                          'label' => __('Footer ID', DCE_TEXTDOMAIN),
                          'type' => Controls_Manager::TEXT,
                          'default' => '',
                          'placeholder' => 'Write ID of footer...',
                          'frontend_available' => true
                          ], */
                        'enable_swup' => [
                            'label' => __('Enable Swup', DCE_TEXTDOMAIN),
                            'type' => Controls_Manager::SWITCHER,
                            'label_off' => __('No', DCE_TEXTDOMAIN),
                            'label_on' => __('Yes', DCE_TEXTDOMAIN),
                            'return_value' => 'yes',
                            'default' => '',
                            'frontend_available' => true
                        ],
                        'a_class' => [
                            'label' => __('A class CLASS', DCE_TEXTDOMAIN),
                            'type' => Controls_Manager::TEXT,
                            'label_block' => true,
                            'row' => 3,
                            'default' => 'a:not([target="_blank"]):not([href^="#"]):not([href^="mailto"]):not([href^="tel"]):not(.gallery-lightbox):not(.elementor-clickable):not(.oceanwp-lightbox)',
                            'placeholder' => 'a:not([target="_blank"]):not([href^="#"]):not([href^="mailto"]):not([href^="tel"]):not(.gallery-lightbox):not(.elementor-clickable):not(.oceanwp-lightbox)',
                            'frontend_available' => true,
                            'separator' => 'after',
                            'condition' => [
                                'enable_swup' => 'yes'
                            ],
                        ],
                        'swup_duration' => [
                            'label' => __('Duration', DCE_TEXTDOMAIN),
                            'type' => Controls_Manager::SLIDER,
                            'default' => [
                                'size' => 0.2,
                            ],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 2,
                                    'step' => 0.1,
                                ],
                            ],
                            'condition' => [
                                'enable_swup' => 'yes'
                            ],
                            'frontend_available' => true
                        ],
                    ]
                ]
            // ANIMSITION
            /* 'settings_animsition' => [
              'label' => __( 'Animsition', DCE_TEXTDOMAIN ),
              'controls' => [
              'dce_animsition_note' => [
              'type' 				=> Controls_Manager::RAW_HTML,
              'raw' 				=> __( 'Animsition.js.', DCE_TEXTDOMAIN ),
              'content_classes' 	=> '',
              ],
              'enable_animsition' => [
              'label' => __('Enable Animsition', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'label_off' => __('Yes', DCE_TEXTDOMAIN),
              'label_on' => __('No', DCE_TEXTDOMAIN),
              'return_value' => 'yes',
              'default' => '',
              'frontend_available' => true
              ],
              'animsition_duration' => [
              'label' 		=> __( 'Duration', DCE_TEXTDOMAIN ),
              'type' 			=> Controls_Manager::SLIDER,
              'default' 	=> [
              'size' 	=> 0.2,
              ],
              'range' 	=> [
              'px' 	=> [
              'min' 	=> 0,
              'max' 	=> 2,
              'step'	=> 0.1,
              ],
              ],
              'condition' => [
              'enable_animsition' => 'yes'
              ],
              'frontend_available' => true
              ],
              ]
              ] */

            // TEMPLATE SYSTEM
            /* 'settings_templateSystem' => [
              'label' => __( 'Template System', DCE_TEXTDOMAIN ),
              'controls' => [
              'dce_templateSystem_note' => [
              'type' 				=> Controls_Manager::RAW_HTML,
              'raw' 				=> __( '<div><i class="icon-dyn-logo-dce" style="font-size: 8em;text-align: center;display: block;"></i></div>', DCE_TEXTDOMAIN ),
              'content_classes' 	=> '',
              ],





              'animsition_duration' => [
              'label' 		=> __( 'Duration', DCE_TEXTDOMAIN ),
              'type' 			=> Controls_Manager::SLIDER,
              'default' 	=> [
              'size' 	=> 0.2,
              ],
              'range' 	=> [
              'px' 	=> [
              'min' 	=> 0,
              'max' 	=> 2,
              'step'	=> 0.1,
              ],
              ],
              'condition' => [
              'enable_animsition' => 'yes'
              ],
              'frontend_available' => true
              ],
              ]
              ],






              'settings_templateSystem_types' => [
              'label' => __( 'Types', DCE_TEXTDOMAIN ),
              'controls' => [

              'dce_templateSystem_heading_types_posts' => [
              'label' => __('Posts', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],
              'dce_templateSystem_posts_template' => [
              'label' => '',
              'type' => Controls_Manager::CHOOSE,
              'options' => [
              'singlepost' => [
              'title' => __('Single Post', DCE_TEXTDOMAIN),
              'icon' => 'eicon-image-box',
              ],
              'archive' => [
              'title' => __('Archive', DCE_TEXTDOMAIN),
              'icon' => 'eicon-post-list',
              ],
              'archive_beforeafter' => [
              'title' => __('Archive Before/After', DCE_TEXTDOMAIN),
              'icon' => 'eicon-accordion',
              ],

              ],
              'default' => '',
              ],





              'dce_templateSystem_posts_archive_heading' => [
              'label' => __('Archive', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              'condition' => [
              'dce_templateSystem_posts_template' => 'archive',
              ],
              ],
              'dce_templateSystem_posts_archive_template' => [
              'label' => __('Template', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SELECT2,
              'options' => DCE_Helper::get_all_template(),

              'default' => '0',
              'condition' => [
              'dce_templateSystem_posts_template' => 'archive',
              ],
              ],

              'dce_templateSystem_posts_archive_columns' => [
              'label' => __('Columns', DCE_TEXTDOMAIN),
              'responsive' => true,
              'type' => Controls_Manager::SELECT,
              'default' => '3',
              'options' => [
              '12' => '1',
              '6' => '2',
              '4' => '3',
              '3' => '4',
              '2' => '6',
              ],
              'condition' => [
              'dce_templateSystem_posts_template' => 'archive',
              'dce_templateSystem_posts_archive_template!' => ['','0'],
              'dce_templateSystem_posts_archive_canvas' => ''
              ],

              ],
              'dce_templateSystem_posts_archive_layout' => [
              'label' => __('Layout Boxed/Full', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'label_off' => __('Full', DCE_TEXTDOMAIN),
              'label_on' => __('Boxed', DCE_TEXTDOMAIN),
              'return_value' => 'full',
              'default' => '',
              'condition' => [
              'dce_templateSystem_posts_template' => 'archive',
              'dce_templateSystem_posts_archive_template!' => ['','0'],
              'dce_templateSystem_posts_archive_canvas' => ''
              ],
              ],
              'dce_templateSystem_posts_archive_canvas' => [
              'label' => __('Canvas Layout', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'label_off' => __('No', DCE_TEXTDOMAIN),
              'label_on' => __('Yes', DCE_TEXTDOMAIN),
              'return_value' => 'yes',
              'default' => '',
              'condition' => [
              'dce_templateSystem_posts_template' => 'archive',
              'dce_templateSystem_posts_archive_template!' => ['','0'],
              ],
              ],





              'dce_templateSystem_posts_singepost_heading' => [
              'label' => __('Single Post', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              'condition' => [
              'dce_templateSystem_posts_template' => 'singlepost',
              ],
              ],
              'dce_templateSystem_posts_singlepost' => [
              'label' => __('Template', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SELECT2,
              'options' => DCE_Helper::get_all_template(),

              'default' => '0',
              'condition' => [
              'dce_templateSystem_posts_template' => 'singlepost',
              ],
              ],
              'dce_templateSystem_posts_singlepost_layout' => [
              'label' => __('Blank template', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'label_off' => __('Default', DCE_TEXTDOMAIN),
              'label_on' => __('Yes', DCE_TEXTDOMAIN),
              'return_value' => 'yes',
              'default' => '',
              'condition' => [
              'dce_templateSystem_posts_template' => 'singlepost',

              ],
              ],







              'dce_templateSystem_posts_beforeafter_heading' => [
              'label' => __('Archive Before/After', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              'condition' => [
              'dce_templateSystem_posts_template' => 'archive_beforeafter',
              ],
              ],
              'dce_templateSystem_posts_beforeArchive' => [
              'label' => __('Before Template', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SELECT2,
              'options' => DCE_Helper::get_all_template(),

              'default' => '0',
              'condition' => [
              'dce_templateSystem_posts_template' => 'archive_beforeafter',
              ],
              ],
              'dce_templateSystem_posts_afterArchive' => [
              'label' => __('After Template', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SELECT2,
              'options' => DCE_Helper::get_all_template(),

              'default' => '0',
              'condition' => [
              'dce_templateSystem_posts_template' => 'archive_beforeafter',
              ],
              ],


              'dce_templateSystem_heading_types_archive_hr' => [
              'type' => Controls_Manager::DIVIDER,
              ],


              // --------------------------------------------------------------------------------------
              'dce_templateSystem_heading_types_pages' => [
              'label' => __('Pages', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],

              'dce_templateSystem_heading_types_pages_hr' => [
              'type' => Controls_Manager::DIVIDER,
              ],


              // --------------------------------------------------------------------------------------
              'dce_templateSystem_heading_types_cpt' => [
              'label' => __('CPT..', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],


              ],

              ], */







            /* 'settings_templateSystem_otherPages' => [
              'label' => __( 'Other Pages', DCE_TEXTDOMAIN ),
              'controls' => [


              'dce_templateSystem_heading_users' => [
              'label' => __('Users', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],
              'dce_templateSystem_heading_media' => [
              'label' => __('Media attachmets', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],
              'dce_templateSystem_heading_search' => [
              'label' => __('Search', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],
              'dce_templateSystem_heading_404' => [
              'label' => __('404', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],
              ],
              ], */







            /* 'settings_templateSystem_taxonomy' => [
              'label' => __( 'Taxominies', DCE_TEXTDOMAIN ),
              'controls' => [

              'dce_templateSystem_heading_categories' => [
              'label' => __('Categories', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],
              'dce_templateSystem_heading_tags' => [
              'label' => __('Tags', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],
              'dce_templateSystem_heading_taxonomy' => [
              'label' => __('Taxonomy ...', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              ],
              ],
              ] */
            ],
                /* Controls_Manager::TAB_STYLE => [

                  ], */
        ];
    }

    protected function _register_controls() {
        $controls_list = self::get_controls_list();

        foreach ($controls_list as $tab_name => $sections) {

            foreach ($sections as $section_name => $section_data) {

                $this->start_controls_section(
                        $section_name, [
                    'label' => $section_data['label'],
                    'tab' => $tab_name,
                        ]
                );

                foreach ($section_data['controls'] as $control_name => $control_data) {
                    $this->add_control($control_name, $control_data);
                }

                $this->end_controls_section();
            }
        }
    }

}
