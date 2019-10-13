<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor ACF-GoogleMaps
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */
class DCE_Widget_GoogleMaps extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-acf-google-maps';
    }

    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('ACF Google Maps', DCE_TEXTDOMAIN);
    }

    public function get_description() {
        return __('Build a map using data from a Google Maps ACF', DCE_TEXTDOMAIN);
    }

    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/acf-maps/';
    }

    public function get_icon() {
        return 'icon-dyn-map';
    }

    public function get_script_depends() {
        return ['dce-google-maps', 'dce-googlemaps-api'];
    }

    static public function get_position() {
        return 4;
    }

    /* public function get_style_depends() {
      return [ 'dce-acfGooglemap' ];
      } */

    public function get_plugin_depends() {
        return array('acf');
    }

    protected function _register_controls() {
        $taxonomies = \DynamicContentForElementor\DCE_Helper::get_taxonomies();

        $this->start_controls_section(
                'section_map', [
            'label' => __('ACF Google Maps', DCE_TEXTDOMAIN),
                ]
        );    

        $this->add_control(
                'map_data_type', [
            'label' => __('Data Type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'default' => 'acfmap',
            'options' => [
                'acfmap' => __('ACF Map Field', DCE_TEXTDOMAIN),
                'address' => __('Address', DCE_TEXTDOMAIN),
                'latlng' => __('Latitude Longitude', DCE_TEXTDOMAIN),
            ],
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'acf_mapfield', [
            'label' => __('ACF Map', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => DCE_Helper::get_all_acf(false, 'google_map'),
            'frontend_available' => true,
            'condition' => [
                'map_data_type' => 'acfmap',
            ],
                ]
        );
        //$default_location = get_field('map_location',$global_ID);
        //$default_address = "Venezia";
        //if( !empty($default_location) ) $default_address = $default_location['address'];
        $this->add_control(
                'address', [
            'label' => __('Manual address', DCE_TEXTDOMAIN),
            //'description' => __('Only works if the ACF field is not set', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            //'placeholder' => $default_address,
            'default' => 'Venice', //$default_address,
            'label_block' => true,
            //'readonly' => true,
            'condition' => [
                'map_data_type' => 'address',
            ],
                ]
        );
        $this->add_control(
                'latitudine', [
            'label' => __('Manual Latitude', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            //'placeholder' => $default_address,
            'default' => '45.4371908', //$default_address,
            //'readonly' => true,
            'condition' => [
                'map_data_type' => 'latlng',
            ],
                ]
        );
        $this->add_control(
                'longitudine', [
            'label' => __('Manual Longitude', DCE_TEXTDOMAIN),
            //'description' => __('Only works if the ACF field is not set', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            //'placeholder' => $default_address,
            'default' => '12.3345898', //$default_address,
            //'readonly' => true,
            'condition' => [
                'map_data_type' => 'latlng',
            ],
                ]
        );
        $this->add_control(
                'zoom', [
            'label' => __('Zoom Level', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 10,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 20,
                ],
            ],
            //'frontend_available' => true,
                ]
        );

        $this->add_responsive_control(
                'height', [
            'label' => __('Height', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 300,
            ],
            'tablet_default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'mobile_default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 40,
                    'max' => 1440,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} #el-wgt-map-{{ID}}' => 'height: {{SIZE}}{{UNIT}};',
            ],
            //'frontend_available' => true,
                ]
        );

        $this->add_control(
                'prevent_scroll', [
            'label' => __('Scroll', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', DCE_TEXTDOMAIN),
            'label_off' => __('No', DCE_TEXTDOMAIN),
            'render_type' => 'template',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'enable_infoWindow', [
            'label' => __('Info Window', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'render_type' => 'template',
            'frontend_available' => true,
                ]
        );

        $this->add_control(
                'view', [
            'label' => __('View', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HIDDEN,
            'default' => 'traditional',
                ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
                'section_mapInfoWIndow', [
            'label' => __('Info Window', DCE_TEXTDOMAIN),
            'condition' => [
                'enable_infoWindow' => 'yes'
            ]
                ]
        );

        $this->add_control(
                'infoWindow_click_to_post', [
            'label' => __('Link to post', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'separator' => 'after',
            'condition' => [
                'map_data_type' => 'acfmap',
                'acf_mapfield!' => '',
                'use_query' => 'yes',
            ],
                ]
        );
        /* $this->add_control(
          'infoWindow_heading_content',
          [
          'label' => __( 'InfoWindow Content', DCE_TEXTDOMAIN ),
          'type' => Controls_Manager::HEADING,
          'separator' => 'before',
          'condition' => [
          'infoWindow_click_to_post' => '',
          ],
          ]
          );
          $this->add_control(
          'infoWindow_title', [
          'label' => __('Title', DCE_TEXTDOMAIN),
          'type' => Controls_Manager::SWITCHER,
          'default' => '',
          'label_on' => __('Yes', DCE_TEXTDOMAIN),
          'label_off' => __('No', DCE_TEXTDOMAIN),
          'frontend_available' => true,
          'condition' => [
          'acf_mapfield!' => '',
          'use_query' => 'yes',
          ],
          ]
          );
          $this->add_control(
          'infoWindow_image', [
          'label' => __('Image', DCE_TEXTDOMAIN),
          'type' => Controls_Manager::SWITCHER,
          'default' => '',
          'label_on' => __('Yes', DCE_TEXTDOMAIN),
          'label_off' => __('No', DCE_TEXTDOMAIN),
          'frontend_available' => true,
          'condition' => [
          'acf_mapfield!' => '',
          'use_query' => 'yes',
          ],
          ]
          ); */
        $this->add_control(
                'custom_infoWindow',
                [
                    'label' => __('Custom text', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'frontend_available' => true,
                    'label_block' => true,
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                        //'acf_mapfield' => '',
                        'use_query' => '',
                    ],
                ]
        );
        $this->add_control(
                'infoWindow_heading_style',
                [
                    'label' => __('InfoWindow Style', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'infowindow_typography',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c',
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_control(
                'infoWindow_textColor', [
            'label' => __('Text Color', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );


        $this->add_control(
                'infoWindow_bgColor', [
            'label' => __('Background Color', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'infoWindow_border',
            'label' => __('Image Border', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after',
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_control(
                'infoWindow_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_control(
                'infoWindow_border_radius', [
            'label' => __('Border Radius', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'infoWindow_box_shadow',
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c',
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
                'section_mapMarker', [
            'label' => __('Marker', DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'acf_markerfield', [
            'label' => __('Map (ACF)', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => DCE_Helper::get_all_acf(false, 'image'),
            'default' => '0',
                ]
        );
        $this->add_control(
                'imageMarker', [
            'label' => __('Marker Image', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => '', //'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
            ],
            'frontend_available' => true,
            'condition' => [
                'acf_markerfield' => '0',
            ],
                ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
                'section_mapStyles', [
            'label' => __('Styles', DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'map_type', [
            'label' => __('Map Type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'default' => 'roadmap',
            'options' => [
                'roadmap' => __('Roadmap', DCE_TEXTDOMAIN),
                'satellite' => __('Satellite', DCE_TEXTDOMAIN),
                'hybrid' => __('Hybrid', DCE_TEXTDOMAIN),
                'terrain' => __('Terrain', DCE_TEXTDOMAIN),
            ],
            'frontend_available' => true,
                ]
        );
        // --------------------------------- [ ACF Type of style ]
        $this->add_control(
                'style_select', [
            'label' => __('Style', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => __('None', DCE_TEXTDOMAIN),
                'custom' => __('Custom', DCE_TEXTDOMAIN),
                'prestyle' => __('Snazzy Style', DCE_TEXTDOMAIN),
            ],
            'default' => '',
            'frontend_available' => true,
            'condition' => [
                'map_type' => 'roadmap',
            ],
                ]
        );
        $this->add_control(
                'snazzy_select', [
            'label' => __('Snazzy Style', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT2,
            'options' => $this->snazzymaps(),
            'frontend_available' => true,
            'condition' => [
                'map_type' => 'roadmap',
                'style_select' => 'prestyle',
            ],
                ]
        );
        $this->add_control(
                'style_map', [
            'label' => __('Copy Snazzy Json Style Map', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXTAREA,
            'default' => __('', DCE_TEXTDOMAIN),
            'description' => 'To better manage the graphic styles of the map go to: <a href="https://snazzymaps.com/" target="_blank">snazzymaps.com</a>',
            'frontend_available' => true,
            'condition' => [
                'map_type' => 'roadmap',
                'style_select' => 'custom',
            ],
                ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
                'section_mapControls', [
            'label' => __('Controls', DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'maptypecontrol', [
            'label' => __('Map Type Control', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'pancontrol', [
            'label' => __('Pan Control', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );

        $this->add_control(
                'rotatecontrol', [
            'label' => __('Rotate Control', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'scalecontrol', [
            'label' => __('Scale Control', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'streetviewcontrol', [
            'label' => __('Street View Control', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'zoomcontrol', [
            'label' => __('Zoom Control', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'fullscreenControl', [
            'label' => __('Full Screen Control', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
                'section_cpt', [
            'label' => __('Post Type Query', DCE_TEXTDOMAIN),
            'condition' => [
                'map_data_type' => 'acfmap',
                'acf_mapfield!' => ''
            ]
                ]
        );
        // --------------------------------- [ Use Query post ]
        $this->add_control(
                'use_query', [
            'label' => __('Use Query', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
                ]
        );
        // --------------------------------- [ Query Type ]
        $this->add_control(
                'query_type', [
            'label' => __('Query Type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'get_cpt' => [
                    'title' => 'Custom Post Type',
                    'icon' => 'fa fa-files-o',
                ],
                'acf_relations' => [
                    'title' => 'ACF Relations',
                    'icon' => 'fa fa-american-sign-language-interpreting',
                ],
                'specific_posts' => [
                    'title' => 'From Specific Post',
                    'icon' => 'fa fa-list-ul',
                ]
            ],
            'default' => 'get_cpt',
            'condition' => [
                'use_query' => 'yes',
            ],
                ]
        );
        // --------------------------------- [ Custom Post Type ]
        $this->add_control(
                'post_type', [
            'label' => __('Post Type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => DCE_Helper::get_post_types(),
            'multiple' => true,
            'label_block' => true,
            'default' => 'post',
            'condition' => [
                'use_query' => 'yes',
                'query_type' => 'get_cpt',
            ],
                ]
        );
        $this->add_control(
                'taxonomy', [
            'label' => __('Taxonomy', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            //'options' => get_post_taxonomies( $post->ID ),
            'options' => ['' => __('None', DCE_TEXTDOMAIN)] + get_taxonomies(array('public' => true)),
            'default' => '',
            'condition' => [
                'use_query' => 'yes',
                'query_type' => 'get_cpt',
            ],
                ]
        );
        $this->add_control(
                'category', [
            'label' => __('Terms ID', DCE_TEXTDOMAIN),
            'description' => __('Comma separated list of category ids', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HIDDEN,
            'default' => '',
            'condition' => [
                'use_query' => 'yes',
                'query_type' => 'get_cpt',
            ],
                ]
        );
        $this->add_control(
                'terms_current_post', [
            'label' => __('Use Dynamic Current Post Terms (Archive)', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Filter results by taxonomy terms associated to current post', DCE_TEXTDOMAIN),
            'condition' => [
                'taxonomy!' => '',
                'query_type' => ['get_cpt', 'dynamic_mode'],
                'use_query' => 'yes',
            //'terms_from_acf' => ''
            ],
                ]
        );
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control(
                        'terms_' . $tkey, [
                    'label' => __('Terms', DCE_TEXTDOMAIN), //.' '.$atax,
                    'type' => Controls_Manager::SELECT2,
                    //'groups' => \DynamicContentForElementor\DCE_Helper::get_taxonomies_terms(),
                    'options' => ['' => __('All', DCE_TEXTDOMAIN)] + \DynamicContentForElementor\DCE_Helper::get_taxonomy_terms($tkey), // + ['dce_current_post_terms' => __('Dynamic Current Post Terms', DCE_TEXTDOMAIN)],
                    'description' => __('Filter results by selected taxonomy term', DCE_TEXTDOMAIN),
                    'multiple' => true,
                    'label_block' => true,
                    'condition' => [
                        //'taxonomy!' => '',
                        'query_type' => ['get_cpt', 'dynamic_mode'],
                        'taxonomy' => $tkey,
                        'terms_current_post' => '',
                    //'terms_from_acf' => ''
                    ],
                    'render_type' => 'template',
                    'use_query' => 'yes',
                    'dynamic' => [
                        'active' => true,
                    ],
                        ]
                );
            }
        }
        // --------------------------------- [ ACF relations ]
        $this->add_control(
                'acf_relationship', [
            'label' => __('Relations (ACF)', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            //'options' => get_post_taxonomies( $post->ID ),
            'options' => DCE_Helper::get_all_acf(false, 'relationship'),
            'default' => '0',
            'condition' => [
                'query_type' => 'acf_relations',
            ],
                ]
        );
        // --------------------------------- [ Specific Pages ]
        $this->add_control(
                'specific_pages', [
            'label' => __('Posts', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT2,
            'options' => DCE_Helper::get_all_posts(),
            'multiple' => true,
            'label_block' => true,
            'condition' => [
                'query_type' => 'specific_posts',
            ],
                ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
                'section_dce_settings', [
            'label' => __('Dynamic content', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_SETTINGS,
                ]
        );
        $this->add_control(
                'data_source',
                [
                    'label' => __('Source', DCE_TEXTDOMAIN),
                    'description' => __('Select the data source', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'label_on' => __('Same', DCE_TEXTDOMAIN),
                    'label_off' => __('Other', DCE_TEXTDOMAIN),
                    'return_value' => 'yes',
                ]
        );
        $this->add_control(
                'other_post_source', [
            'label' => __('Select from other source post', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => DCE_Helper::get_all_posts(),
            'default' => '',
            'condition' => [
                'data_source' => '',
            ],
                ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display(null, false);
        if (empty($settings))
            return;
        //
        //
        // ------------------------------------------
        $dce_data = DCE_Helper::dce_dynamic_data($settings['other_post_source']);
        $id_page = $dce_data['id'];
        $type_page = $dce_data['type'];
        $global_is = $dce_data['is'];
        // ------------------------------------------
        //
        $zoom = $settings['zoom']['size'];
        if (!$zoom) {
            $zoom = 10;
        }

        $imageMarker = get_field($settings['acf_markerfield'], $id_page);
        if (is_string($imageMarker)) {
            //echo 'url: '.$imageMarker;
            $imageSrc = $imageMarker;
        } else if (is_numeric($imageMarker)) {
            //echo 'id: '.$imageMarker;
            $imageSrc = wp_get_attachment_image_src($imageMarker, 'full');
            $imageMarker = $imageSrc[0];
        } else if (is_array($imageMarker)) {
            //echo 'array: '.$imageMarker;
            $imageSrc = wp_get_attachment_image_src($imageMarker['ID'], 'full');
            $imageMarker = $imageSrc[0];
        }
        if ($imageMarker == '') {
            $imageMarker = $settings['imageMarker']['url'];
        }

        //$imageMarker = $settings['imageMarker'];
        //var_dump( $imageMarker );
        $infoWindow_str = '';
        // se la infoWindow è abilitata..
        // Le possibilità: statico, da ACF singolo, da ACF Query
        if ($settings['enable_infoWindow']) {
                $infoWindow_str = $settings['custom_infoWindow'];
                /* if($settings['acf_mapfield']){

                  }else{
                  $infoWindow_str = $settings['address'];
                  } */
        }
            
        if (!$settings['use_query']) {

            $map_data_type = $settings['map_data_type'];
            $indirizzo = $settings['address'];
            
            $lat = $settings['latitudine'];
            $lng = $settings['longitudine'];

            if ($settings['acf_mapfield']) {
                $location = get_field($settings['acf_mapfield'], $id_page);
                //$location = unserialize(get_post_meta( $id_page, $settings['acf_mapfield'], true ));
                //var_dump($location);
                //echo $id_page;
                if (!empty($location)) {
                    $indirizzo = $location['address'];
                    $lat = $location['lat'];
                    $lng = $location['lng'];
                }
            }
            //$indirizzo = $location['address'];
            //echo $indirizzo;
            /*
              echo $settings['height']['size'];
              echo $settings['zoom']['size'];
             */
            //$location['address'] = $indirizzo;
            //echo $indirizzo;
        } else {
            /* -------------------------- Query ------------------------ */
            // ARGS
            if ($settings['query_type'] == 'specific_posts') {
                $args = array(
                    'post_type' => 'any',
                    //'posts_per_page'    => -1,
                    'post__in' => $settings['specific_pages'],
                    'post_status' => 'publish',
                    'order_by' => 'post__in',
                );
                //acf_relationship
            } else if ($settings['query_type'] == 'acf_relations') {
                $relations_ids = get_field($settings['acf_relationship'], $id_page, false);
                //$relations_ids = unserialize(get_post_meta( $id_page, $settings['acf_relationship'] ));
                if (!empty($relations_ids)) {
                    $relations_type = get_post_type($relations_ids[0]);
                    //echo $relations_type;
                    $args = array(
                        'post_type' => 'any',
                        'posts_per_page' => -1,
                        'post__in' => $relations_ids,
                        'post_status' => 'publish',
                        'orderby' => 'menu_order',
                    );
                }
            } else if ($settings['query_type'] == 'get_cpt') {
                $terms_query = 'all';
                $taxquery = array();
                if ($settings['category'] != '') {
                    $terms_query = explode(',', $settings['category']);
                }
                if ($settings['terms_current_post']) {

                    if (is_single()) {
                        $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => true));
                        //var_dump($terms_list);
                        if (!empty($terms_list)) {
                            $terms_query = array();
                            foreach ($terms_list as $akey => $aterm) {
                                if (!in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }

                    if (is_archive()) {
                        if (is_tax()) {
                            $queried_object = get_queried_object();
                            $terms_query = array($queried_object->term_id);
                        }
                    }
                }
                if (isset($settings['terms_' . $settings['taxonomy']]) && !empty($settings['terms_' . $settings['taxonomy']])) {
                    $terms_query = $settings['terms_' . $settings['taxonomy']];
                    // add current post terms id
                    $dce_key = array_search('dce_current_post_terms', $terms_query);
                    if ($dce_key !== false) {
                        //var_dump($dce_key);
                        unset($terms_query[$dce_key]);
                        $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => true));
                        if (!empty($terms_list)) {
                            $terms_query = array();
                            foreach ($terms_list as $akey => $aterm) {
                                if (!in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }
                }
                if ($settings['taxonomy'] != "")
                    $taxquery = array(
                        array(
                            'taxonomy' => $settings['taxonomy'],
                            'field' => 'id',
                            'terms' => $terms_query
                        )
                    );
                $args = array(
                    'post_type' => $settings['post_type'],
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'tax_query' => $taxquery,
                );
            }
            
            // QUERY
            $p_query = new \WP_Query($args);
            //echo $p_query->found_posts;
            $counter = 0;
            //var_dump($args);
            if ($p_query->have_posts()) :
                /*while ($p_query->have_posts()) : $p_query->the_post();
                    $id_page = get_the_ID();
                    $map_field = get_field($settings['acf_mapfield']);
                //var_dump($map_field);
                // if(isset($map_field) && $map_field != '' && $map_field['address']){
                // echo '<br> a '.$map_field['address'];
                //  } 
                endwhile;
                wp_reset_postdata();
                */
                //var_dump(get_field($settings['acf_mapfield']));
                ?>
                <script>
                    var address_list = [<?php
                while ($p_query->have_posts()) {
                    $p_query->the_post();
                    $id_page = get_the_ID();

                    $map_field = get_field($settings['acf_mapfield'], get_the_ID());

                    if (!empty($map_field)) {
                        //var_dump($map_field);
                        $indirizzo = $map_field['address'];
                        $lat = $map_field['lat'];
                        $lng = $map_field['lng'];
                        $postlink = get_the_permalink($id_page);
                        $postTitle = get_the_title($id_page);

                        //$map_field = get_post_meta( $id_page, $settings['acf_mapfield'] );
                        $marker_img = get_field($settings['acf_markerfield']);
                        if (is_string($marker_img)) {
                            //echo 'url: '.$marker_img;
                            $imageSrc = $marker_img;
                        } else if (is_numeric($marker_img)) {
                            //echo 'id: '.$marker_img;
                            $imageSrc = wp_get_attachment_image_src($marker_img, 'full');
                            $marker_img = $imageSrc[0];
                        } else if (is_array($marker_img)) {
                            //echo 'array: '.$marker_img;
                            $imageSrc = wp_get_attachment_image_src($marker_img['ID'], 'full');
                            $marker_img = $imageSrc[0];
                        }
                        if ($marker_img == '') {
                            $marker_img = $imageMarker;
                        }
                        //$marker_img = get_post_meta( $id_page, $settings['acf_markerfield'] );

                        if ($counter > 0) {
                            echo ', ';
                        }
                        echo '{"address":"' . $indirizzo . '",';
                        echo '"lat":"' . $lat . '",';
                        echo '"lng":"' . $lng . '",';
                        echo '"marker":"' . $marker_img . '",';
                        echo '"postLink":"' . $postlink . '",';
                        echo '"infoWindow": "' . $postTitle . '"}';
                        //var_dump($map_field);
                        $counter++;
                    }
                }
                ?>];
                </script>
                <?php
                // Reset the post data to prevent conflicts with WP globals
                wp_reset_postdata();
            endif;
            /* ----------------------------------------------------------------- END Query */
        } // end if use_query
        //var_dump($counter);
        ?>

        <style>
            #el-wgt-map-<?php echo $this->get_id(); ?>{
                width: 100%;
                background-color: #ccc;
            }
        </style>
        <span id="debug" style="display: none;"></span>

        <div id="el-wgt-map-<?php echo $this->get_id(); ?>" 
             data-address="<?php echo $indirizzo; ?>" 
             data-lat="<?php echo $lat; ?>" 
             data-lng="<?php echo $lng; ?>" 
             data-zoom="<?php echo $zoom; ?>" 
             data-imgmarker="<?php echo $imageMarker; ?>" 
             data-infowindow="<?php echo $infoWindow_str ?>"
          >
        </div>
        <?php
    }

    protected function _content_template() {
        
    }

    protected function snazzymaps() {
        $snazzy_list = [];
        $snazzy_styles = glob(DCE_PATH . 'assets/maps_style/*.json');
        if (!empty($snazzy_styles)) {
            foreach ($snazzy_styles as $key => $value) {
                $snazzy_name = basename($value);
                $snazzy_name = str_replace('.json', '', $snazzy_name);
                $snazzy_name = str_replace('_', ' ', $snazzy_name);
                $snazzy_name = ucfirst($snazzy_name);
                $snazzy_url = str_replace('.json', '', $value);
                $snazzy_url = str_replace(DCE_PATH, DCE_URL, $snazzy_url);
                $snazzy_list[$snazzy_url] = $snazzy_name;
            }
        }
        // ciclo la cartellina maps_style in assets e ricavo la lista dei files ....
        //$snazzy_list[DCE_URL.'assets/maps_style/extra_light'] = 'Extra Light';
        //$snazzy_list[DCE_URL.'assets/maps_style/extra_black'] = 'Extra black';

        return $snazzy_list;
    }

}
