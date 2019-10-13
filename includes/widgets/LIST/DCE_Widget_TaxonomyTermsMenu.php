<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\DCE_Helper;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Taxonomy & Terms Menu
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */
class DCE_Widget_TaxonomyTermsMenu extends DCE_Widget_Prototype {

    public function get_name() {
            return 'taxonomy-terms-menu';
    }
    static public function is_enabled() {
        return true;
    }

    public function get_title() {
            return __( 'Taxonomy Terms List', DCE_TEXTDOMAIN );
    }
    public function get_description() {
        return __('Write a taxonomy for your article', DCE_TEXTDOMAIN);
    }
    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/taxonomy-terms-list/';
    }
    public function get_icon() {
            return 'icon-dyn-parenttax';
    }
    /*public function get_style_depends() {
        return [ 'dce-list' ];
    }*/
    protected function _register_controls() {
        
        $this->start_controls_section(
            'section_content',
            [
                    'label' => __( 'Menu of terms from Taxonomy', DCE_TEXTDOMAIN ),
            ]
        );
        $this->add_control(
            'taxonomy_select',
            [
                'label' => __( 'Select Taxonomy', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => DCE_Helper::get_taxonomies(),
                'default' => '',

            ]
        );
        $this->add_control(
            'menu_style',
            [
                'label' => __( 'Style', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'horizontal' => __( 'Horizontal', DCE_TEXTDOMAIN ),
                    'vertical' => __( 'Vertical', DCE_TEXTDOMAIN )
                ],
                'default' => 'vertical',

            ]
        );
        $this->add_responsive_control(
            'item_width',
            [
                'label' => __( 'Items width', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                        'size' => '',
                ],
                'range' => [
                        '%' => [
                                'min' => 0,
                                'max' => 100,
                        ],
                ],
                'selectors' => [
                        '{{WRAPPER}} .dce-menu.horizontal li' => 'width: {{SIZE}}%;',
                ],
                'condition' => [
                    'menu_style' => 'horizontal',
                ],
            ]
        );
        $this->add_control(
        'taxonomy_dynamic',
            [
                'label'         => __( 'Enable Dynamic', DCE_TEXTDOMAIN ),
                'description'   => __('Change to depending on the page that displays it', DCE_TEXTDOMAIN),
                'type'          => Controls_Manager::SWITCHER,
                'default'       => '',
                'label_on'      => __( 'Yes', DCE_TEXTDOMAIN ),
                'label_off'     => __( 'No', DCE_TEXTDOMAIN ),
                'return_value'  => 'yes',
                'sparator'      => 'before'
            ]
        );
        $this->add_control(
        'hide_empty',
            [
                'label'         => __( 'Hide Empty', DCE_TEXTDOMAIN ),
                'type'          => Controls_Manager::SWITCHER,
                'default'       => 'yes',
                'label_on'      => __( 'Yes', DCE_TEXTDOMAIN ),
                'label_off'     => __( 'No', DCE_TEXTDOMAIN ),
                'return_value'  => 'yes',
                'sparator'      => 'before'
            ]
        );
        
        $this->add_control(
            'heading_options_menu',
            [
                'label' => __( 'Options', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'show_taxonomy',
            [
                'label' => __( 'Show Taxonomy Name', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'label_block' => false,
                'options' => [
                    '1' => [
                        'title' => __( 'Yes', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __( 'No', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-ban',
                    ]
                ],
                'default' => '1'
            ]
        );


        //
        $this->add_control(
            'tax_text',
            [
                'label' => __( 'Custom Taxonomy Name', DCE_TEXTDOMAIN ),
                'description' => __('If you do not want to use your native label',DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'show_taxonomy' => '1',
                ],
            ]
        );
        $this->add_control(
            'show_childlist',
            [
                'label' => __( 'Show Child List', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    '1' => [
                        'title' => __( 'Yes', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __( 'No', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-ban',
                    ]
                ],
                'default' => '1'
            ]
        );
        $this->add_responsive_control(
            'show_border',
            [
                'label' => __( 'Show Border', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    '1' => [
                        'title' => __( 'Yes', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __( 'No', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-ban',
                    ],
                    '2' => [
                        'title' => __( 'Any', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-square-o',
                    ]
                ],
                'default' => '1'
            ]
        );
        $this->add_responsive_control(
            'show_separators',
            [
                'label' => __( 'Show Separator', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'solid' => [
                        'title' => __( 'Yes', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-check',
                    ],
                    'none' => [
                        'title' => __( 'No', DCE_TEXTDOMAIN ),
                        'icon' => 'fa fa-ban',
                    ],
                ],
                'condition' => [
                    'menu_style' => 'horizontal',
                ],
                'toggle' => true,
                'default' => 'solid',
                'selectors' => [
                    '{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-style: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'menu_color_separator',
            [
                'label' => __( 'Separator Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
               
                'condition' => [
                    'show_separators' => 'solid',
                     'menu_style' => 'horizontal',
                ],
                'default' => '#999999',
                'selectors' => [
                        '{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'menu_size_separator',
            [
                'label' => __( 'Separator Width', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                    'unit' => 'px',
                ],
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_separators' => 'solid',
                     'menu_style' => 'horizontal',
                ],
            ]
        );
        $this->add_responsive_control(
            'menu_align',
            [
                'label' => __( 'Text Alignment', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'left' => [
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
                    ]
                ],
                'default' => 'left',
                'prefix_class' => 'menu-align-',
                'selectors' => [
                     '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_spaces_menu',
            [
                'label' => __( 'Space', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'menu_space',
            [
                'label' => __( 'Header Space', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                        'size' => 0,
                ],
                'range' => [
                        'px' => [
                                'min' => 0,
                                'max' => 100,
                        ],
                ],
                'selectors' => [
                        '{{WRAPPER}} .dce-menu .dce-parent-title' => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2);',
                        '{{WRAPPER}} .dce-menu hr' => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2);',
                        '{{WRAPPER}} .dce-menu div.box' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'menu_list_space',
            [
                'label' => __( 'List Space', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                        'size' => 0,
                ],
                'range' => [
                        'px' => [
                                'min' => 0,
                                'max' => 100,
                        ],
                ],
                'selectors' => [
                        '{{WRAPPER}} .dce-menu ul.first-level li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_childlist' => '1',
                ],
            ]
        );
        $this->add_control(
            'menu_indent',
            [
                'label' => __( 'Indent', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                        'size' => 10,
                ],
                'range' => [
                        'px' => [
                                'min' => 0,
                                'max' => 100,
                        ],
                ],
                'selectors' => [
                        '{{WRAPPER}} .dce-menu li' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        if(DCE_Helper::is_plugin_active('acf')){
            $this->add_control(
                'heading_image_acf',
                [
                    'label' => __( 'ACF Term image', DCE_TEXTDOMAIN ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $this->add_control(
            'image_acf_enable',
                [
                    'label'         => __( 'Enable', DCE_TEXTDOMAIN ),
                    'type'          => Controls_Manager::SWITCHER,
                    'default'       => '',
                    'label_on'      => __( 'Yes', DCE_TEXTDOMAIN ),
                    'label_off'     => __( 'No', DCE_TEXTDOMAIN ),
                    'return_value'  => 'yes',
                ]
            );
            $this->add_control(
                'acf_field_image', [
                    'label' => __('ACF Field', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT,
                    //'options' => $this->get_acf_field(),
                    'groups' => $this->get_acf_field_image(true),
                    'default' => 'Select the Field',
                    'condition' => [
                            'image_acf_enable' => 'yes',
                        ]
                ]
            );
            $this->add_group_control(
                Group_Control_Image_Size::get_type(), [
                    'name' => 'size',
                    'label' => __('Image Size', DCE_TEXTDOMAIN),
                    'default' => 'large',
                    'render_type' => 'template',
                    'condition' => [
                            'image_acf_enable' => 'yes',
                        ]
                ]
            );
            $this->add_control(
                'block_enable', [
                    'label' => __('Block', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                    'label_on' => __('Yes', DCE_TEXTDOMAIN),
                    'label_off' => __('No', DCE_TEXTDOMAIN),
                    'return_value' => 'block',
                    'selectors' => [
                        '{{WRAPPER}} .dce-menu li img' => 'display: {{VALUE}};',
                    ],
                    'condition' => [
                            'image_acf_enable' => 'yes',
                        ],
                ]
            );
            $this->add_control(
                'image_acf_space',
                [
                    'label' => __( 'Space', DCE_TEXTDOMAIN ),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                            'size' => 0,
                    ],
                    'range' => [
                            'px' => [
                                    'min' => 0,
                                    'max' => 100,
                            ],
                    ],
                    'selectors' => [
                            '{{WRAPPER}} .dce-menu li img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                            'image_acf_enable' => 'yes',
                            'block_enable' => 'block'
                        ],
                ]
            );
            $this->add_control(
                'image_acf_space_right',
                [
                    'label' => __( 'Space', DCE_TEXTDOMAIN ),
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
                            '{{WRAPPER}} .dce-menu li img' => 'margin-right: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                            'image_acf_enable' => 'yes',
                            'block_enable' => ''
                        ],
                ]
            );
            $this->add_responsive_control(
                'space', [
                    'label' => __('Size (%)', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        
                        'unit' => '%',
                    ],
                    'size_units' => [ '%','px'],
                    'range' => [
                        '%' => [
                            'min' => 1,
                            'max' => 100,
                        ],
                        'px' => [
                            'min' => 1,
                            'max' => 800,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-menu li img' => 'max-width: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                            'image_acf_enable' => 'yes',
                        ],
                        
                ]
            );
        }// end is_plugin_active
        $this->end_controls_section();
        // ------------------------------------------ STYLE
        $this->start_controls_section(
            'section_style',
            [
                    'label' => __( 'Style', DCE_TEXTDOMAIN ),
                    'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'heading_colors',
            [
                    'label' => __( 'List items', DCE_TEXTDOMAIN ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
            ]
        );
        $this->add_control(
            'menu_color',
            [
                'label' => __( 'Text Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                
                'condition' => [
                    'show_childlist' => '1',
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dce-menu a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'menu_color_hover',
            [
                'label' => __( 'Text Hover Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                
                'condition' => [
                    'show_childlist' => '1',
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dce-menu a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'menu_color_active',
            [
                'label' => __( 'Text Active Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                
                'condition' => [
                    'show_childlist' => '1',
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dce-menu a.active' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_list',

                'selector' => '{{WRAPPER}} .dce-menu li',
            ]
        );

        
        $this->add_control(
            'heading_title',
            [
                'label' => __( 'Title', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                        'show_taxonomy' => '1',
                ],
            ]
        );
        $this->add_control(
            'menu_title_color',
            [
                'label' => __( 'Title Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                
                'condition' => [
                        'show_taxonomy' => '1',
                ],
                'default' => '',
                'selectors' => [
                        '{{WRAPPER}} .dce-menu .dce-parent-title a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'menu_title_color_hover',
            [
                'label' => __( 'Title Hover Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                
                'default' => '',
                'selectors' => [
                        '{{WRAPPER}} .dce-menu .dce-parent-title a:hover' => 'color: {{VALUE}};',
                ],
                'condition' => [
                        'show_taxonomy' => '1',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_tit',

                'selector' => '{{WRAPPER}} .dce-menu .dce-parent-title',
                'condition' => [
                        'show_taxonomy' => '1',
                ],
            ]
        );

        $this->add_control(
            'heading_border',
            [
                'label' => __( 'Border', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_border' => ['1','2'],
                ],
            ]
        );

        $this->add_control(
            'menu_border_color',
            [
                'label' => __( 'Border Color', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'toggle' => false,
                'label_block' => false,
                'default' => '',
                'condition' => [
                    'show_border' => ['1','2'],
                ],
                'selectors' => [
                        '{{WRAPPER}} .dce-menu hr' => 'border-color: {{VALUE}};',
                        //'{{WRAPPER}} .dce-menu.horizontal li' => 'border-left-color: {{VALUE}};',
                        '{{WRAPPER}} .dce-menu .box' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'menu_border_size',
            [
                'label' => __( 'Border weight', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'toggle' => false,
                'label_block' => false,
                'default' => [
                    'size' => 1,
                    'unit' => 'px',
                ],
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-menu hr' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_border' => ['1','2']
                ],
            ]
        );
        $this->add_control(
            'menu_border_width',
            [
                'label' => __( 'Border width', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'toggle' => false,
                'label_block' => false,
                'size_units' => [ 'px','%'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-menu hr' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_border' => ['1','2']
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings =  $this->get_settings_for_display();
        if ( empty( $settings ) )
           return; 
        //
        // ------------------------------------------
        $dce_data = DCE_Helper::dce_dynamic_data();
        // ------------------------------------------
        $id_page = $dce_data['id'];
        $global_is = $dce_data['is'];
        $type_page = $dce_data['type'];

        //echo $type_page;

            /*$args = array(
            'post_type'         => 'page',
            'posts_per_page'    => -1,
            'order'             => 'ASC',
            'orderby'           => 'menu_order',
            'page_id'           =>  $settings['taxonomy_select']
        );
        $p_query = new \WP_Query( $args );

        $counter = 0;
            echo '<ul class="title">';
            if ( $p_query->have_posts() ) : 
                            echo '<li>';
                            echo $settings['taxonomy_select'];
                            echo '</li>';

            // End post check
        endif;*/
            /*$args = array(
            'sort_order' => 'desc',
            'sort_column' => 'menu_order',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        ); 
        $pages = get_pages($args); 
        $listPage = [];


        foreach ( $pages as $page ) {

            $terms = get_children( 'post_parent='.$page->ID );
            $parents = get_post_ancestors( $page->ID );
            //
            if( !$parents && count($terms) > 0 ) $listPage[$page->ID] = $page->post_title ;
          }*/
        $taxonomy_list = get_post_taxonomies($id_page);
       
        if($settings['taxonomy_dynamic'] == 'yes' ){  
            $terms = wp_get_post_terms( $id_page, $settings['taxonomy_select'], array(
                                    
                                    'hide_empty' => $settings['hide_empty'] ? true : false,
                                    'orderby' => 'parent',
                                    'order' => 'ASC',
                                ) );
            //var_dump($terms);
        }else{
            $terms = get_terms( array(
                    'taxonomy' => $settings['taxonomy_select'],
                    'hide_empty' => $settings['hide_empty'] ? true : false,
                    'orderby' => 'parent',
                    'order' => 'ASC',
                ) );
        }

        //print_r($terms);
        
        /**/
        $styleMenu = $settings['menu_style'];
        $clssStyleMenu = $styleMenu; 


        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                echo '<nav class="dce-menu '.$clssStyleMenu.'" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">';
                //echo $settings['taxonomy_select'];

                if( $settings['show_border'] == 2  ) echo '<div class="box">';
                
                if ( $settings['show_taxonomy'] != 0 ) {
                    
                    if( $settings['tax_text'] != "" ){
                            $taxtext = $settings['tax_text'];
                    }else{
                            $taxtext = $settings['taxonomy_select'];
                    }
                            echo '<h3 class="dce-parent-title"><a href="'.get_post_type_archive_link( $settings['taxonomy_select'] ).'">'.$taxtext.'</a></h3>';
                    if( $settings['show_border'] == 1  ) echo '<hr />';

                }
                if ( $settings['show_childlist'] ) {
                    echo '<ul class="first-level">';
                        
                        foreach ( $terms as $term ) {
                            $term_link = get_term_link( $term );
                            $myIDterm = get_queried_object_id();
                            //echo get_the_title($myIDterm);
                            $myTerms = wp_get_post_terms( $myIDterm, $settings['taxonomy_select'] );
                            
                            //var_dump($myTerms);
                            $linkActive = '';
                            if( /*is_single() && */ isset($myTerms) && count($myTerms) > 0 ){
                                
                                


                                $myT = $myTerms[0]->term_id;
                                
                                if( $myT == $term->term_id ){
                                    $linkActive = ' class="active"';
                                }else{
                                    $linkActive = '';
                                }
                            }

                            // --------------------- Image ACF
                            $image_acf = '';
                            if( $settings['image_acf_enable'] ){

                                $idFields = $settings['acf_field_image'];
                                $imageField = get_field( $idFields , 'term_'.$term->term_id);
                                $typeField = '';

                                //echo $typeField.': '.$imageField;
                                if( is_string($imageField) ){
                                    //echo 'url: '.$imageField;
                                    $typeField = 'image_url';
                                    $imageSrc = $imageField;
                                }else if( is_numeric($imageField) ){
                                    //echo 'id: '.$imageField;
                                    $typeField = 'image';
                                    $imageSrc = Group_Control_Image_Size::get_attachment_image_src( $imageField, 'size', $settings );
                                }else if( is_array( $imageField )){
                                    //echo 'array: '.$imageField;
                                    $typeField = 'image_array';
                                    $imageSrc = Group_Control_Image_Size::get_attachment_image_src( $imageField['ID'], 'size', $settings );
                                }
                                $image_acf = '<img src="'.$imageSrc.'" />';
                            }


                            echo '<li class="dce-term-'.$term->term_id.'"><a href="'.$term_link.'"'.$linkActive.'>'.$image_acf.$term->name.'</a></li>';
                        } // end each

                        echo '</ul>';
                        if( $settings['show_border'] == 2  ) echo '</div>';
                    }
                    echo '</nav>';
            }
    }
    protected function get_acf_field_image($group = false) {
        $acfList = [];
        $acfList[0] = 'Select the Field';
        $tipo = 'acf-field';
        $get_templates = get_posts(array('post_type' => $tipo, 'numberposts' => -1, 'post_status' => 'publish'));
        if (!empty($get_templates)) {

            foreach ($get_templates as $template) {
                $gruppoAppartenenza = get_the_title($template->post_parent);
                $arrayField = maybe_unserialize($template->post_content);

                if ($arrayField['type'] == 'image') {

                    if($group){
                        $acfList[$gruppoAppartenenza]['options'][$template->post_excerpt] = $template->post_title;
                        $acfList[$gruppoAppartenenza]['label'] = $gruppoAppartenenza;
                    }else{
                        $acfList[$template->post_excerpt] = $template->post_title . '(' . $arrayField['type'] . ')'; //.$template->post_content; //post_name,
                    }
                }
            }
        }
        return $acfList;
    }

}
