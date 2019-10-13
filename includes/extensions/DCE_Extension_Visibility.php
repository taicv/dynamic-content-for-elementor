<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Visibility extenstion
 *
 * Conditional Visibility Widgets & Rows/Sections
 *
 * @since 1.0.1
 */
class DCE_Extension_Visibility extends DCE_Extension_Prototype {

    public $name = 'Visibility';
    public $common_sections_actions = array(
        array(
            'element' => 'common',
            'action' => '_section_style',
        ),
        array(
            'element' => 'section',
            'action' => 'section_advanced',
        )
    );
    public static $tabs = ['user' => 'User & Role', 'device' => 'Device & Browser', 'datetime' => 'Date & Time', 'post' => 'Current Post', 'context' => 'Context', 'tags' => 'Conditional Tags', 'random' => 'Random', 'custom' => 'Custom condition', 'v2' => 'V2 Backwards compatibility', 'repeater' => 'Advanced', 'fallback' => 'Fallback'];
    public static $triggers = array(
        'user' => array(
            'label' => 'User & Role',
            'options' => array(
                'role',
                'users',
                'usermeta',
            ),
        ),
        'device' => array(
            'label' => 'Device & Browser',
            'options' => array(
                'browser',
                'responsive',
            ),
        ),
        'post' => array(
            'label' => 'Current Post',
            'options' => array(
                'leaf',
                'parent',
                'node',
                'root',
            ),
        ),
    );

    /**
     * The description of the current extension
     *
     * @since 0.5.4
     * */
    public static function get_description() {
        return __('Visibility rules for Widgets and Rows');
    }

    /**
     * Add Actions
     *
     * @since 0.5.5
     *
     * @access private
     */
    protected function add_actions() {

        // Activate controls for widgets
        add_action('elementor/element/common/dce_section_visibility_advanced/before_section_end', function( $element, $args ) {
            $this->add_controls($element, $args);
        }, 10, 2);
        foreach (self::$tabs as $tkey => $tvalue) {
            // Activate controls for widgets
            add_action('elementor/element/common/dce_section_visibility_' . $tkey . '/before_section_end', function( $element, $args ) use ($tkey) {
                $args['section'] = $tkey;
                $this->add_controls($element, $args);
            }, 10, 2);
        }


        //add_filter('elementor/widget/print_template', array($this, 'visibility_print_widget'), 10, 2);
        add_filter('elementor/widget/render_content', array($this, 'visibility_render_widget'), 10, 2);
        add_action("elementor/frontend/widget/before_render", function( $element ) {
            $settings = $element->get_settings_for_display();
            if (isset($settings['enabled_visibility']) && $settings['enabled_visibility']) {
                if ($this->is_hidden($element)) {
                    echo '<!--DCE VISIBILITY HIDDEN WIDGET-->';
                    if (!isset($settings['dce_visibility_debug']) || !$settings['dce_visibility_debug']) {
                        if (!isset($settings['dce_visibility_fallback']) || !$settings['dce_visibility_fallback']) {
                            $element->add_render_attribute('_wrapper', 'class', 'dce-visibility-widget-hidden');
                        }
                    } else {
                        $element->add_render_attribute('_wrapper', 'class', 'dce-visibility-widget-hidden-debug');
                    }
                }
            }
        }, 10, 1);

        // Activate controls for sections
        add_action('elementor/element/section/dce_section_visibility_advanced/before_section_end', function( $element, $args ) {
            $this->add_controls($element, $args);
        }, 10, 2);
        foreach (self::$tabs as $tkey => $tvalue) {
            // Activate controls for widgets
            add_action('elementor/element/section/dce_section_visibility_' . $tkey . '/before_section_end', function( $element, $args ) use ($tkey) {
                $args['section'] = $tkey;
                $this->add_controls($element, $args);
            }, 10, 2);
        }
        add_action('elementor/frontend/section/before_render', function( $element ) {
            $element_type = $element->get_type();
            $element_name = $element->get_unique_name();
            $element_id = $element->get_id();
            $settings = $element->get_settings_for_display();
            if (isset($settings['enabled_visibility']) && $settings['enabled_visibility']) {
                //var_dump($this->is_hidden($settings));
                if ($this->is_hidden($element)) {
                    //$fallback = $this->get_fallback($settings, $element);
                    //if (!$fallback) {
                    //$element->add_render_attribute('_wrapper', 'class', 'dce-visibility-section-hidden');
                    //}
                }
            }
        }, 10, 1);


        // filter sections
        add_action("elementor/frontend/section/before_render", function( $element ) {
            $settings = $element->get_settings_for_display();
            if (isset($settings['enabled_visibility']) && $settings['enabled_visibility']) {
                $hidden = $this->is_hidden($element);
                if ($hidden) {
                    echo '<!--DCE VISIBILITY HIDDEN SECTION START-->';
                    if (!isset($settings['dce_visibility_dom']) || !$settings['dce_visibility_dom']) {
                        ob_start();
                    } else {
                        $element->add_render_attribute('_wrapper', 'class', 'dce-visibility-section-hidden');
                        $element->add_render_attribute('_wrapper', 'class', 'dce-visibility-original-content');
                    }
                } 
                $this->set_element_view_counters($element, $hidden);
            }
        }, 10, 1);
        add_action("elementor/frontend/section/after_render", function( $element ) {
            $settings = $element->get_settings_for_display();
            $content = '';
            if (isset($settings['enabled_visibility']) && $settings['enabled_visibility']) {
                if ($this->is_hidden($element)) {
                    if (!isset($settings['dce_visibility_dom']) || !$settings['dce_visibility_dom']) {
                        $content = ob_get_contents();
                        ob_end_clean();
                    }
                    $this->print_conditions($element);
                    $fallback = $this->get_fallback($settings, $element);
                    if ($fallback) {
                        $fallback = str_replace('dce-visibility-section-hidden', '', $fallback);
                        $fallback = str_replace('dce-visibility-original-content', 'dce-visibility-fallback-content', $fallback);
                        echo $fallback;
                    }
                    echo '<!--DCE VISIBILITY HIDDEN SECTION END-->';
                }
            }
        }, 10, 1);

        // filter columns
        //addAction( "elementor/frontend/column/before_render", 'filterSectionContentBefore', 10, 1 );
        //addAction( "elementor/frontend/column/after_render", 'filterSectionContentAfter', 10, 1 );
    }

    /**
     * Add Controls
     *
     * @since 0.5.5
     *
     * @access private
     */
    private function add_controls($element, $args) {

        $user_metas = array();
        $post_metas = array();
        $roles = array();
        $post_types = array();
        $all_posts = array();
        $taxonomies = array();
        $taxonomies_terms = array();
        $all_taxonomies_terms = array();
        $templates = array();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $post_metas = DCE_Helper::get_post_metas();
            $user_metas = DCE_Helper::get_user_metas();
            $roles = DCE_Helper::get_roles(false);
            $post_types = DCE_Helper::get_post_types();
            $all_posts = DCE_Helper::get_all_posts();
            $taxonomies = DCE_Helper::get_taxonomies();
            foreach ($taxonomies as $tkey => $atax) {
                $taxonomies_terms[$tkey] = DCE_Helper::get_taxonomy_terms($tkey);
            }
            $all_taxonomies_terms = DCE_Helper::get_taxonomy_terms(null, true);
            $templates = DCE_Helper::get_all_template();
        }



        /* \Elementor\Controls_Manager::add_tab(
          'dce-visibility',
          __( 'Visibility', DCE_TEXTDOMAIN )
          ); */
        //var_dump($args); die();

        if (isset($args['section'])) {
            $section = $args['section'];
        } else {
            $section = 'advanced';
        }

        $element_type = $element->get_type();

        /* $element->start_controls_section(
          'visibility_section',
          [
          'label' => __( 'Visibility', DCE_TEXTDOMAIN ),
          'tab' => 'dce-visibility',
          ]
          ); */

        if ($section == 'advanced') {

            $element->add_control(
                    'enabled_visibility', [
                'label' => __('Enable Visibility', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                    ]
            );

            $element->add_control(
                    'dce_visibility_hidden', [
                'label' => __('HIDE this element', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                //'label_on' => __('Hide', DCE_TEXTDOMAIN),
                //'label_off' => __('Show', DCE_TEXTDOMAIN),
                'description' => __('Hide the element on the frontend until it is enabled', DCE_TEXTDOMAIN),
                'condition' => [
                    'enabled_visibility' => 'yes',
                ],
                'separator' => 'before',
                    ]
            );

            $element->add_control(
                    'dce_visibility_dom', [
                'label' => __('Keep HTML', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'description' => __('Keep the element HTML in the DOM.', DCE_TEXTDOMAIN) . '<br>' . __('Only hide this element via CSS.', DCE_TEXTDOMAIN),
                'condition' => [
                    'enabled_visibility' => 'yes',
                ],
                    ]
            );

            if (defined('DVE_PLUGIN_BASE') || true) {
                $element->add_control(
                        'dce_visibility_mode', [
                    'label' => __('Composition mode', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HIDDEN,
                    'default' => 'quick',
                        ]
                );
            } else {
                /*
                  $element->add_control(
                  'dce_visibility_mode', [
                  'label' => __('Composition mode', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::CHOOSE,
                  'options' => [
                  'quick' => [
                  'title' => __('Quick', DCE_TEXTDOMAIN),
                  'icon' => 'fa fa-bolt',
                  ],
                  'advanced' => [
                  'title' => __('Advanced', DCE_TEXTDOMAIN),
                  'icon' => 'fa fa-list-ol',
                  ]
                  ],
                  'default' => 'quick',
                  'description' => __('Quickly set a trigger or create a complex expression in Advanced mode.', DCE_TEXTDOMAIN),
                  'toggle' => false,
                  'condition' => [
                  'enabled_visibility' => 'yes',
                  'dce_visibility_hidden' => '',
                  ],
                  ]
                  );
                 *
                 */
            }

            $element->add_control(
                    'dce_visibility_selected', [
                'label' => __('Display mode', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', DCE_TEXTDOMAIN),
                'label_off' => __('Hide', DCE_TEXTDOMAIN),
                'description' => __('Hide or Show element when a condition is triggered.', DCE_TEXTDOMAIN),
                'default' => 'yes',
                'condition' => [
                    'enabled_visibility' => 'yes',
                    'dce_visibility_hidden' => '',
                ],
                    ]
            );



            if (WP_DEBUG) {
                $element->add_control(
                        'dce_visibility_debug', [
                    'label' => __('DEBUG', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Enable to get a report of triggered rule which hide element in frontend.<br>WP_DEBUG must be active.', DCE_TEXTDOMAIN),
                    'separator' => 'before',
                    'condition' => [
                        'enabled_visibility' => 'yes',
                        'dce_visibility_hidden' => '',
                    //'dce_visibility_selected' => '',
                    ],
                        ]
                );
            }

            if (defined('DVE_PLUGIN_BASE')) {
                $element->add_control(
                        'dce_visibility_review', [
                    'label' => '<b>' . __('Enjoyed Visibility extension?', DCE_TEXTDOMAIN) . '</b>',
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => __('Please leave us a', DCE_TEXTDOMAIN)
                    . ' <a target="_blank" href="https://wordpress.org/support/plugin/dynamic-visibility-for-elementor/reviews/?filter=5/#new-post">★★★★★</a> '
                    . __('rating.<br>We really appreciate your support!', DCE_TEXTDOMAIN),
                    'separator' => 'before',
                        ]
                );
            }
        }

        if ($section == 'v2') {
            if (false) {
                $ctype = Controls_Manager::HIDDEN;
            } else {
                $ctype = Controls_Manager::SWITCHER;
                $element->add_control(
                        'dce_visibility_v2_notice', [
                    'label' => __('<b>WARNING</b>', DCE_TEXTDOMAIN),
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => __('<b>If you updated from V2</b> set all to Yes and manage it from main "Display Mode", otherwise ignore this control section', DCE_TEXTDOMAIN),
                        ]
                );
            }
            $element->add_control(
                    'dce_visibility_user_selected', [
                'label' => __('User Show/Hide', DCE_TEXTDOMAIN),
                //'type' => Controls_Manager::HIDDEN,
                'type' => $ctype,
                'default' => 'yes',
                    ]
            );
            $element->add_control(
                    'dce_visibility_device_selected', [
                'label' => __('Device Show/Hide', DCE_TEXTDOMAIN),
                //'type' => Controls_Manager::HIDDEN,
                'type' => $ctype,
                'default' => 'yes',
                    ]
            );
            $element->add_control(
                    'dce_visibility_datetime_selected', [
                'label' => __('DateTime Show/Hide', DCE_TEXTDOMAIN),
                //'type' => Controls_Manager::HIDDEN,
                'type' => $ctype,
                'default' => 'yes',
                    ]
            );
            $element->add_control(
                    'dce_visibility_context_selected', [
                'label' => __('Context Show/Hide', DCE_TEXTDOMAIN),
                //'type' => Controls_Manager::HIDDEN,
                'type' => $ctype,
                'default' => 'yes',
                    ]
            );
            $element->add_control(
                    'dce_visibility_tags_selected', [
                'label' => __('Tags Show/Hide', DCE_TEXTDOMAIN),
                //'type' => Controls_Manager::HIDDEN,
                'type' => $ctype,
                'default' => 'yes',
                    ]
            );
            $element->add_control(
                    'dce_visibility_custom_condition_selected', [
                'label' => __('Custom Show/Hide', DCE_TEXTDOMAIN),
                //'type' => Controls_Manager::HIDDEN,
                'type' => $ctype,
                'default' => 'yes',
                    ]
            );
        }


        if ($section == 'user') {
            /* $element->start_controls_section(
              'section_visibility_user', [
              'label' => __('User & Roles', DCE_TEXTDOMAIN),
              'condition' => [
              'enabled_visibility' => 'yes',
              'dce_visibility_hidden' => '',
              ],
              ]
              ); */
            /* $element->add_control(
              'role_visibility_heading', [
              'label' => __('Users & Roles', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              'condition' => [
              'enabled_visibility' => 'yes',
              'dce_visibility_hidden' => '',
              ],
              'separator' => 'before',
              ]
              ); */

            /* $element->add_control(
              'dce_visibility_everyone', [
              'label' => __('Visible by EveryONE', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'description' => __('If enabled every user, also visitors, can view the element', DCE_TEXTDOMAIN),
              ]
              ); */


            $roles = array_reverse($roles, true);
            //$roles['users'] = 'Selected User';
            $roles['visitor'] = 'Visitor (non logged User)';
            $roles = array_reverse($roles, true);

            $element->add_control(
                    'dce_visibility_role', [
                'label' => __('Roles', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                'options' => $roles,
                //'default' => 'everyone',
                'description' => __('If you want limit visualization to specific user roles', DCE_TEXTDOMAIN),
                'multiple' => true,
                    /* 'condition' => [
                      'dce_visibility_everyone' => '',
                      ], */
                    ]
            );
            $element->add_control(
                    'dce_visibility_users', [
                'label' => __('Selected Users', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'description' => __('Write here the list of user who will be able to view (or not) this element.<br>You can use their ID, email or username.<br>Simply separate them by a comma. (ex. "23, info@dynamic.ooo, dynamicooo")', DCE_TEXTDOMAIN),
                    /* 'condition' => [
                      'dce_visibility_role' => 'users',
                      //'dce_visibility_everyone' => '',
                      ], */
                    ]
            );

            $element->add_control(
                    'dce_visibility_usermeta', [
                'label' => __('User Meta', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                'options' => $user_metas,
                'description' => __('Triggered by a selected User Meta value', DCE_TEXTDOMAIN),
                    ]
            );
            $element->add_control(
                    'dce_visibility_usermeta_status', [
                'label' => __('User Meta Status', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'not' => [
                        'title' => __('Not isset or empty', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-circle-o',
                    ],
                    'isset' => [
                        'title' => __('Valorized', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-dot-circle-o',
                    ],
                    'value' => [
                        'title' => __('Specific value', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-circle',
                    ]
                ],
                'default' => 'isset',
                'toggle' => false,
                'condition' => [
                    'dce_visibility_usermeta!' => '',
                ],
                    ]
            );
            $element->add_control(
                    'dce_visibility_usermeta_value', [
                'label' => __('User Meta Value', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'description' => __('The specific value of the User Meta', DCE_TEXTDOMAIN),
                'condition' => [
                    //'dce_visibility_context' => '',
                    'dce_visibility_usermeta!' => '',
                    'dce_visibility_usermeta_status' => 'value',
                ],
                    ]
            );

            $element->add_control(
                    'dce_visibility_ip', [
                'label' => __('Remote IP', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'description' => __('Write here the list of IP who will be able to view this element.<br>Separate IPs by comma. (ex. "123.123.123.123, 8.8.8.8, 4.4.4.4")', DCE_TEXTDOMAIN)
                . '<br><b>' . __('Your current IP is: ', DCE_TEXTDOMAIN) . $_SERVER['REMOTE_ADDR'] . '</b>',
                    /*                     * 'condition' => [
                      'dce_visibility_everyone' => '',
                      ], */
                    ]
            );
            $element->add_control(
                    'dce_visibility_referrer', [
                'label' => __('Enable Referrer', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'description' => __('Triggered when previous page is a specific page.', DCE_TEXTDOMAIN),
                    /* 'condition' => [
                      'dce_visibility_everyone' => '',
                      ] */
                    ]
            );
            $element->add_control(
                    'dce_visibility_referrer_list', [
                'label' => __('Specific referral site authorized:', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXTAREA,
                'placeholder' => 'facebook.com' . PHP_EOL . 'google.com',
                'description' => __('Only selected referral, once per line. If empty it is triggered for all external site.', DCE_TEXTDOMAIN),
                'condition' => [
                    'dce_visibility_referrer' => 'yes',
                //'dce_visibility_everyone' => '',
                ],
                    ]
            );


            if (DCE_Helper::is_plugin_active('geoip-detect') && function_exists('geoip_detect2_get_info_from_current_ip')) {
                $geoinfo = geoip_detect2_get_info_from_current_ip();
                $countryInfo = new \YellowTree\GeoipDetect\Geonames\CountryInformation();
                $countries = $countryInfo->getAllCountries();
                $element->add_control(
                        'dce_visibility_country', [
                    'label' => __('Country', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT2,
                    'options' => $countries,
                    'description' => __('Trigger visibility for a specific country.', DCE_TEXTDOMAIN),
                    'multiple' => true,
                    'separator' => 'before',
                        ]
                );
                $element->add_control(
                        'dce_visibility_city', [
                    'label' => __('City', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'description' => __('Write here the name of the city which trigger the condition. Insert the city name translated in one of the supported language (preferable in EN) and don\'t worry about case sensitive. You can insert multiple cities, separated by comma.', DCE_TEXTDOMAIN) . '<br>' . __('Actually you are in:', DCE_TEXTDOMAIN) . ' ' . implode(', ', $geoinfo->city->names),]
                );
            }

            //YellowTree\GeoipDetect\DataSources\City::
            //geoip_detect2_get_info_from_current_ip();
            /* $element->add_control(
              'dce_visibility_referrer_selected', [
              'label' => __('Show/Hide', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __('Show', DCE_TEXTDOMAIN),
              'label_off' => __('Hide', DCE_TEXTDOMAIN),
              'description' => __('Show or hide by selected referrers.', DCE_TEXTDOMAIN),
              'condition' => [
              'dce_visibility_referrer' => 'yes',
              'dce_visibility_referrer_list!' => '',
              'dce_visibility_everyone' => '',
              ],
              ]
              ); */
            /* $element->add_control(
              'dce_visibility_user_selected', [
              'label' => __('Show/Hide', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __('Show', DCE_TEXTDOMAIN),
              'label_off' => __('Hide', DCE_TEXTDOMAIN),
              'return_value' => 'yes',
              'description' => __('Show or hide for selected users.', DCE_TEXTDOMAIN),
              'condition' => [
              'dce_visibility_everyone' => '',
              ],
              ]
              ); */

            //$element->end_controls_section();
        }

        if ($section == 'device') {
            /* $element->add_control(
              'dce_visibility_device', [
              'label' => __('Visible on Every Device', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'description' => __('If enabled element will displayed on every device', DCE_TEXTDOMAIN),
              ]
              ); */
            $element->add_control(
                    'dce_visibility_responsive', [
                'label' => __('Responsive', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    /* 'all' => [
                      'title' => __('All', DCE_TEXTDOMAIN),
                      'icon' => 'fa fa-circle-o',
                      ], */
                    'desktop' => [
                        'title' => __('Desktop and Tv', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-desktop',
                    ],
                    'mobile' => [
                        'title' => __('Mobile and Tablet', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-mobile',
                    ]
                ],
                'description' => __('Not really responsive, remove the element from the code based on the user\'s device. This trigger use native WP device detenction.', DCE_TEXTDOMAIN) . ' <a href="https://codex.wordpress.org/Function_Reference/wp_is_mobile" target="_blank">' . __('Read more.', DCE_TEXTDOMAIN) . '</a>',
                    //'default' => 'all',
                    //'toggle' => false,
                    /* 'condition' => [
                      'dce_visibility_device' => '',
                      ], */
                    ]
            );
            $element->add_control(
                    'dce_visibility_browser', [
                'label' => __('Browser', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                'options' => array(
                    'is_chrome' => 'Google Chrome',
                    'is_gecko' => 'FireFox',
                    'is_safari' => 'Safari',
                    'is_IE' => 'Internet Explorer',
                    'is_edge' => 'Microsoft Edge',
                    'is_NS4' => 'Netscape',
                    'is_opera' => 'Opera',
                    'is_lynx' => 'Lynx',
                    'is_iphone' => 'iPhone Safari'
                ),
                'description' => __('Trigger visibility for a specific browser.', DCE_TEXTDOMAIN),
                'multiple' => true,
                    /* 'condition' => [
                      'dce_visibility_device' => '',
                      ] */
                    ]
            );
            /* $element->add_control(
              'dce_visibility_device_selected', [
              'label' => __('Show/Hide', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __('Show', DCE_TEXTDOMAIN),
              'label_off' => __('Hide', DCE_TEXTDOMAIN),
              'return_value' => 'yes',
              'description' => __('Show or hide for selected device.', DCE_TEXTDOMAIN),
              'condition' => [
              'dce_visibility_device' => '',
              ],
              ]
              ); */
        }

        if ($section == 'datetime') {
            /* $element->add_control(
              'date_visibility_heading', [
              'label' => __('Date & Time', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              'condition' => [
              'enabled_visibility' => 'yes',
              'dce_visibility_hidden' => '',
              ],
              'separator' => 'before',
              ]
              ); */

            /* $element->add_control(
              'dce_visibility_datetime', [
              'label' => __('Visible EveryTIME', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'description' => __('If enabled you can show the element for a specific period.', DCE_TEXTDOMAIN),
              ]
              ); */

            if (time() != current_time('timestamp')) {
                $element->add_control(
                        'dce_visibility_datetime_important_note', [
                    'label' => '<strong><i class="elementor-dce-datetime-icon eicon-warning"></i> ' . __('ATTENTION', DCE_TEXTDOMAIN) . '</strong>',
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<small><br>' . __('Server time and Wordpress time are different.', DCE_TEXTDOMAIN) . '<br>'
                    . __('Will be used the Wordpress time you set in', DCE_TEXTDOMAIN)
                    . ' <a target="_blank" href="' . admin_url('options-general.php') . '">' . __('Wordpress General preferences', DCE_TEXTDOMAIN) . '</a>.<br>'
                    //.__( 'Here actual time on this page load:', DCE_TEXTDOMAIN ).'<br>'
                    . '<br>'
                    . '<strong>SERVER time:</strong><br>' . date('r') . '<br><br>'
                    . '<strong>WORDPRESS time:</strong><br>' . current_time('r')
                    . '</small>'
                    ,
                    'content_classes' => 'dce-datetime-notice',
                        /* 'condition' => [
                          'dce_visibility_datetime' => ''
                          ], */
                        ]
                );
            }

            $element->add_control(
                    'dce_visibility_date_from', [
                'label' => __('Date FROM', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::DATE_TIME,
                'description' => __('If set the element will appear after this date', DCE_TEXTDOMAIN),
                    /* 'condition' => [
                      'dce_visibility_datetime' => ''
                      ], */
                    ]
            );
            $element->add_control(
                    'dce_visibility_date_to', [
                'label' => __('Date TO', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::DATE_TIME,
                'description' => __('If set the element will be visible until this date', DCE_TEXTDOMAIN),
                    /* 'condition' => [
                      'dce_visibility_datetime' => ''
                      ], */
                    ]
            );

            $element->add_control(
                    'dce_visibility_period_from', [
                'label' => __('Period FROM', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'description' => __('If set the element will appear after this period', DCE_TEXTDOMAIN),
                'placeholder' => 'mm/dd',
                    ]
            );
            $element->add_control(
                    'dce_visibility_period_to', [
                'label' => __('Period TO', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'mm/dd',
                'description' => __('If set the element will be visible until this period', DCE_TEXTDOMAIN),
                    ]
            );

            global $wp_locale;
            $week = array();
            for ($day_index = 0; $day_index <= 6; $day_index++) {
                $week[esc_attr($day_index)] = $wp_locale->get_weekday($day_index);
            }
            $element->add_control(
                    'dce_visibility_time_week', [
                'label' => __('Days of the WEEK', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                'options' => $week,
                'description' => __('Select days in the week.', DCE_TEXTDOMAIN),
                'multiple' => true,
                    /* 'condition' => [
                      'dce_visibility_datetime' => '',
                      ], */
                    ]
            );


            $element->add_control(
                    'dce_visibility_time_from', [
                'label' => __('Time FROM', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'HH:mm',
                'description' => __('If setted (in H:m format) the element will appear after this time.', DCE_TEXTDOMAIN),
                    /* 'condition' => [
                      'dce_visibility_datetime' => ''
                      ], */
                    ]
            );
            $element->add_control(
                    'dce_visibility_time_to', [
                'label' => __('Time TO', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'HH:mm',
                'description' => __('If setted (in H:m format) the element will be visible until this time', DCE_TEXTDOMAIN),
                    /* 'condition' => [
                      'dce_visibility_datetime' => ''
                      ], */
                    ]
            );
            /* $element->add_control(
              'dce_visibility_datetime_selected', [
              'label' => __('Show/Hide', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __('Show', DCE_TEXTDOMAIN),
              'label_off' => __('Hide', DCE_TEXTDOMAIN),
              'return_value' => 'yes',
              'description' => __('Show or hide for selected datetime.', DCE_TEXTDOMAIN),
              'condition' => [
              'dce_visibility_datetime' => '',
              ],
              ]
              ); */
        }

        if ($section == 'context') {
            if (defined('DVE_PLUGIN_BASE')) { //  Feature not present in FREE version
                $element->add_control(
                        'dce_visibility_context_hide', [
                    'label' => __('Only in PRO', DCE_TEXTDOMAIN),
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<style>.elementor-control-dce_section_visibility_context { display: none !important; }</style>',
                        ]
                );
            } else {
                /* $element->add_control(
                  'post_visibility_heading', [
                  'label' => __('Context', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::HEADING,
                  'condition' => [
                  'enabled_visibility' => 'yes',
                  'dce_visibility_hidden' => '',
                  ],
                  'separator' => 'before',
                  ]
                  ); */
                /* $element->add_control(
                  'dce_visibility_context', [
                  'label' => __('Visible EveryWHERE', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::SWITCHER,
                  'default' => 'yes',
                  'description' => __("If you want show something only when it's in a specific page.", DCE_TEXTDOMAIN) . '<br><strong>' . __("Very useful if you are using a Template System.", DCE_TEXTDOMAIN) . '</strong>',
                  ]
                  ); */
                $element->add_control(
                        'dce_visibility_parameter', [
                    'label' => __('Parameter', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'description' => __('Write here the name of the parameter passed in GET or POST method', DCE_TEXTDOMAIN),
                        /* 'condition' => [
                          'dce_visibility_context' => '',
                          ], */
                        ]
                );
                $element->add_control(
                        'dce_visibility_parameter_status', [
                    'label' => __('Parameter Status', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'not' => [
                            'title' => __('Not isset', DCE_TEXTDOMAIN),
                            'icon' => 'fa fa-circle-o',
                        ],
                        'isset' => [
                            'title' => __('Isset', DCE_TEXTDOMAIN),
                            'icon' => 'fa fa-dot-circle-o',
                        ],
                        'value' => [
                            'title' => __('Definited value', DCE_TEXTDOMAIN),
                            'icon' => 'fa fa-circle',
                        ]
                    ],
                    'default' => 'isset',
                    'toggle' => false,
                    'condition' => [
                        //'dce_visibility_context' => '',
                        'dce_visibility_parameter!' => '',
                    ],
                        ]
                );
                $element->add_control(
                        'dce_visibility_parameter_value', [
                    'label' => __('Parameter Value', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'description' => __('The specific value of the parameter', DCE_TEXTDOMAIN),
                    'condition' => [
                        //'dce_visibility_context' => '',
                        'dce_visibility_parameter!' => '',
                        'dce_visibility_parameter_status' => 'value',
                    ],
                        ]
                );

                /* $element->add_control(
                  'dce_visibility_max_user',
                  [
                  'label' => __('Max per User', DCE_TEXTDOMAIN),
                  'type' => \Elementor\Controls_Manager::NUMBER,
                  'min' => 0,
                  ]
                  ); */
                $element->add_control(
                        'dce_visibility_max_day',
                        [
                            'label' => __('Max per Day', DCE_TEXTDOMAIN),
                            'type' => \Elementor\Controls_Manager::NUMBER,
                            'min' => 0,
                        ]
                );
                $element->add_control(
                        'dce_visibility_max_total',
                        [
                            'label' => __('Max Total', DCE_TEXTDOMAIN),
                            'type' => \Elementor\Controls_Manager::NUMBER,
                            'min' => 0,
                        ]
                );

                /* $element->add_control(
                  'dce_visibility_context_selected', [
                  'label' => __('Show/Hide', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::SWITCHER,
                  'default' => 'yes',
                  'label_on' => __('Show', DCE_TEXTDOMAIN),
                  'label_off' => __('Hide', DCE_TEXTDOMAIN),
                  'description' => __('Hide or show in selected context.', DCE_TEXTDOMAIN),
                  'condition' => [
                  'dce_visibility_context' => '',
                  ],
                  ]
                  ); */

                /* $element->add_control(
                  'dce_visibility_meta_selected', [
                  'label' => __('Show/Hide', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::SWITCHER,
                  'default' => 'yes',
                  'label_on' => __('Hide', DCE_TEXTDOMAIN),
                  'label_off' => __('Show', DCE_TEXTDOMAIN),
                  'return_value' => 'yes',
                  'description' => __('Visible or hidden by selected meta.', DCE_TEXTDOMAIN),
                  'condition' => [
                  'enabled_visibility' => 'yes',
                  'dce_visibility_hidden' => '',
                  'dce_visibility_meta!' => '',
                  ],
                  ]
                  ); */
            }
        }

        if ($section == 'post') {
            if (defined('DVE_PLUGIN_BASE')) { //  Feature not present in FREE version
                $element->add_control(
                        'dce_visibility_curent_post_hide', [
                    'label' => __('Only in PRO', DCE_TEXTDOMAIN),
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<style>.elementor-control-dce_section_visibility_context { display: none !important; }</style>',
                        ]
                );
            } else {
                $element->add_control(
                        'dce_visibility_cpt', [
                    'label' => __('CPT', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT2,
                    'options' => $post_types,
                    'description' => __('Visible if current post is one of this Custom Post Type.', DCE_TEXTDOMAIN),
                    'multiple' => true,
                        /* 'condition' => [
                          'dce_visibility_context' => '',
                          ], */
                        ]
                );
                $element->add_control(
                        'dce_visibility_post', [
                    'label' => __('Page/Post', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT2,
                    'options' => $all_posts,
                    'description' => __('Visible if current post is one of this Page/Posts.', DCE_TEXTDOMAIN),
                    'multiple' => true,
                        /* 'condition' => [
                          'dce_visibility_context' => '',
                          ], */
                        ]
                );

                $element->add_control(
                        'dce_visibility_tax', [
                    'label' => __('Taxonomy', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT2,
                    'options' => $taxonomies,
                    'description' => __('Triggered if current post is related with this Taxonomy.', DCE_TEXTDOMAIN),
                    'multiple' => false,
                        /* 'condition' => [
                          'dce_visibility_context' => '',
                          ], */
                        ]
                );

                foreach ($taxonomies as $tkey => $atax) {
                    if ($tkey) {
                        $element->add_control(
                                'dce_visibility_term_' . $tkey, [
                            'label' => __('Terms', DCE_TEXTDOMAIN), //.' '.$atax,
                            'type' => Controls_Manager::SELECT2,
                            //'groups' => \DynamicContentForElementor\DCE_Helper::get_taxonomies_terms(),
                            'options' => $taxonomies_terms[$tkey],
                            'description' => __('Visible if current post is related with this Terms.', DCE_TEXTDOMAIN),
                            'multiple' => true,
                            'condition' => [
                                //'dce_visibility_context' => '',
                                'dce_visibility_tax' => $tkey,
                            ],
                                ]
                        );
                    }
                }

                $element->add_control(
                        'dce_visibility_field', [
                    'label' => __('Meta Field', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT2,
                    'options' => $post_metas,
                    'description' => __('Triggered by a selected Post Meta value', DCE_TEXTDOMAIN),
                        ]
                );
                $element->add_control(
                        'dce_visibility_field_status', [
                    'label' => __('Post Field Status', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'not' => [
                            'title' => __('Not isset or empty', DCE_TEXTDOMAIN),
                            'icon' => 'fa fa-circle-o',
                        ],
                        'isset' => [
                            'title' => __('Valorized', DCE_TEXTDOMAIN),
                            'icon' => 'fa fa-dot-circle-o',
                        ],
                        'value' => [
                            'title' => __('Specific value', DCE_TEXTDOMAIN),
                            'icon' => 'fa fa-circle',
                        ]
                    ],
                    'default' => 'isset',
                    'toggle' => false,
                    'condition' => [
                        'dce_visibility_field!' => '',
                    ],
                        ]
                );
                $element->add_control(
                        'dce_visibility_field_value', [
                    'label' => __('Post Meta Value', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'description' => __('The specific value of the Post Meta', DCE_TEXTDOMAIN),
                    'condition' => [
                        //'dce_visibility_context' => '',
                        'dce_visibility_field!' => '',
                        'dce_visibility_field_status' => 'value',
                    ],
                        ]
                );

                $element->add_control(
                        'dce_visibility_meta', [
                    'label' => __('Metas', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT2,
                    'options' => $post_metas,
                    'description' => __('Triggered by specifics metas fields if they are valorized.', DCE_TEXTDOMAIN),
                    'multiple' => true,
                        /* 'condition' => [
                          'dce_visibility_context' => '',
                          ], */
                        ]
                );
                $element->add_control(
                        'dce_visibility_meta_operator', [
                    'label' => __('Meta conditions', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'label_on' => __('And', DCE_TEXTDOMAIN),
                    'label_off' => __('Or', DCE_TEXTDOMAIN),
                    'description' => __('How post meta have to satisfy this conditions.', DCE_TEXTDOMAIN),
                    'condition' => [
                        'dce_visibility_meta!' => '',
                    //'dce_visibility_context' => '',
                    ],
                        ]
                );

                $element->add_control(
                        'dce_visibility_format', [
                    'label' => __('Format', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT2,
                    'options' => DCE_Helper::get_post_formats(),
                    'description' => __('Triggered if current post is setted as one of this format.', DCE_TEXTDOMAIN) . '<br><a href="https://wordpress.org/support/article/post-formats/" target="_blank">' . __('Read more on Post Format.', DCE_TEXTDOMAIN) . '</a>',
                    'multiple' => true,
                        ]
                );

                $element->add_control(
                        'dce_visibility_parent', [
                    'label' => __('Is Parent', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Triggered for post with children.', DCE_TEXTDOMAIN),
                        ]
                );
                $element->add_control(
                        'dce_visibility_root', [
                    'label' => __('Is Root', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Triggered for post of first level (without parent).', DCE_TEXTDOMAIN),
                        ]
                );
                $element->add_control(
                        'dce_visibility_leaf', [
                    'label' => __('Is Leaf', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Triggered for post of last level (without children).', DCE_TEXTDOMAIN),
                        ]
                );
                $element->add_control(
                        'dce_visibility_node', [
                    'label' => __('Is Node', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Triggered for post of intermedial level (with parent and child).', DCE_TEXTDOMAIN),
                        ]
                );
                $element->add_control(
                        'dce_visibility_node_level',
                        [
                            'label' => __('Node level', DCE_TEXTDOMAIN),
                            'type' => \Elementor\Controls_Manager::NUMBER,
                            'min' => 1,
                            'condition' => [
                                'dce_visibility_node!' => '',
                            ],
                        ]
                );
                $element->add_control(
                        'dce_visibility_child', [
                    'label' => __('Has Parent', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Triggered for posts which are children (with a parent).', DCE_TEXTDOMAIN),
                        ]
                );
                $element->add_control(
                        'dce_visibility_child_parent',
                        [
                            'label' => __('Spercific Parent Post ID', DCE_TEXTDOMAIN),
                            'type' => \Elementor\Controls_Manager::TEXT,
                            'min' => 1,
                            'description' => __('Specify the ID (or multiple separated by comma) of a Post, all his children will be trigger. Otherwise leave blank for a generic parent.', DCE_TEXTDOMAIN),
                            'condition' => [
                                'dce_visibility_child!' => '',
                            ],
                        ]
                );
                $element->add_control(
                        'dce_visibility_sibling', [
                    'label' => __('Has Siblings', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Triggered for post with siblings.', DCE_TEXTDOMAIN),
                        ]
                );
                $element->add_control(
                        'dce_visibility_friend', [
                    'label' => __('Has Term Buddies', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Triggered for post grouped in taxonomies with other posts.', DCE_TEXTDOMAIN),
                        ]
                );
                $element->add_control(
                        'dce_visibility_friend_term', [
                    'label' => __('Terms where find Buddies', DCE_TEXTDOMAIN), //.' '.$atax,
                    'type' => Controls_Manager::SELECT2,
                    //'groups' => \DynamicContentForElementor\DCE_Helper::get_taxonomies_terms(),
                    'options' => $all_taxonomies_terms,
                    'description' => __('Specific a Term for current post has friends.', DCE_TEXTDOMAIN),
                    'multiple' => true,
                    'label_block' => true,
                    'condition' => [
                        'dce_visibility_friend!' => '',
                    ],
                        ]
                );
            }
        }

        if ($section == 'tags') {
            /* $element->add_control(
              'tags_visibility_heading', [
              'label' => __('Conditional Tags', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              'condition' => [
              'enabled_visibility' => 'yes',
              'dce_visibility_hidden' => '',
              ],
              'separator' => 'before',
              ]
              ); */
            /* $element->add_control(
              'dce_visibility_tags', [
              'label' => __('Visible UNconditionally', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'description' => __('You can use Conditional Tags rule to decide to show your element.', DCE_TEXTDOMAIN).'<a href="https://codex.wordpress.org/Conditional_Tags" target="_blank">' . '<br>'. __('Read more on WordPress related page.', DCE_TEXTDOMAIN).'</a>',
              ]
              ); */
            $element->add_control(
                    'dce_visibility_tags_intro', [
                'label' => '<b>' . __('What\'s Conditional Tags?', DCE_TEXTDOMAIN) . '</b>',
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('You can use native Wordpress Conditional Tags to decide when show your element.', DCE_TEXTDOMAIN)
                . '<br>' . __('Don\'t you know them?', DCE_TEXTDOMAIN) . ' <a href="https://codex.wordpress.org/Conditional_Tags" target="_blank">' . __('Read more on WordPress Codex related page.', DCE_TEXTDOMAIN) . '</a>',
                    ]
            );
            // https://codex.wordpress.org/Conditional_Tags
            $element->add_control(
                    'dce_visibility_conditional_tags_post', [
                'label' => __('Post', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'is_sticky' => __('Is Sticky', DCE_TEXTDOMAIN),
                    'is_post_type_hierarchical' => __('Is Hierarchical Post Type', DCE_TEXTDOMAIN),
                    'is_post_type_archive' => __('Is Post Type Archive', DCE_TEXTDOMAIN),
                    'comments_open' => __('Comments open', DCE_TEXTDOMAIN),
                    'pings_open' => __('Pings open', DCE_TEXTDOMAIN),
                    'has_tag' => __('Has Tags', DCE_TEXTDOMAIN),
                    'has_term' => __('Has Terms', DCE_TEXTDOMAIN),
                    'has_excerpt' => __('Has Excerpt', DCE_TEXTDOMAIN),
                    'has_post_thumbnail' => __('Has Post Thumbnail', DCE_TEXTDOMAIN),
                    'has_nav_menu' => __('Has Nav menu', DCE_TEXTDOMAIN),
                ],
                'multiple' => true,
                    /* 'condition' => [
                      'dce_visibility_tags' => '',
                      ], */
                    ]
            );
            $element->add_control(
                    'dce_visibility_conditional_tags_site', [
                'label' => __('Site', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'is_dynamic_sidebar' => __('Dynamic sidebar', DCE_TEXTDOMAIN),
                    'is_active_sidebar' => __('Active sidebar', DCE_TEXTDOMAIN),
                    'is_rtl' => __('RTL', DCE_TEXTDOMAIN),
                    'is_multisite' => __('Multisite', DCE_TEXTDOMAIN),
                    'is_main_site' => __('Main site', DCE_TEXTDOMAIN),
                    'is_child_theme' => __('Child theme', DCE_TEXTDOMAIN),
                    'is_customize_preview' => __('Customize preview', DCE_TEXTDOMAIN),
                    'is_multi_author' => __('Multi author', DCE_TEXTDOMAIN),
                    'is feed' => __('Feed', DCE_TEXTDOMAIN),
                    'is_trackback' => __('Trackback', DCE_TEXTDOMAIN),
                ],
                'multiple' => true,
                    /* 'condition' => [
                      'dce_visibility_tags' => '',
                      ], */
                    ]
            );
            // https://codex.wordpress.org/Special:SpecialPages
            $element->add_control(
                    'dce_visibility_special', [
                'label' => __('Page', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'is_front_page' => __('Front Page', DCE_TEXTDOMAIN),
                    'is_home' => __('Home', DCE_TEXTDOMAIN),
                    'is_404' => __('404 Not Found', DCE_TEXTDOMAIN),
                    'is_single' => __('Single', DCE_TEXTDOMAIN),
                    'is_page' => __('Page', DCE_TEXTDOMAIN),
                    'is_attachment' => __('Attachment', DCE_TEXTDOMAIN),
                    'is_preview' => __('Preview', DCE_TEXTDOMAIN),
                    'is_admin' => __('Admin', DCE_TEXTDOMAIN),
                    'is_page_template' => __('Page Template', DCE_TEXTDOMAIN),
                    'is_comments_popup' => __('Comments Popup', DCE_TEXTDOMAIN),
                    /*
                      'static' => __('Static', DCE_TEXTDOMAIN),
                      'login' => __('Login', DCE_TEXTDOMAIN),
                      'registration' => __('Registration', DCE_TEXTDOMAIN),
                      'profile' => __('Profile', DCE_TEXTDOMAIN),
                     */
                    // woocommerce
                    'is_woocommerce' => __('A Woocommerce Page', DCE_TEXTDOMAIN),
                    'is_shop' => __('Shop', DCE_TEXTDOMAIN),
                    'is_product' => __('Product', DCE_TEXTDOMAIN),
                    'is_product_taxonomy' => __('Product Taxonomy', DCE_TEXTDOMAIN),
                    'is_product_category' => __('Product Category', DCE_TEXTDOMAIN),
                    'is_product_tag' => __('Product Tag', DCE_TEXTDOMAIN),
                    'is_cart' => __('Cart', DCE_TEXTDOMAIN),
                    'is_checkout' => __('Checkout', DCE_TEXTDOMAIN),
                    'is_add_payment_method_page' => __('Add Payment method', DCE_TEXTDOMAIN),
                    'is_checkout_pay_page' => __('Checkout Pay', DCE_TEXTDOMAIN),
                    'is_account_page' => __('Account page', DCE_TEXTDOMAIN),
                    'is_edit_account_page' => __('Edit Account', DCE_TEXTDOMAIN),
                    'is_lost_password_page' => __('Lost password', DCE_TEXTDOMAIN),
                    'is_view_order_page' => __('Order summary', DCE_TEXTDOMAIN),
                    'is_order_received_page' => __('Order complete', DCE_TEXTDOMAIN),
                ],
                'multiple' => true,
                    /* 'condition' => [
                      'dce_visibility_tags' => '',
                      ], */
                    ]
            );
            $element->add_control(
                    'dce_visibility_archive', [
                'label' => __('Archive', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'is_blog' => __('Home blog (latest posts)', DCE_TEXTDOMAIN),
                    'posts_page' => __('Posts page', DCE_TEXTDOMAIN),
                    'is_tax' => __('Taxonomy', DCE_TEXTDOMAIN),
                    'is_category' => __('Category', DCE_TEXTDOMAIN),
                    'is_tag' => __('Tag', DCE_TEXTDOMAIN),
                    'is_author' => __('Author', DCE_TEXTDOMAIN),
                    'is_date' => __('Date', DCE_TEXTDOMAIN),
                    'is_year' => __('Year', DCE_TEXTDOMAIN),
                    'is_month' => __('Month', DCE_TEXTDOMAIN),
                    'is_day' => __('Day', DCE_TEXTDOMAIN),
                    'is_time' => __('Time', DCE_TEXTDOMAIN),
                    'is_new_day' => __('New Day', DCE_TEXTDOMAIN),
                    'is_search' => __('Search', DCE_TEXTDOMAIN),
                    'is_paged' => __('Paged', DCE_TEXTDOMAIN),
                    'is_main_query' => __('Main Query', DCE_TEXTDOMAIN),
                    'in_the_loop' => __('In the Loop', DCE_TEXTDOMAIN),
                ],
                'multiple' => true,
                    /* 'condition' => [
                      'dce_visibility_tags' => '',
                      ], */
                    ]
            );

            /* $element->add_control(
              'dce_visibility_tags_selected', [
              'label' => __('Show/Hide', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __('Show', DCE_TEXTDOMAIN),
              'label_off' => __('Hide', DCE_TEXTDOMAIN),
              'description' => __('Hide or show in selected tags.', DCE_TEXTDOMAIN),
              'condition' => [
              'dce_visibility_tags' => '',
              ],
              ]
              ); */
        }

        if ($section == 'random') {
            $element->add_control(
                    'dce_visibility_random',
                    [
                        'label' => __('Random', DCE_TEXTDOMAIN),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => ['%'],
                        'range' => [
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                    ]
            );
        }

        if ($section == 'custom') {
            if (defined('DVE_PLUGIN_BASE')) { //  Feature not present in FREE version
                $element->add_control(
                        'dce_visibility_custom_hide', [
                    'label' => __('Only in PRO', DCE_TEXTDOMAIN),
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<style>.elementor-control-dce_section_visibility_custom { display: none !important; }</style>',
                        ]
                );
            } else {
                /* $element->add_control(
                  'php_visibility_heading', [
                  'label' => __('Custom Condition', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::HEADING,
                  'condition' => [
                  'enabled_visibility' => 'yes',
                  'dce_visibility_hidden' => '',
                  ],
                  'separator' => 'before',
                  ]
                  ); */
                /* $element->add_control(
                  'dce_visibility_custom_condition', [
                  'label' => __('Visible Always', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::SWITCHER,
                  'default' => 'yes',
                  'description' => __("By a your handwritten advanced custom condition.", DCE_TEXTDOMAIN),
                  ]
                  ); */
                $element->add_control(
                        'dce_visibility_custom_condition_php', [
                    'label' => __('Custom PHP condition', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::CODE,
                    //'placeholder' => 'return true;',
                    'default' => 'return true;',
                    'description' => __('Write here a function that return a boolean value. You can use all WP variabile and functions.', DCE_TEXTDOMAIN),
                        /* 'condition' => [
                          'dce_visibility_custom_condition' => '',
                          ], */
                        ]
                );
                $element->add_control(
                        'dce_visibility_custom_condition_secure', [
                    'label' => __('Prevent errors', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'description' => __('Execute code externally in secure mode without throw possible FATAL error.', DCE_TEXTDOMAIN)
                    . '<br><strong>' . __("NOTE", DCE_TEXTDOMAIN) . '</strong>: ' . __("if you want access to current page data and context you need to disable it.", DCE_TEXTDOMAIN)
                    . '<br><strong>' . __("WARNING: if it's disabled a wrong code can broke this page, check if code is correct before saving.", DCE_TEXTDOMAIN) . '</strong>',
                        /* 'condition' => [
                          'dce_visibility_custom_condition' => '',
                          ], */
                        ]
                );
                /* $element->add_control(
                  'dce_visibility_custom_condition_selected', [
                  'label' => __('Show/Hide', DCE_TEXTDOMAIN),
                  'type' => Controls_Manager::SWITCHER,
                  'default' => 'yes',
                  'label_on' => __('Show', DCE_TEXTDOMAIN),
                  'label_off' => __('Hide', DCE_TEXTDOMAIN),
                  'description' => __('Hide or show by custom condition.', DCE_TEXTDOMAIN),
                  'condition' => [
                  'dce_visibility_custom_condition' => '',
                  ],
                  ]
                  ); */
            }
        }

        /*
          if ($section == 'repeater') {

          $repeater_fields = new \Elementor\Repeater();
          $repeater_fields->add_control(
          'dce_visibility_repeater_trigger', [
          'label' => __('Trigger', DCE_TEXTDOMAIN),
          'type' => Controls_Manager::SELECT,
          'options' => self::$triggers,
          ]
          );

          $element->add_control(
          'dce_visibility_repeater', [
          'label' => __('Add trigger', DCE_TEXTDOMAIN),
          'type' => \Elementor\Controls_Manager::REPEATER,
          'fields' => $repeater_fields->get_controls(),
          ]
          );

          $element->add_control(
          'dce_visibility_repeater_expression', [
          'label' => __('Parameter Value', DCE_TEXTDOMAIN),
          'type' => Controls_Manager::TEXT,
          'placeholder' => '((1) AND (2)) OR (3)',
          'description' => __('The combination of selected trigger', DCE_TEXTDOMAIN),
          ]
          );
          }
         */

        if ($section == 'fallback') {
            /* $element->add_control(
              'fallback_visibility_heading', [
              'label' => __('Fallback', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::HEADING,
              'separator' => 'before',
              'condition' => [
              'enabled_visibility' => 'yes',
              ],
              ]
              ); */
            $element->add_control(
                    'dce_visibility_fallback', [
                'label' => __('Enable a Fallback Content', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'description' => __("If you want to show something when the element is hidden", DCE_TEXTDOMAIN),
                    ]
            );
            if (defined('DVE_PLUGIN_BASE')) { // free version not support template shortcode
                $element->add_control(
                        'dce_visibility_fallback_type', [
                    'label' => __('Content type', DCE_TEXTDOMAIN),
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 'text',
                        ]
                );
            } else {
                $element->add_control(
                        'dce_visibility_fallback_type', [
                    'label' => __('Content type', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'text' => [
                            'title' => __('Text', DCE_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'template' => [
                            'title' => __('Template', DCE_TEXTDOMAIN),
                            'icon' => 'fa fa-th-large',
                        ]
                    ],
                    'default' => 'text',
                    'condition' => [
                        'dce_visibility_fallback!' => '',
                    ],
                        ]
                );
            }
            $element->add_control(
                    'dce_visibility_fallback_template', [
                'label' => __('Render Template', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT2,
                //'options' => get_post_taxonomies( $post->ID ),
                'options' => $templates,
                'description' => 'Use a Elementor Template as content of popup, useful for complex structure.',
                'condition' => [
                    'dce_visibility_fallback!' => '',
                    'dce_visibility_fallback_type' => 'template',
                ],
                    ]
            );
            $element->add_control(
                    'dce_visibility_fallback_text', [
                'label' => __('Text Fallback', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::WYSIWYG,
                'default' => "This element is currently hidden.",
                'description' => __("Insert here some content showed if the element is not visible", DCE_TEXTDOMAIN),
                'condition' => [
                    'dce_visibility_fallback!' => '',
                    'dce_visibility_fallback_type' => 'text',
                ],
                    ]
            );
            if ($element_type == 'section') {
                $element->add_control(
                        'dce_visibility_fallback_section', [
                    'label' => __('Use section wrapper', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'description' => __('Mantain original section wrapper.', DCE_TEXTDOMAIN),
                    'condition' => [
                        'dce_visibility_fallback!' => '',
                    //'dce_visibility_fallback_type' => 'text',
                    //'dce_visibility_fallback_text!' => '',
                    ],
                        ]
                );
            }
        }

        //$this->end_controls_section();
    }

    public function visibility_print_widget($content, $widget) {
        if (!$content)
            return '';

        $notice = '<div class="dce-visibility-warning"><i class="fa fa-eye-slash"></i> Hidden</div>'; // nascondo il widget
        $content = "<# if ( '' !== settings.enabled_visibility ) { if ( '' !== settings.dce_visibility_hidden ) { #>" . $notice . "<# } #><div class=\"dce-visibility-hidden-outline\">" . $content . "</div><# } else { #>" . $content . "<# } #>";
        return $content;
    }

    public function set_element_view_counters($element, $hidden = false) {
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $settings = $element->get_settings_for_display();
            if ((!$hidden && $settings['dce_visibility_selected']) || ($hidden && !$settings['dce_visibility_selected'])) {
                //var_dump($settings);
                if (!empty($settings['dce_visibility_max_user']) || !empty($settings['dce_visibility_max_day']) || !empty($settings['dce_visibility_max_total'])) {
                    $dce_visibility_max = get_option('dce_visibility_max', array());

                    // remove elements with no limits
                    foreach ($dce_visibility_max as $ekey => $value) {
                        if ($ekey != $element->get_id()) {
                            $esettings = DCE_Helper::get_settings_by_id($ekey);
                            //var_dump($esettings);
                            if (empty($esettings['dce_visibility_max_day']) && empty($esettings['dce_visibility_max_total'])) {
                                unset($dce_visibility_max[$ekey]);
                            } else {
                                if (empty($esettings['dce_visibility_max_day'])) {
                                    unset($dce_visibility_max[$ekey]['day']);
                                }
                                if (empty($esettings['dce_visibility_max_total'])) {
                                    unset($dce_visibility_max[$ekey]['total']);
                                }
                            }
                        }
                    }

                    //var_dump($dce_visibility_max);
                    if (isset($dce_visibility_max[$element->get_id()])) {
                        $today = date('Ymd');
                        /*
                          // save in cookie/usermeta
                          if (!empty($settings['dce_visibility_max_user'])) {
                          $current_user_unique = get_current_user_id();
                          if (!$current_user_unique) {
                          $current_user_unique = wp_get_session_token();
                          }
                          $dce_visibility_max_user = intval($dce_visibility_max['user'][]) + 1;
                          } else {
                          $dce_visibility_max_user = array();
                          }
                         */

                        if (!empty($settings['dce_visibility_max_day'])) {
                            if (!empty($dce_visibility_max[$element->get_id()]['day'][$today])) {
                                $dce_visibility_max_day = $dce_visibility_max[$element->get_id()]['day'];
                                $dce_visibility_max_day[$today] = intval($dce_visibility_max_day[$today]) + 1;
                            } else {
                                $dce_visibility_max_day = array();
                                $dce_visibility_max_day[$today] = 1;
                            }
                        } else {
                            $dce_visibility_max_day = array();
                        }
                        if (!empty($settings['dce_visibility_max_total'])) {
                            if (isset($dce_visibility_max[$element->get_id()]['total'])) {
                                $dce_visibility_max_total = intval($dce_visibility_max[$element->get_id()]['total']) + 1;
                            } else {
                                $dce_visibility_max_total = 1;
                            }
                        } else {
                            $dce_visibility_max_total = 0;
                        }
                    } else {
                        $dce_visibility_max_day = array();
                        $dce_visibility_max_total = 1;
                    }
                    $dce_visibility_max[$element->get_id()] = array(
                        'day' => $dce_visibility_max_day,
                        'total' => $dce_visibility_max_total,
                    );
                    //var_dump($dce_visibility_max);
                    update_option('dce_visibility_max', $dce_visibility_max);
                }
            }
        }
    }

    public function visibility_render_widget($content, $widget) {
        $settings = $widget->get_settings_for_display();
        //delete_option('dce_visibility_max');
        if (isset($settings['enabled_visibility']) && $settings['enabled_visibility']) {
            $hidden = $this->is_hidden($widget);
            if ($hidden) {
                $this->print_conditions($widget);
            }
            $this->set_element_view_counters($widget, $hidden);

            // show element in backend
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $notice = '';
                if ($hidden) {
                    $widget->add_render_attribute('_wrapper', 'class', 'dce-visibility-hidden');
                    $notice = '<div class="dce-visibility-warning"><i class="fa fa-eye-slash"></i> Hidden</div>'; // nascondo il widget
                    //return $notice . '<div class="dce-visibility-hidden dce-visibility-hidden-outline">' . $content . '</div>'; // mostro il widget
                    //return $notice .  $content ; // mostro il widget
                }

                //return '<div class="dce-visibility-hidden-outline">' . $content . '</div>';
                return $content;
            }

            if ($hidden) {
                if (!isset($settings['dce_visibility_dom']) || !$settings['dce_visibility_dom']) {
                    $content = '';
                }
                if (isset($settings['dce_visibility_debug']) && $settings['dce_visibility_debug']) {
                    $content = '<div class="dce-visibility-original-content dce-visibility-widget-hidden">' . $content . '</div>';
                }

                $fallback = $this->get_fallback($settings, $widget);
                if ($fallback) {
                    return $content . $fallback;
                }
                return $content; // . '<style>' . $widget->get_unique_selector() . '{display:none !important;}</style>'; // nascondo il widget
            }
        }
        return $content; // mostro il widget
    }

    public function get_fallback($settings, $element = null) {

        if (isset($settings['dce_visibility_fallback']) && $settings['dce_visibility_fallback']) {
            if (isset($settings['dce_visibility_fallback_type']) && $settings['dce_visibility_fallback_type'] == 'template') {
                $fallback_content = '[dce-elementor-template id="' . $settings['dce_visibility_fallback_template'] . '"]';
            } else { //if ($settings['dce_visibility_fallback_type'] == 'text') {
                $fallback_content = __($settings['dce_visibility_fallback_text'], DCE_TEXTDOMAIN . '_texts');
            }
            $fallback_content = do_shortcode($fallback_content); // TODO FIX
            if (!defined('DVE_PLUGIN_BASE')) {
                $fallback_content = \DynamicContentForElementor\DCE_Tokens::do_tokens($fallback_content);
            }


            if ($fallback_content && (!isset($settings['dce_visibility_fallback_section']) || $settings['dce_visibility_fallback_section'] == 'yes')) { // BUG - Fix it
                $fallback_content = '
                                <div class="elementor-element elementor-column elementor-col-100 elementor-top-column" data-element_type="column">
                                    <div class="elementor-column-wrap elementor-element-populated">
                                        <div class="elementor-widget-wrap">
                                            <div class="elementor-element elementor-widget">
                                                <div class="elementor-widget-container dce-visibility-fallback">'
                        . $fallback_content .
                        '</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                ob_start();
                $element->before_render();
                echo $fallback_content;
                $element->after_render();
                $fallback_content = ob_get_contents();
                ob_end_clean();
            }

            return $fallback_content;
        }
        return '';
    }

    public function is_hidden($element = null, $why = false) {
        $settings = $element->get_settings_for_display();

        $hidden = FALSE;
        $conditions = array();

        if ($why) {
            //var_dump($settings);
        }

        if (isset($settings['enabled_visibility']) && $settings['enabled_visibility']) {

            // FORCED HIDDEN
            if (isset($settings['dce_visibility_hidden']) && $settings['dce_visibility_hidden']) {
                $conditions['dce_visibility_hidden'] = __('Always Hidden', DCE_TEXTDOMAIN);
                $hidden = TRUE;
            } else {

                // DATETIME
                //if (isset($settings['dce_visibility_datetime']) && !$settings['dce_visibility_datetime']) {
                $everytimehidden = false;

                if ($settings['dce_visibility_date_from'] && $settings['dce_visibility_date_to']) {
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        $conditions['date'] = __('Date', DCE_TEXTDOMAIN);
                    }
                    // between
                    $dateTo = strtotime($settings['dce_visibility_date_to']);
                    $dateFrom = strtotime($settings['dce_visibility_date_from']);
                    if (current_time('timestamp') >= $dateFrom && current_time('timestamp') <= $dateTo) {
                        $conditions['date'] = __('Date', DCE_TEXTDOMAIN);
                        $everytimehidden = TRUE;
                    }
                } else {
                    if ($settings['dce_visibility_date_from']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_date_from'] = __('Date From', DCE_TEXTDOMAIN);
                        }
                        $dateFrom = strtotime($settings['dce_visibility_date_from']);
                        if (current_time('timestamp') >= $dateFrom) {
                            $conditions['dce_visibility_date_from'] = __('Date From', DCE_TEXTDOMAIN);
                            $everytimehidden = TRUE;
                        }
                    }
                    if ($settings['dce_visibility_date_to']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_date_to'] = __('Date To', DCE_TEXTDOMAIN);
                        }
                        $dateTo = strtotime($settings['dce_visibility_date_to']);
                        if (current_time('timestamp') <= $dateTo) {
                            $conditions['dce_visibility_date_to'] = __('Date To', DCE_TEXTDOMAIN);
                            $everytimehidden = TRUE;
                        }
                    }
                }

                if ($settings['dce_visibility_period_from'] && $settings['dce_visibility_period_to']) {
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        $conditions['period'] = __('Period', DCE_TEXTDOMAIN);
                    }
                    // between
                    if (date_i18n('m/d') >= $settings['dce_visibility_period_from'] && date_i18n('m/d') <= $settings['dce_visibility_period_to']) {
                        $conditions['period'] = __('Period', DCE_TEXTDOMAIN);
                        $everytimehidden = TRUE;
                    }
                } else {
                    if ($settings['dce_visibility_period_from']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_period_from'] = __('Period From', DCE_TEXTDOMAIN);
                        }
                        if (date_i18n('m/d') >= $settings['dce_visibility_period_from']) {
                            $conditions['dce_visibility_period_from'] = __('Period From', DCE_TEXTDOMAIN);
                            $everytimehidden = TRUE;
                        }
                    }
                    if ($settings['dce_visibility_period_to']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_period_to'] = __('Period To', DCE_TEXTDOMAIN);
                        }
                        if (date_i18n('m/d') <= $settings['dce_visibility_period_to']) {
                            $conditions['dce_visibility_period_to'] = __('Period To', DCE_TEXTDOMAIN);
                            $everytimehidden = TRUE;
                        }
                    }
                }

                if ($settings['dce_visibility_time_week'] && !empty($settings['dce_visibility_time_week'])) {
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        $conditions['dce_visibility_time_week'] = __('Day of Week', DCE_TEXTDOMAIN);
                    }
                    if (in_array(current_time('w'), $settings['dce_visibility_time_week'])) {
                        $conditions['dce_visibility_time_week'] = __('Day of Week', DCE_TEXTDOMAIN);
                        $everytimehidden = TRUE;
                    }
                }

                if ($settings['dce_visibility_time_from'] && $settings['dce_visibility_time_to']) {
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        $conditions['time'] = __('Time', DCE_TEXTDOMAIN);
                    }
                    $timeFrom = $settings['dce_visibility_time_from'];
                    $timeTo = ($settings['dce_visibility_time_to'] == '00:00') ? '24:00' : $settings['dce_visibility_time_to'];
                    if (current_time('H:m') >= $timeFrom && current_time('H:m') <= $timeTo) {
                        $conditions['time'] = __('Time', DCE_TEXTDOMAIN);
                        $everytimehidden = TRUE;
                    }
                } else {
                    if ($settings['dce_visibility_time_from']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_time_from'] = __('Time From', DCE_TEXTDOMAIN);
                        }
                        $timeFrom = $settings['dce_visibility_time_from'];
                        if (current_time('H:m') >= $timeFrom) {
                            $conditions['dce_visibility_time_from'] = __('Time From', DCE_TEXTDOMAIN);
                            $everytimehidden = TRUE;
                        }
                    }
                    if ($settings['dce_visibility_time_to']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_time_to'] = __('Time To', DCE_TEXTDOMAIN);
                        }
                        $timeTo = ($settings['dce_visibility_time_to'] == '00:00') ? '24:00' : $settings['dce_visibility_time_to'];
                        if (current_time('H:m') <= $timeTo) {
                            $conditions['dce_visibility_time_to'] = __('Time To', DCE_TEXTDOMAIN);
                            $everytimehidden = TRUE;
                        }
                    }
                }
                //}
                // USER & ROLES
                if (!isset($settings['dce_visibility_everyone']) || !$settings['dce_visibility_everyone']) {
                    $everyonehidden = FALSE;

                    //roles
                    if (isset($settings['dce_visibility_role']) && !empty($settings['dce_visibility_role'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_role'] = __('User Role', DCE_TEXTDOMAIN);
                        }
                        $current_user = wp_get_current_user();
                        if ($current_user && $current_user->ID) {
                            $user_roles = $current_user->roles; // possibile avere più ruoli
                            if (!is_array($user_roles)) {
                                $user_roles = array($user_roles);
                            }
                            if (is_array($settings['dce_visibility_role'])) {
                                $tmp_role = array_intersect($user_roles, $settings['dce_visibility_role']);
                                if (!empty($tmp_role)) {
                                    $conditions['dce_visibility_role'] = __('User Role', DCE_TEXTDOMAIN);
                                    $everyonehidden = TRUE;
                                }
                            }
                        } else {
                            if (in_array('visitor', $settings['dce_visibility_role'])) {
                                $conditions['dce_visibility_role'] = __('User not logged', DCE_TEXTDOMAIN);
                                $everyonehidden = TRUE;
                            }
                        }
                    }

                    // user
                    if (isset($settings['dce_visibility_users']) && $settings['dce_visibility_users'] && $settings['dce_visibility_users'] != '1') {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_users'] = __('Specific User', DCE_TEXTDOMAIN);
                        }
                        $users = $settings['dce_visibility_users'];
                        if ($users) {
                            $users = explode(',', $users);
                            $users = array_map('trim', $users);
                            $users = array_filter($users);
                        }
                        $is_user = false;
                        if (!empty($users)) {
                            $current_user = wp_get_current_user();
                            foreach ($users as $key => $value) {
                                if (is_numeric($value)) {
                                    if ($value == $current_user->ID) {
                                        $is_user = true;
                                    }
                                }
                                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    if ($value == $current_user->user_email) {
                                        $is_user = true;
                                    }
                                }
                                if ($value == $current_user->user_login) {
                                    $is_user = true;
                                }
                            }
                        }
                        //var_dump($is_user);
                        if ($is_user) {
                            $conditions['dce_visibility_users'] = __('Specific User', DCE_TEXTDOMAIN);
                            $everyonehidden = TRUE;
                        }
                    }

                    if (isset($settings['dce_visibility_usermeta']) && !empty($settings['dce_visibility_usermeta'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_usermeta'] = __('User Meta', DCE_TEXTDOMAIN);
                        }
                        $current_user = wp_get_current_user();
                        $usermeta = get_user_meta($current_user->ID, $settings['dce_visibility_usermeta'], true); // false for visitor
                        switch ($settings['dce_visibility_usermeta_status']) {
                            case 'isset':
                                if (!empty($usermeta)) {
                                    $conditions['dce_visibility_usermeta'] = __('User Meta', DCE_TEXTDOMAIN);
                                }
                                break;
                            case 'not':
                                if (empty($usermeta)) {
                                    $conditions['dce_visibility_usermeta'] = __('User Meta', DCE_TEXTDOMAIN);
                                }
                                break;
                            case 'value':
                                if ($usermeta == $settings['dce_visibility_usermeta_value']) {
                                    $conditions['dce_visibility_usermeta'] = __('User Meta', DCE_TEXTDOMAIN);
                                }
                        }
                    }



                    // GEOIP
                    if (DCE_Helper::is_plugin_active('geoip-detect') && function_exists('geoip_detect2_get_info_from_current_ip')) {
                        if (!empty($settings['dce_visibility_country'])) {
                            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                                $conditions['dce_visibility_country'] = __('Country', DCE_TEXTDOMAIN);
                            } else {
                                $geoinfo = geoip_detect2_get_info_from_current_ip();
                                if (in_array($geoinfo->country->isoCode, $settings['dce_visibility_country'])) {
                                    $conditions['dce_visibility_country'] = __('Country', DCE_TEXTDOMAIN);
                                }
                            }
                        }

                        if (!empty($settings['dce_visibility_city'])) {
                            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                                $conditions['dce_visibility_country'] = __('City', DCE_TEXTDOMAIN);
                            } else {
                                $geoinfo = geoip_detect2_get_info_from_current_ip();
                                $ucity = array_map('strtolower', $geoinfo->city->names);
                                $scity = DCE_Helper::str_to_array(',', $settings['dce_visibility_city'], 'strtolower');
                                $icity = array_intersect($ucity, $scity);
                                if (!empty($icity)) {
                                    $conditions['dce_visibility_country'] = __('City', DCE_TEXTDOMAIN);
                                }
                            }
                        }
                    }


                    // referrer
                    if (isset($settings['dce_visibility_referrer']) && $settings['dce_visibility_referrer'] && $settings['dce_visibility_referrer_list']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_referrer_list'] = __('Referer', DCE_TEXTDOMAIN);
                        }
                        if ($_SERVER['HTTP_REFERER']) {
                            $pieces = explode('/', $_SERVER['HTTP_REFERER']);
                            $referrer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST); //$pieces[2];
                            $referrers = explode(PHP_EOL, $settings['dce_visibility_referrer_list']);
                            $referrers = array_map('trim', $referrers);
                            $ref_found = false;
                            foreach ($referrers as $aref) {
                                if ($aref == $referrer || $aref == str_replace('www.', '', $referrer)) {
                                    $ref_found = true;
                                }
                            }
                            if ($ref_found) {
                                $conditions['dce_visibility_referrer_list'] = __('Referer', DCE_TEXTDOMAIN);
                                $everyonehidden = TRUE;
                            }
                        }/* else {
                          $everyonehidden = TRUE;
                          } */
                    }

                    if (isset($settings['dce_visibility_ip']) && $settings['dce_visibility_ip']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_ip'] = __('Remote IP', DCE_TEXTDOMAIN);
                        }
                        $ips = explode(',', $settings['dce_visibility_ip']);
                        $ips = array_map('trim', $ips);
                        if (in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                            $conditions['dce_visibility_ip'] = __('Remote IP', DCE_TEXTDOMAIN);
                            $everyonehidden = TRUE;
                        }
                    }
                }

                // DEVICE
                if (!isset($settings['dce_visibility_device']) || !$settings['dce_visibility_device']) {
                    $ahidden = FALSE;

                    // responsive
                    if (isset($settings['dce_visibility_responsive']) && $settings['dce_visibility_responsive']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_responsive'] = __('Responsive', DCE_TEXTDOMAIN);
                        }
                        if (wp_is_mobile()) {
                            if ($settings['dce_visibility_responsive'] == 'mobile') {
                                $conditions['dce_visibility_responsive'] = __('Responsive: is Mobile', DCE_TEXTDOMAIN);
                                $ahidden = TRUE;
                            }
                        } else {
                            if ($settings['dce_visibility_responsive'] == 'desktop') {
                                $conditions['dce_visibility_responsive'] = __('Responsive: is Desktop', DCE_TEXTDOMAIN);
                                $ahidden = TRUE;
                            }
                        }
                    }

                    // browser
                    if (isset($settings['dce_visibility_browser']) && is_array($settings['dce_visibility_browser']) && !empty($settings['dce_visibility_browser'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_browser'] = __('Browser', DCE_TEXTDOMAIN);
                        }
                        $is_browser = false;
                        foreach ($settings['dce_visibility_browser'] as $browser) {
                            global $$browser;
                            //var_dump($$browser);
                            if (isset($$browser) && $$browser) {
                                $is_browser = true;
                            }
                        }
                        //$hidden_browser = false;
                        if ($is_browser) {
                            $conditions['dce_visibility_browser'] = __('Browser', DCE_TEXTDOMAIN);
                            $ahidden = TRUE;
                        }
                    }
                }

                // CONTEXT
                if (!isset($settings['dce_visibility_context']) || !$settings['dce_visibility_context']) {
                    $contexthidden = false;

                    // cpt
                    if (isset($settings['dce_visibility_cpt']) && !empty($settings['dce_visibility_cpt']) && is_array($settings['dce_visibility_cpt'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_cpt'] = __('Post Type', DCE_TEXTDOMAIN);
                        }
                        $cpt = get_post_type();
                        //var_dump($cpt);
                        if (in_array($cpt, $settings['dce_visibility_cpt'])) {
                            $conditions['dce_visibility_cpt'] = __('Post Type', DCE_TEXTDOMAIN);
                            $contexthidden = true;
                        }
                    }

                    // post
                    //var_dump($settings['dce_visibility_post']);
                    if (isset($settings['dce_visibility_post']) && !empty($settings['dce_visibility_post']) && is_array($settings['dce_visibility_post'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_post'] = __('Post', DCE_TEXTDOMAIN);
                        }
                        $id = 0;
                        $queried_object = get_queried_object();
                        //if ( $queried_object instanceof WP_Post ) {
                        if ($queried_object && get_class($queried_object) == 'WP_Post') {
                            $id = $queried_object->ID;
                        }
                        if (in_array($id, $settings['dce_visibility_post'])) {
                            $conditions['dce_visibility_post'] = __('Post', DCE_TEXTDOMAIN);
                            $contexthidden = true;
                        }
                    }

                    // taxonomy
                    /* if (!empty($settings['dce_visibility_tax']) && is_array($settings['dce_visibility_tax'])) {
                      $tax = get_post_taxonomies();
                      //return $tax;
                      if (!array_intersect($tax, $settings['dce_visibility_tax'])) {
                      $conditions[] = __('Taxonomy', DCE_TEXTDOMAIN);
                      $contexthidden = true;
                      }
                      } */
                    if (isset($settings['dce_visibility_tax']) && $settings['dce_visibility_tax']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_tax'] = __('Taxonomy', DCE_TEXTDOMAIN);
                        }
                        //return $settings['dce_visibility_tax'];
                        $tax = get_post_taxonomies();
                        //return $tax;
                        if (!in_array($settings['dce_visibility_tax'], $tax)) {
                            $conditions['dce_visibility_tax'] = __('Taxonomy', DCE_TEXTDOMAIN);
                            $contexthidden = true;
                        } else {
                            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                                $conditions['terms'] = __('Terms', DCE_TEXTDOMAIN);
                            }
                            // term
                            $terms = get_the_terms(get_the_ID(), $settings['dce_visibility_tax']);
                            $terms = wp_list_pluck($terms, 'term_id');
                            //return $terms;
                            $tkey = 'dce_visibility_term_' . $settings['dce_visibility_tax'];
                            //return $settings[$tkey];
                            if (!empty($settings[$tkey]) && is_array($settings[$tkey])) {
                                if (array_intersect($terms, $settings[$tkey])) {
                                    $conditions[$tkey] = __('Terms', DCE_TEXTDOMAIN);
                                    $contexthidden = true;
                                    //return $tax;
                                }
                            } else {
                                if (!empty($terms)) {
                                    $conditions['terms'] = __('Terms', DCE_TEXTDOMAIN);
                                    $contexthidden = true;
                                }
                            }
                        }
                    }

                    // meta
                    if (isset($settings['dce_visibility_meta']) && is_array($settings['dce_visibility_meta']) && !empty($settings['dce_visibility_meta'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_meta'] = __('Post Metas', DCE_TEXTDOMAIN);
                        }
                        $post_metas = $settings['dce_visibility_meta'];
                        $metafirst = true;
                        $metavalued = false;
                        foreach ($post_metas as $mkey => $ameta) {
                            //var_dump($ameta);
                            if (is_author()) {
                                $author_id = get_the_author_meta('ID');
                                //var_dump($author_id);
                                $mvalue = get_user_meta($author_id, $ameta, true);
                            } else {
                                //var_dump(get_the_ID());
                                $mvalue = get_post_meta(get_the_ID(), $ameta, true);
                                if (is_array($mvalue) && empty($mvalue)) {
                                    $mvalue = false;
                                }
                            }
                            if ($settings['dce_visibility_meta_operator']) { // AND
                                if ($metafirst && $mvalue) {
                                    $metavalued = true;
                                }
                                if (!$metavalued || !$mvalue) {
                                    $metavalued = FALSE;
                                }
                            } else { // OR
                                if ($metavalued || $mvalue) {
                                    $metavalued = TRUE;
                                }
                            }
                            $metafirst = false;
                        }

                        if ($metavalued) {
                            $conditions['dce_visibility_meta'] = __('Post Metas', DCE_TEXTDOMAIN);
                            $contexthidden = TRUE;
                        }
                    }

                    if (isset($settings['dce_visibility_field']) && !empty($settings['dce_visibility_field'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_field'] = __('Post Meta', DCE_TEXTDOMAIN);
                        }
                        $postmeta = get_post_meta(get_the_ID(), $settings['dce_visibility_field'], true);
                        switch ($settings['dce_visibility_field_status']) {
                            case 'isset':
                                if (!empty($postmeta)) {
                                    $conditions['dce_visibility_field'] = __('Post Meta', DCE_TEXTDOMAIN);
                                }
                                break;
                            case 'not':
                                if (empty($postmeta)) {
                                    $conditions['dce_visibility_field'] = __('Post Meta', DCE_TEXTDOMAIN);
                                }
                                break;
                            case 'value':
                                if ($postmeta == $settings['dce_visibility_field_value']) {
                                    $conditions['dce_visibility_field'] = __('Post Meta', DCE_TEXTDOMAIN);
                                }
                        }
                    }

                    if (isset($settings['dce_visibility_parameter']) && $settings['dce_visibility_parameter']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_parameter'] = __('Parameter', DCE_TEXTDOMAIN);
                        }
                        switch ($settings['dce_visibility_parameter_status']) {
                            case 'isset':
                                if (isset($_REQUEST[$settings['dce_visibility_parameter']])) {
                                    $conditions['dce_visibility_parameter'] = __('Parameter', DCE_TEXTDOMAIN);
                                    $contexthidden = true;
                                }
                                break;
                            case 'not':
                                if (!isset($_REQUEST[$settings['dce_visibility_parameter']])) {
                                    $conditions['dce_visibility_parameter'] = __('Parameter', DCE_TEXTDOMAIN);
                                    $contexthidden = true;
                                }
                                break;
                            case 'value':
                                if (isset($_REQUEST[$settings['dce_visibility_parameter']]) && $_REQUEST[$settings['dce_visibility_parameter']] == $settings['dce_visibility_parameter_value']) {
                                    $conditions['dce_visibility_parameter'] = __('Parameter', DCE_TEXTDOMAIN);
                                    $contexthidden = true;
                                }
                        }
                    }

                    /* if (!empty($settings['dce_visibility_max_user']) && $settings['dce_visibility_max_user']) {
                      $dce_visibility_max = get_option('dce_visibility_max', array());
                      if (isset($dce_visibility_max[$element->get_id()]) && isset($dce_visibility_max[$element->get_id()]['user'])) {
                      if ($settings['dce_visibility_max_user'] < $dce_visibility_max[$element->get_id()]['user']) {
                      $conditions['dce_visibility_max_user'] = __('Max User', DCE_TEXTDOMAIN);
                      }
                      }
                      } */
                    if (!empty($settings['dce_visibility_max_day']) && $settings['dce_visibility_max_day']) {
                        $dce_visibility_max = get_option('dce_visibility_max', array());
                        //var_dump($dce_visibility_max);echo $element->get_id();
                        $today = date('Ymd');
                        if (isset($dce_visibility_max[$element->get_id()]) && isset($dce_visibility_max[$element->get_id()]['day']) && isset($dce_visibility_max[$element->get_id()]['day'][$today])) {
                            //var_dump($dce_visibility_max[$element->get_id()]['day'][$today]);
                            if ($settings['dce_visibility_max_day'] >= $dce_visibility_max[$element->get_id()]['day'][$today]) {
                                $conditions['dce_visibility_max_day'] = __('Max Day', DCE_TEXTDOMAIN);
                            }
                        } else {
                            $conditions['dce_visibility_max_day'] = __('Max Day', DCE_TEXTDOMAIN);
                        }
                    }
                    if (!empty($settings['dce_visibility_max_total']) && $settings['dce_visibility_max_total']) {
                        $dce_visibility_max = get_option('dce_visibility_max', array());
                        if (isset($dce_visibility_max[$element->get_id()]) && isset($dce_visibility_max[$element->get_id()]['total'])) {
                            //var_dump($dce_visibility_max[$element->get_id()]['total']);
                            if ($settings['dce_visibility_max_total'] >= $dce_visibility_max[$element->get_id()]['total']) {
                                $conditions['dce_visibility_max_total'] = __('Max Total', DCE_TEXTDOMAIN);
                            }
                        } else {
                            $conditions['dce_visibility_max_total'] = __('Max Total', DCE_TEXTDOMAIN);
                        }
                    }


                    if (isset($settings['dce_visibility_root']) && $settings['dce_visibility_root']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_root'] = __('Post is Root', DCE_TEXTDOMAIN);
                        }
                        $post_ID = get_the_ID();
                        if (!wp_get_post_parent_id($post_ID)) {
                            $conditions['dce_visibility_root'] = __('Post is Root', DCE_TEXTDOMAIN);
                        }
                    }

                    if (isset($settings['dce_visibility_format']) && !empty($settings['dce_visibility_format'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_format'] = __('Post Format', DCE_TEXTDOMAIN);
                        }
                        $post_ID = get_the_ID();
                        $format = get_post_format($post_ID) ?: 'standard';
                        if (in_array($format, $settings['dce_visibility_format'])) {
                            $conditions['dce_visibility_format'] = __('Post Format', DCE_TEXTDOMAIN);
                        }
                    }

                    if (isset($settings['dce_visibility_parent']) && $settings['dce_visibility_parent']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_parent'] = __('Post is Parent', DCE_TEXTDOMAIN);
                        }
                        $post_ID = get_the_ID();
                        $args = array(
                            'post_parent' => $post_ID,
                            'post_type' => get_post_type(),
                            'numberposts' => -1,
                            'post_status' => 'publish'
                        );
                        $children = get_children($args);
                        if (!empty($children) && count($children)) {
                            $conditions['dce_visibility_parent'] = __('Post is Parent', DCE_TEXTDOMAIN);
                        }
                    }

                    if (isset($settings['dce_visibility_leaf']) && $settings['dce_visibility_leaf']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_leaf'] = __('Post is Leaf', DCE_TEXTDOMAIN);
                        }
                        $post_ID = get_the_ID();
                        $args = array(
                            'post_parent' => $post_ID,
                            'post_type' => get_post_type(),
                            'numberposts' => -1,
                            'post_status' => 'publish'
                        );
                        $children = get_children($args);
                        if (empty($children)) {
                            $conditions['dce_visibility_leaf'] = __('Post is Leaf', DCE_TEXTDOMAIN);
                        }
                    }

                    if (isset($settings['dce_visibility_node']) && $settings['dce_visibility_node']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_node'] = __('Post is Node', DCE_TEXTDOMAIN);
                        }
                        $post_ID = get_the_ID();
                        if (wp_get_post_parent_id($post_ID)) {
                            $args = array(
                                'post_parent' => $post_ID,
                                'post_type' => get_post_type(),
                                'numberposts' => -1,
                                'post_status' => 'publish'
                            );
                            $children = get_children($args);
                            if (!empty($children)) {

                                $parents = get_post_ancestors();
                                $node_level = count($parents + 1);
                                if (!$settings['dce_visibility_node_level'] || $node_level == $settings['dce_visibility_node_level']) {
                                    $conditions['dce_visibility_node'] = __('Post is Node', DCE_TEXTDOMAIN);
                                }
                            }
                        }
                    }

                    if (isset($settings['dce_visibility_child']) && $settings['dce_visibility_child']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_child'] = __('Post has Parent', DCE_TEXTDOMAIN);
                        }
                        $post_ID = get_the_ID();
                        if ($post_parent_ID = wp_get_post_parent_id($post_ID)) {
                            $parent_ids = DCE_Helper::str_to_array(',', $settings['dce_visibility_child_parent']);
                            if (empty($settings['dce_visibility_child_parent']) || in_array($post_parent_ID, $parent_ids)) {
                                $conditions['dce_visibility_child'] = __('Post has Parent', DCE_TEXTDOMAIN);
                            }
                        }
                    }

                    if (isset($settings['dce_visibility_sibling']) && $settings['dce_visibility_sibling']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_sibling'] = __('Post has Siblings', DCE_TEXTDOMAIN);
                        }
                        $post_ID = get_the_ID();
                        if ($post_parent_ID = wp_get_post_parent_id($post_ID)) {
                            $args = array(
                                'post_parent' => $post_parent_ID,
                                'post_type' => get_post_type(),
                                'numberposts' => -1,
                                'post_status' => 'publish'
                            );
                            $children = get_children($args);
                            if (!empty($children) && count($children) > 1) {
                                $conditions['dce_visibility_sibling'] = __('Post has Siblings', DCE_TEXTDOMAIN);
                            }
                        }
                    }

                    if (isset($settings['dce_visibility_friend']) && $settings['dce_visibility_friend']) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_friend'] = __('Post has Friends', DCE_TEXTDOMAIN);
                        }
                        $post_ID = get_the_ID();
                        $posts_ID = array();
                        if ($settings['dce_visibility_friend_term']) {
                            $term = get_term($settings['dce_visibility_friend_term']);
                            $terms = array($term);
                        } else {
                            $terms = wp_get_post_terms();
                        }
                        if (!empty($terms)) {
                            foreach ($terms as $term) {
                                $post_args = array(
                                    'posts_per_page' => -1,
                                    'post_type' => get_post_type(),
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => $term->taxonomy,
                                            'field' => 'term_id', // this can be 'term_id', 'slug' & 'name'
                                            'terms' => $term->term_id,
                                        )
                                    )
                                );
                                $term_posts = get_posts($post_args);
                                if (!empty($term_posts) && count($term_posts) > 1) {
                                    $posts_ID = wp_list_pluck($term_posts, 'ID');
                                    if (in_array($post_ID, $posts_ID)) {
                                        $conditions['dce_visibility_friend'] = __('Post has Friends', DCE_TEXTDOMAIN);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                // CONDITONAL TAGS
                if (!isset($settings['dce_visibility_tags']) || !$settings['dce_visibility_tags']) {
                    $contexttags = false;
                    // conditional tags
                    if (isset($settings['dce_visibility_conditional_tags_post']) && is_array($settings['dce_visibility_conditional_tags_post']) && !empty($settings['dce_visibility_conditional_tags_post'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_conditional_tags_post'] = __('Conditional tags Post', DCE_TEXTDOMAIN);
                        }
                        $context_conditional_tags = false;
                        $post_type = get_post_type();
                        foreach ($settings['dce_visibility_conditional_tags_post'] as $conditional_tags) {
                            if (!$context_conditional_tags) {
                                switch ($conditional_tags) {
                                    case 'is_post_type_hierarchical':
                                    case 'is_post_type_archive':
                                        if (is_callable($conditional_tags)) {
                                            $context_conditional_tags = call_user_func($conditional_tags, $post_type);
                                        }
                                        break;
                                    case 'has_post_thumbnail':
                                        if (is_callable($conditional_tags)) {
                                            $context_conditional_tags = call_user_func($conditional_tags, get_the_ID());
                                        }
                                        break;
                                    default:
                                        if (is_callable($conditional_tags)) {
                                            $context_conditional_tags = call_user_func($conditional_tags);
                                        }
                                }
                            }
                        }
                        if ($context_conditional_tags) {
                            $conditions['dce_visibility_conditional_tags_post'] = __('Conditional tags Post', DCE_TEXTDOMAIN);
                            $contexttags = TRUE;
                        }
                    }
                    if (isset($settings['dce_visibility_conditional_tags_site']) && is_array($settings['dce_visibility_conditional_tags_site']) && !empty($settings['dce_visibility_conditional_tags_site'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_conditional_tags_site'] = __('Conditional tags Site', DCE_TEXTDOMAIN);
                        }
                        $context_conditional_tags = false;
                        foreach ($settings['dce_visibility_conditional_tags_site'] as $conditional_tags) {
                            if (!$context_conditional_tags) {
                                switch ($conditional_tags) {
                                    default:
                                        if (is_callable($conditional_tags)) {
                                            $context_conditional_tags = call_user_func($conditional_tags);
                                        }
                                }
                            }
                        }
                        if ($context_conditional_tags) {
                            $conditions['dce_visibility_conditional_tags_site'] = __('Conditional tags Site', DCE_TEXTDOMAIN);
                            $contexttags = TRUE;
                        }
                    }

                    // specials
                    if (isset($settings['dce_visibility_special']) && is_array($settings['dce_visibility_special']) && !empty($settings['dce_visibility_special'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_special'] = __('Conditional tags Special', DCE_TEXTDOMAIN);
                        }
                        $context_special = false;
                        foreach ($settings['dce_visibility_special'] as $special) {
                            if (!$context_special) {
                                switch ($special) {
                                    default:
                                        if (is_callable($special)) {
                                            $context_special = call_user_func($special);
                                        }
                                }
                            }
                        }
                        if ($context_special) {
                            $conditions['dce_visibility_special'] = __('Conditional tags Special', DCE_TEXTDOMAIN);
                            $contexttags = TRUE;
                        }
                    }

                    // archive
                    if (isset($settings['dce_visibility_archive']) && is_array($settings['dce_visibility_archive']) && !empty($settings['dce_visibility_archive'])) {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['dce_visibility_archive'] = __('Conditional tags Archive', DCE_TEXTDOMAIN);
                        }
                        $context_archive = false;
                        foreach ($settings['dce_visibility_archive'] as $archive) {
                            if (!$context_archive) {
                                switch ($archive) {
                                    case 'is_post_type_archive':
                                    case 'is_tax':
                                    case 'is_taxonomy':
                                    case 'is_category':
                                    case 'is_tag':
                                    case 'is_author':
                                    case 'is_date':
                                    case 'is_year':
                                    case 'is_month':
                                    case 'is_day':
                                    case 'is_search':
                                        if (is_callable($archive)) {
                                            $context_archive = call_user_func($archive);
                                        }
                                        break;
                                    default:
                                        $context_archive = is_archive();
                                }
                            }
                        }
                        if ($context_archive) { // || ($context_archive && !$settings['dce_visibility_context_selected'])) {
                            $conditions['dce_visibility_archive'] = __('Archive', DCE_TEXTDOMAIN);
                            $contexttags = TRUE;
                        }
                    }
                }

                if (isset($settings['dce_visibility_random']) && $settings['dce_visibility_random']['size']) {
                    $rand = mt_rand(1, 100);
                    if ($rand <= $settings['dce_visibility_random']['size']) {
                        $conditions['dce_visibility_random'] = __('Random', DCE_TEXTDOMAIN);
                        $randomhidden = true;
                    }
                }

                // CUSTOM CONDITION
                if (!isset($settings['dce_visibility_custom_condition']) || !$settings['dce_visibility_custom_condition']) {
                    $customhidden = false;
                    if (isset($settings['dce_visibility_custom_condition_php']) && trim($settings['dce_visibility_custom_condition_php']) && trim($settings['dce_visibility_custom_condition_php']) != 'return true;') {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            $conditions['custom'] = __('Custom Condition', DCE_TEXTDOMAIN);
                        }
                        $customhidden = $this->check_custom_condition($settings, $element->get_id());
                        //var_dump($customhidden);
                        if ($customhidden) {
                            $conditions['custom'] = __('Custom Condition', DCE_TEXTDOMAIN);
                        }
                    }
                }
            }

            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $hidden = true;
            }
        }

        //var_dump($hidden);

        $triggered = false;
        if (!empty($conditions)) {
            $triggered = true;
        }

        $shidden = $settings['dce_visibility_selected'];
        // retrocompatibility for 1.4
        if (isset($settings['dce_visibility_user_selected']) && !$settings['dce_visibility_user_selected']) {
            $shidden = FALSE;
        }
        if (isset($settings['dce_visibility_datetime_selected']) && !$settings['dce_visibility_datetime_selected']) {
            $shidden = FALSE;
        }
        if (isset($settings['dce_visibility_custom_condition_selected']) && !$settings['dce_visibility_custom_condition_selected']) {
            $shidden = FALSE;
        }
        if (isset($settings['dce_visibility_tags_selected']) && !$settings['dce_visibility_tags_selected']) {
            $shidden = FALSE;
        }
        if (isset($settings['dce_visibility_context_selected']) && !$settings['dce_visibility_context_selected']) {
            $shidden = FALSE;
        }
        if (isset($settings['dce_visibility_device_selected']) && !$settings['dce_visibility_device_selected']) {
            $shidden = FALSE;
        }

        if (self::check_visibility_condition($triggered, $shidden)) {
            $hidden = TRUE;
        }

        if ($why) {
            return $conditions;
        }

        return $hidden;
    }

    static public function check_visibility_condition($condition, $visibility) {
        $ret = $condition;
        if ($visibility) {
            if ($condition) {
                $ret = false; // mostro il widget
            } else {
                $ret = true; // nascondo il widget
            }
        } else {
            if ($condition) {
                $ret = true; // nascondo il widget
            } else {
                $ret = false; // mostro il widget
            }
        }
        return $ret;
    }

    public function check_custom_condition($settings, $eid = null) {
        $php_code = $settings['dce_visibility_custom_condition_php'];
        if ($php_code) {
            if (strpos($php_code, 'return ') !== false) {
                if ($settings['dce_visibility_custom_condition_secure']) {
                    $url = DCE_URL . 'assets/condition.php?pid=' . get_the_ID() . '&eid=' . $eid;
                    $custom_condition_result = wp_remote_get($url);
                    if ($custom_condition_result['body'] == '1') {
                        return true;
                    }
                } else {
                    // it may cause fatal error
                    $return = eval($php_code);
                    if ($return) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function print_conditions($element, $settings = null) {
        if (WP_DEBUG && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            //if ($this->is_hidden($element)) {
            if (empty($settings)) {
                $settings = $element->get_settings_for_display();
            }
            if ($settings['dce_visibility_debug']) {
                $conditions = $this->is_hidden($element, true);
                if (!empty($conditions)) {
                    //echo '<a href=".elementor-element-'.$element->get_ID().'" class="dce-btn-visibility"><i class="dce-icon-visibility fa fa-eye-slash" aria-hidden="true"></i></a>';
                    echo '<a onClick="jQuery(this).next().fadeToggle(); return false;" href="#box-visibility-debug-' . $element->get_ID() . '" class="dce-btn-visibility dce-btn-visibility-debug"><i class="dce-icon-visibility fa fa fa-eye exclamation-triangle" aria-hidden="true"></i></a>';
                    echo '<div id="#box-visibility-debug-' . $element->get_ID() . '" class="dce-box-visibility-debug"><ul>';
                    foreach ($conditions as $key => $value) {
                        echo '<li>';
                        echo $value;
                        if (isset($settings[$key])) {
                            echo ': ';
                            if (is_array($settings[$key])) {
                                if ($key == 'dce_visibility_random') {
                                    echo $settings[$key]['size'] . '%';
                                } else {
                                    echo implode(', ', $settings[$key]);
                                }
                            } else {
                                echo print_r($settings[$key], true);
                            }
                        }
                        echo '</li>';
                    }
                    echo '</ul></div>';
                }
            }
            //}
        }
    }

}
