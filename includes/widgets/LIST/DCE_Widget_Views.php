<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\DCE_Tokens;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Parent Child Menu
 *
 * Elementor widget for Linkness Elements
 *
 * @since 1.0.0
 */
class DCE_Widget_Views extends DCE_Widget_Prototype {

    public $cpts;
    public $taxonomies;
    public $taxonomies_terms;
    public $post__in = [];
    public $the_query;

    public function get_name() {
        return 'dce-views';
    }

    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('Views', DCE_TEXTDOMAIN);
    }

    public function get_keywords() {
        return ['list', 'archive', 'search'];
    }

    public function get_description() {
        return __('Create a custom list from query results', DCE_TEXTDOMAIN);
    }

    public function get_docs() {
        return 'https://www.dynamic.ooo/';
    }

    public function get_icon() {
        return 'icon-dyn-views';
    }

    public function get_script_depends() {
        return ['infinitescroll'];
    }

    protected function _register_controls() {

        $cpts = DCE_Helper::get_post_types(false);
        $this->taxonomies = $taxonomies = DCE_Helper::get_taxonomies();
        $taxonomies_terms = array();
        foreach ($taxonomies as $tkey => $atax) {
            $taxonomies_terms[$tkey] = DCE_Helper::get_taxonomy_terms($tkey);
        }
        $this->taxonomies_terms = $taxonomies_terms;
        $post_fields = DCE_Helper::array_to_groups(DCE_Helper::get_post_fields(true, true));
//var_dump($post_fields); die();
        $post_fields_taxonomies = $post_fields;
        $post_fields_taxonomies['TAXONOMIES']['label'] = __('TAXONOMIES');
        $post_fields_taxonomies['TAXONOMIES']['options'] = $taxonomies;

        $post_status = get_post_stati(); //DCE_Helper::get_post_statuses_all(); // get_post_statuses();
        $templates = DCE_Helper::get_all_template();
        $sql_operators = DCE_Helper::get_sql_operators();

//* SELECT *//
        $this->start_controls_section(
                'section_select', [
            'label' => __('Select', DCE_TEXTDOMAIN),
                ]
        );

        /*
          $this->add_control(
          'dce_views_select_object', [
          'label' => __('Content object', DCE_TEXTDOMAIN),
          'type' => Controls_Manager::CHOOSE,
          'options' => [
          'fields' => [
          'title' => __('Post', DCE_TEXTDOMAIN),
          'icon' => 'fa fa-list',
          ],
          'text' => [
          'title' => __('User', DCE_TEXTDOMAIN),
          'icon' => 'fa fa-align-left',
          ],
          'template' => [
          'title' => __('Term', DCE_TEXTDOMAIN),
          'icon' => 'fa fa-th-large',
          ]
          ],
          'toggle' => false,
          'default' => 'post',
          ]
          );
         */

        $this->add_control(
                'dce_views_select_type', [
            'label' => __('Content type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'fields' => [
                    'title' => __('Fields', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-list',
                ],
                'text' => [
                    'title' => __('Text', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-align-left',
                ],
                'template' => [
                    'title' => __('Template', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-th-large',
                ]
            ],
            'toggle' => false,
            'default' => 'text',
                ]
        );
        $this->add_control(
                'dce_views_select_template', [
            'label' => __('Render Template', DCE_TEXTDOMAIN),
            'label_block' => true,
            'type' => Controls_Manager::SELECT2,
            'options' => $templates,
            'condition' => [
                'dce_views_select_type' => 'template',
            ],
                ]
        );
        $this->add_control(
                'dce_views_select_text', [
            'label' => __('Post preview html', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::WYSIWYG,
            'default' => '[post:thumb]<h4>[post:title]</h4><p>[post:excerpt]</p><a class="btn btn-primary" href="[post:permalink]">READ MORE</a>',
            'description' => __("Insert here some content showed if the widget is not visible", DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_select_type' => 'text',
            ],
                ]
        );
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control(
                'dce_views_select_field', [
            'label' => __('Field', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            //'options' => $post_fields,
            'groups' => $post_fields,
            'label_block' => true,
                ]
        );
        $repeater_fields->add_control(
                'dce_views_select_label', [
            'label' => __('Label', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_fields->add_control(
                'dce_views_select_label_inline', [
            'label' => __('Inline label', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'dce_views_select_label!' => '',
            ]
                ]
        );
        $repeater_fields->add_control(
                'dce_views_select_render', [
            'label' => __('Render', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'auto' => [
                    'title' => __('Auto', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-rocket',
                ],
                'rewrite' => [
                    'title' => __('Text', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-edit',
                ],
            ],
            'toggle' => false,
            'default' => 'auto',
                ]
        );
        $repeater_fields->add_control(
                'dce_views_select_rewrite', [
            'label' => __('Rewrite field', DCE_TEXTDOMAIN),
            'description' => __('Override the output of this field with custom text. You may include HTML and Tokens.', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => '[field]',
            'placeholder' => '[field]',
            'condition' => [
                'dce_views_select_render' => 'rewrite',
            ]
                ]
        );
        $repeater_fields->add_control(
                'dce_views_select_no_results', [
            'label' => __('No results text', DCE_TEXTDOMAIN),
            'description' => __('Provide text to display if this field contains an empty result. You may include HTML and Tokens.', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_fields->add_control(
                'custom_classes_heading', [
            'label' => __('Custom classes', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
                ]
        );
        $repeater_fields->add_control(
                'dce_views_select_class_wrapper', [
            'label' => __('Wrapper', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_fields->add_control(
                'dce_views_select_class_label', [
            'label' => __('Label', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_fields->add_control(
                'dce_views_select_class_value', [
            'label' => __('Value', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );


        $this->add_control(
                'dce_views_select_fields', [
            'label' => __('Show this fields', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_fields->get_controls(),
            'title_field' => '{{{ dce_views_select_field }}}',
            'default' => ['dce_views_select_field' => 'post_title'],
            'condition' => [
                'dce_views_select_type' => 'fields',
            ],
                ]
        );

        $this->add_control(
                'dce_views_h_format',
                [
                    'label' => __('Format', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_control(
                'dce_views_style_format', [
            'label' => __('Render as ', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => array('grid' => 'Grid', 'table' => 'Table', 'list' => 'List'),
            'default' => 'grid',
                ]
        );
        $this->add_control(
                'dce_views_style_list', [
            'label' => __('List type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'ul' => [
                    'title' => __('Unordered list', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-list-ul',
                ],
                'ol' => [
                    'title' => __('Ordered list', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-list-ol',
                ]
            ],
            'toggle' => false,
            'default' => 'ul',
            'condition' => [
                'dce_views_style_format' => 'list',
            ],
                ]
        );
        $this->add_control(
                'dce_views_select_class_heading', [
            'label' => __('Custom classes', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'dce_views_style_grid_class', [
            'label' => __('Add default classes', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'description' => __("Add default classes to row and cols to create a flex grid.", DCE_TEXTDOMAIN),
            'default' => 1,
            'condition' => [
                'dce_views_style_format' => 'grid',
            ],
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_col', [
            'label' => __('Columns', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 3,
            'min' => 1,
            'description' => __("Set 1 to show one result per line", DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_style_format' => 'grid',
                'dce_views_style_grid_class!' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_style_wrapper_class', [
            'label' => __('Wrapper', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'row',
                ]
        );
        $this->add_control(
                'dce_views_style_element_class', [
            'label' => __('Single Element', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'col col-md-4 col-sm-2',
                ]
        );

        $this->end_controls_section();

//* FROM *//
        $this->start_controls_section(
                'section_from', [
            'label' => __('From', DCE_TEXTDOMAIN),
                ]
        );

        $this->add_control(
                'dce_views_from_dynamic', [
            'label' => __('Dynamic', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Automatic fetch results from global WP_Query, change by current page', DCE_TEXTDOMAIN)
                ]
        );

        unset($cpts['elementor_library']);
        $this->add_control(
                'dce_views_cpt', [
            'label' => __('Post Type', DCE_TEXTDOMAIN),
            'label_block' => true,
            'type' => Controls_Manager::SELECT2,
            'options' => $cpts + array('nav_menu_item' => __('Navigation menu item', DCE_TEXTDOMAIN), 'any' => __('Any', DCE_TEXTDOMAIN)),
            //'description' => __('Select if post is one of this Type.', DCE_TEXTDOMAIN),
            'default' => ['post'],
            'multiple' => true,
            'condition' => [
                'dce_views_from_dynamic' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_attachment_mime_type', [
            'label' => __('Mime Type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'application/pdf,image/jpeg,image/png',
            'condition' => [
                'dce_views_cpt' => 'attachment',
                'dce_views_from_dynamic' => '',
            ],
                ]
        );

        $this->add_control(
                'dce_views_status', [
            'label' => __('Status', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT2,
            'options' => $post_status + array('any' => __('Any', DCE_TEXTDOMAIN)),
            'multiple' => true,
            'default' => ['publish'],
            'separator' => 'after',
            'condition' => [
                'dce_views_from_dynamic' => '',
            ],
                ]
        );
        $this->add_control(
                'taxonomy_heading', [
            'label' => __('Taxonomy', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'condition' => [
                'dce_views_from_dynamic' => '',
            ],
                ]
        );
        /* $this->add_control(
          'dce_views_tax', [
          'label' => __('Taxonomy', DCE_TEXTDOMAIN),
          'type' => Controls_Manager::SELECT2,
          'options' => $taxonomies,
          'description' => __('Visible if current post is related with this Taxonomy.', DCE_TEXTDOMAIN),
          'multiple' => false,
          ]
          ); */
        unset($taxonomies['elementor_library_type']);
        if (!empty($taxonomies)) {
            $wp_taxonomies_type = array();
            $wp_taxonomies = get_taxonomies(null, 'objects');
            foreach ($wp_taxonomies as $taxonomy) {
                $wp_taxonomies_type[$taxonomy->name] = $taxonomy->object_type;
                //echo '<p>' . var_export($taxonomy, true) . '</p>';
            }
        }
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey && !empty($taxonomies_terms[$tkey]) && count($taxonomies_terms[$tkey]) > 1) {
                //var_dump($wp_taxonomies_type[$tkey]);
                $this->add_control(
                        'dce_views_term_' . $tkey, [
                    'label' => $atax, //__('Terms', DCE_TEXTDOMAIN),
                    'label_block' => true,
                    'type' => Controls_Manager::SELECT2,
                    //'groups' => \DynamicContentForElementor\DCE_Helper::get_taxonomies_terms(),
                    'options' => $taxonomies_terms[$tkey],
                    'multiple' => true,
                    /* 'condition' => [
                      'dce_views_tax' => $tkey,
                      ], */
                    'condition' => [
                        'dce_views_from_dynamic' => '',
                    //'dce_views_cpt' => $wp_taxonomies_type[$tkey],
                    ],
                        ]
                );
            }
        }
        $this->add_control(
                'dce_views_tax_relation', [
            'label' => __('Tax Relation', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'AND' => [
                    'title' => __('AND', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-circle',
                ],
                'OR' => [
                    'title' => __('OR', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-circle-o',
                ]
            ],
            'toggle' => false,
            'default' => 'OR',
            'condition' => [
                'dce_views_from_dynamic' => '',
            ],
                ]
        );

        $this->end_controls_section();


//* WHERE - Conditions *//
        $this->start_controls_section(
                'section_where', [
            'label' => __('Where - Filter criteria', DCE_TEXTDOMAIN),
                ]
        );
        $repeater_where = new \Elementor\Repeater();
        $repeater_where->add_control(
                'dce_views_where_field', [
            'label' => __('Field', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            //'options' => $post_fields,
            'groups' => $post_fields,
            'label_block' => true,
                ]
        );
        $repeater_where->add_control(
                'dce_views_where_operator', [
            'label' => __('Operator', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => $sql_operators,
            'default' => '=',
                ]
        );
        $repeater_where->add_control(
                'dce_views_where_value', [
            'label' => __('Value', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_where->add_control(
                'dce_views_where_rule', [
            'label' => __('Combination', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'AND' => [
                    'title' => __('AND', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-circle',
                ],
                'OR' => [
                    'title' => __('OR', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-circle-o',
                ]
            ],
            'toggle' => false,
            'default' => 'OR',
                ]
        );
        $this->add_control(
                'dce_views_where', [
            'label' => __('Filter by this conditions', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_where->get_controls(),
            'prevent_empty' => false,
            'title_field' => '{{{ dce_views_where_field }}}',
                ]
        );
        $this->end_controls_section();


//* WHERE - Exposed form *//
        $this->start_controls_section(
                'section_form', [
            'label' => __('Where - Exposed form', DCE_TEXTDOMAIN),
                ]
        );

        $repeater_form = new \Elementor\Repeater();
        $repeater_form->add_control(
                'dce_views_where_form_field', [
            'label' => __('Filter', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'label_block' => true,
            //'options' => $post_fields
            'groups' => $post_fields_taxonomies,
            'description' => __('Select a field or a taxonomy', DCE_TEXTDOMAIN),
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_label', [
            'label' => __('Label', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_type', [
            'label' => __('Type', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => array('text' => 'Text', /* 'textarea' => 'TextArea', */ 'select' => 'Select', 'radio' => 'Radio', 'checkbox' => 'Checkbox', 'auto' => 'AUTO'),
            'default' => 'auto',
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_operator', [
            'label' => __('Operator', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => $sql_operators,
            'default' => 'LIKE'
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_required', [
            'label' => __('Required', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_required_label', [
            'label' => __('Post Field Status', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'none' => [
                    'title' => __('None', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-close',
                ],
                'asterisk' => [
                    'title' => __('*', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-asterisk',
                ],
                'text' => [
                    'title' => __('Text', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-i-cursor',
                ]
            ],
            'default' => 'asterisk',
            'toggle' => false,
            'condition' => [
                'dce_views_where_form_required!' => '',
            ],
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_required_label_text', [
            'label' => __('Required marker Text', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => __('required', DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_where_form_required!' => '',
                'dce_views_where_form_required_label' => 'text',
            ]
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_required_empty_label', [
            'label' => __('Empty option label', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'default' => __('Select a value', DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_where_form_required' => '',
            ]
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_value', [
            'label' => __('Value', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXTAREA,
            'description' => __('If select/ceckbox/radio use one line for option, use | to separate value and name (ex: "my_value|Name of the option").', DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_where_form_type!' => 'text',
                'dce_views_where_form_type!' => 'auto',
            ]
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_preselect', [
            'label' => __('Preselect', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'description' => __('Insert default value.', DCE_TEXTDOMAIN),
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_placeholder', [
            'label' => __('Placeholder', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_hint', [
            'label' => __('Hint', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'description' => __('A short description of the field', DCE_TEXTDOMAIN),
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_rule', [
            'label' => __('Combination', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'AND' => [
                    'title' => __('AND', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-circle',
                ],
                'OR' => [
                    'title' => __('OR', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-circle-o',
                ]
            ],
            'toggle' => false,
            'default' => 'AND',
                ]
        );
        $repeater_form->add_responsive_control(
                'dce_views_where_form_width',
                [
                    'label' => __('Column Width', 'elementor-pro'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => __('Default', 'elementor-pro'),
                        '100' => '100%',
                        '80' => '80%',
                        '75' => '75%',
                        '66' => '66%',
                        '60' => '60%',
                        '50' => '50%',
                        '40' => '40%',
                        '33' => '33%',
                        '25' => '25%',
                        '20' => '20%',
                    ],
                    'default' => '100',
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_custom_classes_heading', [
            'label' => __('Custom classes', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_class_wrapper', [
            'label' => __('Wrapper', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_class_label', [
            'label' => __('Label', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_form->add_control(
                'dce_views_where_form_class_input', [
            'label' => __('Input', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $this->add_control(
                'dce_views_where_form', [
            'label' => __('Exposed Fields', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_form->get_controls(),
            'prevent_empty' => false,
            'title_field' => '{{{ dce_views_where_form_field }}}',
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'dce_views_style_form_text', [
            'label' => __('Form Title', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'condition' => [
                'dce_views_where_form!' => ['', []],
            ],
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'dce_views_style_form_text_size',
                [
                    'label' => __('Title HTML Tag', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                        'div' => 'div',
                        'span' => 'span',
                        'p' => 'p',
                    ],
                    'default' => 'h4',
                    'condition' => [
                        'dce_views_where_form!' => ['', []],
                        'dce_views_style_form_text!' => '',
                    ]
                ]
        );

        $this->add_control(
                'dce_views_where_form_result', [
            'label' => __('Show result', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'description' => __("Show results from first time, also before user interact with form, using preselected value.", DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_where_form!' => ['', []],
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_ajax', [
            'label' => __('Use Ajax', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'dce_views_where_form!' => ['', []],
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_ajax_transition', [
            'label' => __('Transition', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => array('' => 'Toggle', 'fade' => 'Fade', 'slide' => 'Slide'),
            'condition' => [
                'dce_views_where_form!' => ['', []],
                'dce_views_where_form_ajax!' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_ajax_onchange', [
            'label' => __('Submit on change', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'dce_views_where_form!' => ['', []],
                'dce_views_where_form_ajax!' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_ajax_nobutton', [
            'label' => __('Remove submit button', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'selectors' => [
                '{{WRAPPER}} .dce-view-exposed-form input.dce-button' => 'display: none;',
            ],
            'condition' => [
                'dce_views_where_form!' => ['', []],
                'dce_views_where_form_ajax!' => '',
                'dce_views_where_form_ajax_onchange!' => '',
            ],
                ]
        );

        $this->add_control(
                'dce_views_where_form_action_heading', [
            'label' => __('Form Action', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'dce_views_where_form!' => ['', []],
                'dce_views_where_form_ajax_nobutton' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_reset', [
            'label' => __('Show Reset', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'dce_views_where_form!' => ['', []],
                'dce_views_where_form_ajax_nobutton' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_style_form_submit_text', [
            'label' => __('Submit Text', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __('Search', DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_where_form!' => ['', []],
                'dce_views_where_form_ajax_nobutton' => '',
            ],
                ]
        );
        $this->add_responsive_control(
                'dce_views_where_form_action_width',
                [
                    'label' => __('Column Width', 'elementor-pro'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => __('Default', 'elementor-pro'),
                        '100' => '100%',
                        '80' => '80%',
                        '75' => '75%',
                        '66' => '66%',
                        '60' => '60%',
                        '50' => '50%',
                        '40' => '40%',
                        '33' => '33%',
                        '25' => '25%',
                        '20' => '20%',
                    ],
                    'default' => '100',
                    'condition' => [
                        'dce_views_where_form!' => ['', []],
                        'dce_views_where_form_ajax_nobutton' => '',
                    ],
                ]
        );

        $this->add_control(
                'dce_views_where_form_class_heading', [
            'label' => __('Custom classes', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'dce_views_where_form!' => ['', []],
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_class', [
            'label' => __('Form', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => [
                'dce_views_where_form!' => ['', []],
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_class_wrapper', [
            'label' => __('Wrapper', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'dce-basic-form',
            'condition' => [
                'dce_views_where_form!' => ['', []],
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_class_filter', [
            'label' => __('Filters', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'form-group',
            'condition' => [
                'dce_views_where_form!' => ['', []],
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_class_buttons', [
            'label' => __('Buttons wrapper', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'form-action',
            'condition' => [
                'dce_views_where_form!' => ['', []],
                'dce_views_where_form_ajax_nobutton' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_where_form_class_button', [
            'label' => __('Button', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'btn-primary',
            'condition' => [
                'dce_views_where_form!' => ['', []],
                'dce_views_where_form_ajax_nobutton' => '',
            ],
                ]
        );
        $this->end_controls_section();


//* ORDER BY *//
        $repeater_order = new \Elementor\Repeater();
        $repeater_order->add_control(
                'dce_views_order_field', [
            'label' => __('Field', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            //'options' => $post_fields
            'groups' => $post_fields,
            'label_block' => true,
                ]
        );
        $repeater_order->add_control(
                'dce_views_order_field_sort', [
            'label' => __('Sorting', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'ASC' => [
                    'title' => __('ASC', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-sort-up',
                ],
                'DESC' => [
                    'title' => __('DESC', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-sort-down',
                ]
            ],
            'inline' => true,
            'toggle' => false,
            'default' => 'ASC',
                ]
        );
        $repeater_order->add_control(
                'dce_views_order_field_sort_exposed', [
            'label' => __('Exposed', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Expose this sort to visitors, to allow them to change it', DCE_TEXTDOMAIN),
                ]
        );

        $this->start_controls_section(
                'section_order', [
            'label' => __('Order By - Sort criteria', DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'dce_views_order_random', [
            'label' => __('Random', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Randomize result sort order.', DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'dce_views_order_by', [
            'label' => __('Sorting by fields', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_order->get_controls(),
            'prevent_empty' => false,
            'title_field' => '{{{ dce_views_order_field }}}',
            'condition' => [
                'dce_views_order_random' => '',
            ],
                ]
        );
        $this->add_control(
                'exposed_sort_heading', [
            'label' => __('Exposed Sort', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HEADING,
            'condition' => [
                'dce_views_order_by!' => [],
            ],
                ]
        );
        $this->add_control(
                'dce_views_order_class', [
            'label' => __('Custom class', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'dce-basic-sort',
            'condition' => [
                'dce_views_order_by!' => [],
            ],
                ]
        );
        $this->add_control(
                'dce_views_order_label', [
            'label' => __('Label', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __('Sort by', DCE_TEXTDOMAIN . '_texts'),
            'condition' => [
                'dce_views_order_by!' => [],
            ],
                ]
        );
        $this->end_controls_section();


//* LIMIT *//
        $this->start_controls_section(
                'section_limit', [
            'label' => __('Limit - Pager', DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'dce_views_limit_offset', [
            'label' => __('Start from - Offset', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 0,
            'min' => 0,
            'description' => __("Number of items to skip. For example, set this to 3 and the first 3 items will not be displayed. Set 0 to show from the first result.", DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'dce_views_limit_to', [
            'label' => __('Max allowed result displayed', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 0,
            'min' => 0,
            'description' => __("Set 0 if you do not want to limit results", DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'dce_views_pagination', [
            'label' => __('Pagination', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Enable results pagination', DCE_TEXTDOMAIN),
                ]
        );



        $this->add_control(
                'dce_views_post_per_page', [
            'label' => __('Post per page', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 10,
            'min' => 0,
            'description' => __("Set 0 for default site global limit.", DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_pagination!' => '',
            ],
                ]
        );

        $this->add_control(
                'dce_views_pagination_type',
                [
                    'label' => __('Pagination', 'elementor-pro'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'numbers_and_prev_next',
                    'options' => [
                        'infinite_scroll' => __('Infinite Scroll', DCE_TEXTDOMAIN),
                        'numbers' => __('Numbers', 'elementor-pro'),
                        'prev_next' => __('Previous/Next', 'elementor-pro'),
                        'numbers_and_prev_next' => __('Numbers', 'elementor-pro') . ' + ' . __('Previous/Next', 'elementor-pro'),
                    ],
                    'condition' => [
                        'dce_views_pagination!' => '',
                    ],
                ]
        );

        $this->add_control(
                'dce_views_pagination_page_limit',
                [
                    'label' => __('Page Limit', 'elementor-pro'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 0,
                    'min' => 0,
                    'condition' => [
                        'dce_views_pagination!' => '',
                        'dce_views_pagination_type' => [
                            'numbers',
                            'numbers_and_prev_next',
                        ],
                    ],
                ]
        );

        $this->add_control(
                'dce_views_pagination_numbers_shorten',
                [
                    'label' => __('Shorten', 'elementor-pro'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'condition' => [
                        'dce_views_pagination!' => '',
                        'dce_views_pagination_type' => [
                            'numbers',
                            'numbers_and_prev_next',
                        ],
                    ],
                ]
        );

        $this->add_control(
                'dce_views_pagination_prev_label',
                [
                    'label' => __('Previous Label', 'elementor-pro'),
                    'default' => __('&laquo; Previous', 'elementor-pro'),
                    'condition' => [
                        'dce_views_pagination!' => '',
                        'dce_views_pagination_type' => [
                            'prev_next',
                            'numbers_and_prev_next',
                        ],
                    ],
                ]
        );

        $this->add_control(
                'dce_views_pagination_next_label',
                [
                    'label' => __('Next Label', 'elementor-pro'),
                    'default' => __('Next &raquo;', 'elementor-pro'),
                    'condition' => [
                        'dce_views_pagination!' => '',
                        'dce_views_pagination_type' => [
                            'prev_next',
                            'numbers_and_prev_next',
                        ],
                    ],
                ]
        );


        $this->add_control(
                'dce_views_limit_scroll_last', [
            'label' => __('Label Last', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'dce_views_pagination!' => '',
                'dce_views_pagination_type' => [
                    'infinite_scroll',
                ],
            ],
                ]
        );
        $this->end_controls_section();


//* FALLBACK for NO RESULTS *//
        $this->start_controls_section(
                'section_fallback', [
            'label' => __('No results behavior', DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'dce_views_fallback', [
            'label' => __('Enable a Fallback Content', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SWITCHER,
            'description' => __("If you want to show something when no element were found.", DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
                'dce_views_fallback_type', [
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
            'toggle' => false,
            'default' => 'text',
            'condition' => [
                'dce_views_fallback!' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_fallback_template', [
            'label' => __('Render Template', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT2,
            'options' => $templates,
            'description' => 'Use a Elementor Template as content, useful for complex structure.',
            'condition' => [
                'dce_views_fallback!' => '',
                'dce_views_fallback_type' => 'template',
            ],
                ]
        );
        $this->add_control(
                'dce_views_fallback_text', [
            'label' => __('Text Fallback', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::WYSIWYG,
            'default' => "This view has no results.",
            'description' => __("Write here some content, you can use HTML and TOKENS.", DCE_TEXTDOMAIN),
            'condition' => [
                'dce_views_fallback!' => '',
                'dce_views_fallback_type' => 'text',
            ],
                ]
        );
        $this->end_controls_section();


//* STYLE *//
        /*
          $this->start_controls_section(
          'section_style_table', [
          'label' => __('Table', DCE_TEXTDOMAIN),
          'tab' => Controls_Manager::TAB_STYLE,
          'condition' => [
          'dce_views_style_format' => 'table',
          ],
          ]
          );
          $this->add_control(
          'dce_views_style_data_table', [
          'label' => __('Use DataTable', DCE_TEXTDOMAIN),
          'type' => \Elementor\Controls_Manager::SWITCHER,
          'description' => __("Add default classes to row and cols to create a flex grid.", DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section();
         */



// EXPOSED FORM
        $this->start_controls_section(
                'section_style_form', [
            'label' => __('Exposed Form', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'dce_views_where_form!' => ['', []],
            ]
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_form_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-form-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_form_margin', [
            'label' => __('Margin', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-form-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_form_align',
                [
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
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form' => 'text-align: {{VALUE}};',
                    ],
                ]
        );
// Border ----------------
        $this->add_control(
                'heading_views_border',
                [
                    'label' => __('Border', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'dce_views_style_form_border',
            'label' => __('Border', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-form-wrapper',
                ]
        );
        $this->add_control(
                'dce_views_style_form_border_radius', [
            'label' => __('Border Radius', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-form-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
// Background ----------------
        $this->add_control(
                'heading_views_background',
                [
                    'label' => __('Background', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'background_search',
                    'types' => ['classic', 'gradient'],
                    'selector' => '{{WRAPPER}} .dce-view-exposed-form',
                ]
        );

// Title ----------------
        $this->add_control(
                'heading_views_title',
                [
                    'label' => __('Title', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'dce_views_style_form_text!' => '',
                    ],
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_form_title_align',
                [
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
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title' => 'text-align: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'dce_views_style_form_title_color',
                [
                    'label' => __('Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title' => 'color: {{VALUE}};',
                    ],
                    'condition' => [
                        'dce_views_style_form_text!' => '',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'dce_views_style_form_title_typography',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title',
            'condition' => [
                'dce_views_style_form_text!' => '',
            ],
                ]
        );
        $this->add_control(
                'dce_views_style_form_title_space',
                [
                    'label' => __('Space', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 10,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -50,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'dce_views_style_form_text!' => '',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'dce_views_style_form_title_text_shadow',
                    'selector' => '{{WRAPPER}} .dce-view-exposed-form .dce-views-form-title',
                    'condition' => [
                        'dce_views_style_form_text!' => '',
                    ],
                ]
        );
       
// Filters ----------------
        $this->add_control(
                'heading_views_filters',
                [
                    'label' => __('Filters', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_form_filters_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-form-wrapper .dce-view-field-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_form_filters_margin', [
            'label' => __('Margin', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-form-wrapper .dce-view-field-filter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_form_filters_align',
                [
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
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-field-filter' => 'text-align: {{VALUE}};',
                    ],
                ]
        );
// Field ----------------
        $this->add_control(
                'heading_views_field',
                [
                    'label' => __('Input Text & Select', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_control(
                'dce_views_style_form_field_txcolor',
                [
                    'label' => __('Text Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text]' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > select' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'dce_views_style_form_field_bgcolor',
                [
                    'label' => __('Background Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text]' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > select' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'dce_views_style_form_field_typography',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text], {{WRAPPER}} .dce-view-exposed-form .dce-view-input > select',
                ]
        );
        $this->add_control(
                'dce_views_style_form_field_border_radius',
                [
                    'label' => __('Border Radius', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 1,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                        ],
                    ],
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text]' => 'border-radius: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > select' => 'border-radius: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_form_field_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > input[type=text]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .dce-view-exposed-form .dce-view-input > select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'dce_views_style_form_field_border',
            'label' => __('Border', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-form .button',
                ]
        );
        $this->add_control(
                'dce_views_style_form_field_space',
                [
                    'label' => __('Space', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 10,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -50,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-field-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'dce_views_style_form_field_box_shadow',
                    'exclude' => [
                        'box_shadow_position',
                    ],
                    'selector' => '{{WRAPPER}} .dce-view-exposed-form .dce-view-input',
                ]
        );
// Label ----------------
        $this->add_control(
                'heading_label_field',
                [
                    'label' => __('Label', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_control(
                'dce_views_style_form_label_color',
                [
                    'label' => __('Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form label.dce-view-input-label' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'dce_views_style_form_label_typography',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-form label.dce-view-input-label',
                ]
        );
        $this->add_control(
                'dca_views_style_form_label_display_mode', [
            'label' => __('Display mode', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'inline' => [
                    'title' => __('Inline', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-arrows-h',
                ],
                'block' => [
                    'title' => __('Block', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-stop',
                ],
                'none' => [
                    'title' => __('None', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-eye-slash',
                ],
            ],
            'default' => 'inline',
            'selectors' => ['{{WRAPPER}} .dce-view-exposed-form label.dce-view-input-label' => 'display: {{VALUE}};'],
                ]
        );
// Buttons ----------------
        $this->add_control(
                'heading_views_buttons',
                [
                    'label' => __('Buttons', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_control(
                'buttons_align',
                [
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
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-exposed-form-buttons' => 'text-align: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'buttons_typography',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-form .button',
                ]
        );
        $this->add_control(
                'buttons_border_radius',
                [
                    'label' => __('Border Radius', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 1,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                        ],
                    ],
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .button' => 'border-radius: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_responsive_control(
                'buttons_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .dce-view-exposed-form .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_responsive_control(
                'buttons_margin', [
            'label' => __('Margin', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .dce-view-exposed-form .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'buttons_border',
            'label' => __('Border', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-form .button',
                ]
        );

        $this->add_control(
                'buttons_v_space',
                [
                    'label' => __('Verical Space', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -100,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .button' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_control(
                'buttons_h_space',
                [
                    'label' => __('Horizontal Space', DCE_TEXTDOMAIN),
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
                        '{{WRAPPER}} .dce-view-exposed-form .button' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );
// Button Reset ----------------
        $this->add_control(
                'heading_views_buttonReset',
                [
                    'label' => __('Button Reset', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_control(
                'buttonreset_txcolor',
                [
                    'label' => __('Text Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.reset' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonreset_bgcolor',
                [
                    'label' => __('Background Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.reset' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonreset_border_color',
                [
                    'label' => __('Border color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.reset' => 'border-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonreset_txcolor_hover',
                [
                    'label' => __('Hover Text Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.reset:hover' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonreset_bgcolor_hover',
                [
                    'label' => __('Hover Background Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.reset:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonreset_border_color_hover',
                [
                    'label' => __('Hover Border color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.reset:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
        );
// Button Find ----------------
        $this->add_control(
                'heading_views_buttonFind',
                [
                    'label' => __('Button Find', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );

        $this->add_control(
                'buttonfind_txcolor',
                [
                    'label' => __('Text Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.find' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonfind_bgcolor',
                [
                    'label' => __('Background Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.find' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonfind_border_color',
                [
                    'label' => __('Border color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.find' => 'border-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonfind_txcolor_hover',
                [
                    'label' => __('Hover Text Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.find:hover' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonfind_bgcolor_hover',
                [
                    'label' => __('Hover Background Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.find:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'buttonfind_border_color_hover',
                [
                    'label' => __('Hover Border color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form input.reset:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
        );
        
        
        $this->add_control(
                'form_col_inner_width',
                [
                    'label' => __('Col inner width', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HIDDEN,
                    'default' => '100',
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-form .dce-view-form-col-inner' => 'width: {{VALUE}}%;',
                    ],
                ]
        );
         
        $this->end_controls_section();



// EXPOSED SORT
        $this->start_controls_section(
                'section_style_sort', [
            'label' => __('Exposed Sort', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_control(
                'dce_views_style_sort_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-exposed-sort' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_control(
                'dce_views_style_sort_margin', [
            'label' => __('Margin', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .dce-view-exposed-sort' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_control(
                'dce_views_style_sort_align',
                [
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
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-sort' => 'text-align: {{VALUE}};',
                    ],
                ]
        );
// Border ----------------
        $this->add_control(
                'heading_views_sort_border',
                [
                    'label' => __('Border', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'dce_views_style_sort_border',
            'label' => __('Border', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-sort',
                ]
        );

        $this->add_control(
                'dce_views_style_sort_border_radius', [
            'label' => __('Border Radius', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-exposed-sort' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
// Background ----------------
        $this->add_control(
                'dce_views_h_style_sort_bg',
                [
                    'label' => __('Background', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'dce_views_style_sort_bg',
                    'selector' => '{{WRAPPER}} .dce-view-exposed-sort',
                ]
        );
// Label ----------------
        $this->add_control(
                'heading_sort_label_field',
                [
                    'label' => __('Label', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_control(
                'dce_views_style_sort_label_color',
                [
                    'label' => __('Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-sort label' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'dce_views_style_sort_label_typography',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-sort label',
                ]
        );
        $this->add_control(
                'dce_views_style_sort_label_display', [
            'label' => __('Display', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'inline' => [
                    'title' => __('Inline', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-arrows-h',
                ],
                'block' => [
                    'title' => __('Block', DCE_TEXTDOMAIN),
                    'icon' => 'fa fa-stop',
                ]
            ],
            'default' => 'inline',
            'selectors' => ['{{WRAPPER}} .dce-view-exposed-sort label' => 'display: {{VALUE}};'],
                ]
        );
        $this->add_control(
                'heading_views_sort_field',
                [
                    'label' => __('Select', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->add_responsive_control(
                'dce_views_style_sort_field_padding', [
            'label' => __('Padding', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        $this->add_control(
                'dce_views_style_sort_field_txcolor',
                [
                    'label' => __('Text Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'dce_views_style_sort_field_bgcolor',
                [
                    'label' => __('Background Color', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'dce_views_style_sort_field_typography',
            'label' => __('Typography', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort',
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'dce_views_style_sort_field_border',
            'label' => __('Border', DCE_TEXTDOMAIN),
            'selector' => '{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort',
                ]
        );
        $this->add_control(
                'dce_views_style_sort_field_border_radius',
                [
                    'label' => __('Border Radius', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 1,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                        ],
                    ],
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .dce-view-exposed-sort .dce-input-sort' => 'border-radius: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );
        $this->end_controls_section();


        // PAGINATION
        $this->start_controls_section(
                'section_style_pagination', [
            'label' => __('Pagination', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'dce_views_pagination!' => '',
            ]
                ]
        );
        $this->add_control(
                'pagination_align',
                [
                    'label' => __('Alignment', 'elementor-pro'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', 'elementor-pro'),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', 'elementor-pro'),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', 'elementor-pro'),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => 'center',
                    'selectors' => [
                        '{{WRAPPER}} .elementor-pagination' => 'text-align: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'pagination_typography',
                    'selector' => '{{WRAPPER}} .elementor-pagination',
                    'scheme' => Scheme_Typography::TYPOGRAPHY_2,
                ]
        );
        $this->add_control(
                'pagination_color_heading',
                [
                    'label' => __('Colors', 'elementor-pro'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $this->start_controls_tabs('pagination_colors');
        $this->start_controls_tab(
                'pagination_color_normal',
                [
                    'label' => __('Normal', 'elementor-pro'),
                ]
        );
        $this->add_control(
                'pagination_color',
                [
                    'label' => __('Color', 'elementor-pro'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-pagination .page-numbers:not(.dots)' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'pagination_bgcolor',
                [
                    'label' => __('Background Color', 'elementor-pro'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-pagination .page-numbers:not(.dots)' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
                'pagination_color_hover',
                [
                    'label' => __('Hover', 'elementor-pro'),
                ]
        );
        $this->add_control(
                'pagination_hover_color',
                [
                    'label' => __('Color', 'elementor-pro'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-pagination a.page-numbers:hover' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'pagination_hover_bgcolor',
                [
                    'label' => __('Background Color', 'elementor-pro'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-pagination a.page-numbers:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
                'pagination_color_active',
                [
                    'label' => __('Active', 'elementor-pro'),
                ]
        );
        $this->add_control(
                'pagination_active_color',
                [
                    'label' => __('Color', 'elementor-pro'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-pagination .page-numbers.current' => 'color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'pagination_active_bgcolor',
                [
                    'label' => __('Background Color', 'elementor-pro'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-pagination .page-numbers.current' => 'background-color: {{VALUE}};',
                    ],
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control(
                'pagination_spacing',
                [
                    'label' => __('Space Between', 'elementor-pro'),
                    'type' => Controls_Manager::SLIDER,
                    'separator' => 'before',
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
                        'body:not(.rtl) {{WRAPPER}} .elementor-pagination .page-numbers:not(:first-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
                        'body:not(.rtl) {{WRAPPER}} .elementor-pagination .page-numbers:not(:last-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
                        'body.rtl {{WRAPPER}} .elementor-pagination .page-numbers:not(:first-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
                        'body.rtl {{WRAPPER}} .elementor-pagination .page-numbers:not(:last-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
                    ],
                ]
        );
        $this->end_controls_section();


        // ADVANCED
        $this->start_controls_section(
                'section_style_advanced', [
            'label' => __('Special effects', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_control(
                'dce_views_style_entrance_animation', [
            'label' => __('Entrance Animation', DCE_TEXTDOMAIN),
            'type' => \Elementor\Controls_Manager::ANIMATION,
            'prefix_class' => 'animated ',
                ]
        );
        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings_for_display(null, true);
        //var_dump($settings['dce_views_where_form']);
//remove_filter('the_content', 'wpautop');

        $this->taxonomies = $taxonomies = DCE_Helper::get_taxonomies();
        $taxonomies_terms = array();
        foreach ($taxonomies as $tkey => $atax) {
            $taxonomies_terms[$tkey] = DCE_Helper::get_taxonomy_terms($tkey);
        }
        $this->taxonomies_terms = $taxonomies_terms;

        $this->_exposed_form();

        $this->_exposed_sort();

        if ($settings['dce_views_where_form_result'] // forzo la visualizzazione alla prima volta
                || empty($settings['dce_views_where_form']) // non ho un form, quindi lo vedo per forza alla prima volta
                || isset($_GET['eid']) // ho eseguito un submit del form
        ) {
//echo 'loop';
            $this->_loop();
        } else {
            if ($settings['dce_views_where_form_ajax']) {
                echo '<div class="dce-view-results dce-view-results-ajax"></div>';
            }
        }

//add_filter('the_content', 'wpautop');
    }

    /*
      // fix orderby bug
      public function filter_query($the_query) {
      $settings = $this->get_settings_for_display(null, true);
      $args = $this->get_wp_query_args($settings);
      $the_query->query_vars = array_merge($the_query->query_vars, $args);
      //echo '<pre>';var_dump($the_query->query_vars);echo '</pre>';
      return $query;
      }
     */

    public function _loop($settings = null) {
        if (!$settings) {
            $settings = $this->get_settings_for_display(null, true); // not parsed because token need to be valued on loop
        }

        global $post, $wp_query;
        $original_post = $post;
        $original_query = $wp_query;

        $wrapper_class = 'dce-view-' . $settings['dce_views_style_format'] . ' ' . $settings['dce_views_style_wrapper_class'];
        $element_class = 'dce-view-' . $settings['dce_views_style_format'] . '-element ' . $settings['dce_views_style_entrance_animation'] . ' ' . $settings['dce_views_style_element_class'];
        $responsive_cols = ' data-col-md="' . $settings['dce_views_style_col'] . '" data-col-sm="' . $settings['dce_views_style_col_tablet'] . '" data-col-xs="' . $settings['dce_views_style_col_mobile'] . '"';

// https://codex.wordpress.org/Class_Reference/WP_Query
//$args = array();
        $args = $this->get_wp_query_args();
//$args["suppress_filters"] = true;  // No posts_orderby filters will be run
        //echo '<pre>'; var_dump($args); echo '</pre>';
// The Query
        //add_action('pre_get_posts', array($this, 'filter_query'));
        /* if ($this->the_query) {    
          $the_query = $this->the_query;
          } else { */
        $this->the_query = $the_query = new \WP_Query($args);
        //}
        //var_dump($this->the_query );
        //remove_action('pre_get_posts', array($this, 'filter_query'));
        //echo '<pre>'; echo $the_query->request; echo '</pre>';
        /* $query_request = get_option('dce-views-query-request');
          if ($query_request) {
          $the_query->request = $query_request;
          echo '<pre>';echo $query_request;echo '</pre>';
          } else {
          //echo $the_query->request;
          update_option('dce-views-query-request', $the_query->request);
          } */

        // Now wipe it out completely
        $wp_query = null;
        $wp_query = $the_query;

// The Loop
        if ($the_query->have_posts()) {

            echo '<div class="dce-view-results">';

            switch ($settings['dce_views_style_format']) {
                case 'table':
                    echo '<table class="' . $wrapper_class . (isset($settings['dce_views_style_data_table']) && $settings['dce_views_style_data_table'] ? ' datatable' : '') . '">';
                    if ($settings['dce_views_select_type'] == 'fields') {
                        echo '<thead><tr>';
                        foreach ($settings['dce_views_select_fields'] as $key => $afield) {
                            echo '<th class="dce-view-field-th ' . $afield['dce_views_select_class_label'] . '">' . ($afield['dce_views_select_label'] ? $afield['dce_views_select_label'] : $afield['dce_views_select_field']) . '</th>';
                        }
                        echo '</tr></thead>';
                    }
                    echo '<tbody>';
                    break;
                case 'list':
                    echo '<' . $settings['dce_views_style_list'] . ' class="' . $wrapper_class . '">';
                    break;
                case 'grid':
                default:
                    echo '<div class="dce-view-row ' . ($settings['dce_views_style_grid_class'] ? 'grid-page grid-col-md-' . $settings['dce_views_style_col'] . ' grid-col-sm-' . $settings['dce_views_style_col_tablet'] . ' grid-col-xs-' . $settings['dce_views_style_col_mobile'] : '') . $wrapper_class . '">';
            }


            $dce_views_limit_to = ($settings['dce_views_limit_to']) ? $settings['dce_views_limit_to'] : 0;
            if ($settings['dce_views_limit_offset'] && $settings['dce_views_limit_offset'] > 0) {
                $dce_views_limit_to += $settings['dce_views_limit_offset'];
            }

            $k = 0;
            while ($the_query->have_posts()) {

                if ($settings['dce_views_limit_to'] && $k >= $dce_views_limit_to) {
                    break;
                }
                if ($settings['dce_views_limit_offset'] > $k) { // && !$settings['dce_views_pagination']) {
                    $k++;
                    continue;
                }

                $the_query->the_post();
                $post = get_post();
                $element_class_post = ' dce-view-element dce-view-post-' . $post->ID;
                switch ($settings['dce_views_select_type']) {
                    case 'fields':
                        switch ($settings['dce_views_style_format']) {
                            case 'table':
                                echo '<tr class="' . $element_class . $element_class_post . '">';
                                foreach ($settings['dce_views_select_fields'] as $key => $afield) {
                                    echo '<td class="dce-view-field-' . $afield['dce_views_select_field'] . ' ' . $afield['dce_views_select_class_wrapper'] . '"><div class="dce-view-field-value ' . $afield['dce_views_select_class_value'] . '">' . $this->get_field_value($afield, $settings) . '</div></td>';
                                }
                                echo '</tr>';
                                break;
                            case 'list':
                                echo '<li class="' . $element_class . $element_class_post . '">';
                                foreach ($settings['dce_views_select_fields'] as $key => $afield) {
                                    echo '<div class="dce-view-field-' . $afield['dce_views_select_field'] . ' ' . $afield['dce_views_select_class_wrapper'] . '">';
                                    if ($afield['dce_views_select_label']) {
                                        if ($afield['dce_views_select_label_inline']) {
                                            echo '<label';
                                        } else {
                                            echo '<div';
                                        }
                                        echo ' class="dce-view-field-label">' . $afield['dce_views_select_label'];
                                        if ($afield['dce_views_select_label_inline']) {
                                            echo '</label>';
                                        } else {
                                            echo '</div>';
                                        }
                                    }
                                    if ($afield['dce_views_select_label'] && $afield['dce_views_select_label_inline']) {
                                        echo '<span';
                                    } else {
                                        echo '<div';
                                    }
                                    echo ' class="dce-view-field-value ' . $afield['dce_views_select_class_value'] . '">' . $this->get_field_value($afield, $settings) . '</div>';
                                    if ($afield['dce_views_select_label'] && $afield['dce_views_select_label_inline']) {
                                        echo '</span>';
                                    } else {
                                        echo '</div>';
                                    }
                                }
                                echo '</li>';
                                break;
                            case 'grid':
                            default:
                                echo '<div class="' . ($settings['dce_views_style_grid_class'] ? 'item-page ' : '') . 'dce-view-col ' . $element_class . $element_class_post . '"' . $responsive_cols . '>';
                                foreach ($settings['dce_views_select_fields'] as $key => $afield) {
                                    echo '<div class="dce-view-field-' . $afield['dce_views_select_field'] . ' ' . $afield['dce_views_select_class_wrapper'] . '">';
                                    if ($afield['dce_views_select_label']) {
                                        if ($afield['dce_views_select_label_inline']) {
                                            echo '<label';
                                        } else {
                                            echo '<div';
                                        }
                                        echo ' class="dce-view-field-label ' . $afield['dce_views_select_class_label'] . '">' . $afield['dce_views_select_label'];
                                        if ($afield['dce_views_select_label_inline']) {
                                            echo '</label>';
                                        } else {
                                            echo '</div>';
                                        }
                                    }
                                    if ($afield['dce_views_select_label'] && $afield['dce_views_select_label_inline']) {
                                        echo '<span';
                                    } else {
                                        echo '<div';
                                    }
                                    echo ' class="dce-view-field-value ' . $afield['dce_views_select_class_value'] . '">' . $this->get_field_value($afield, $settings) . '</div>';
                                    if ($afield['dce_views_select_label'] && $afield['dce_views_select_label_inline']) {
                                        echo '</span>';
                                    } else {
                                        echo '</div>';
                                    }
                                }
                                echo '</div>';
                        }
                        break;
                    case 'template':
                        global $global_ID;
                        if ($settings['dce_views_style_format'] == 'grid')
                            echo '<div class="item-page dce-view-col ' . $element_class . $element_class_post . '"' . $responsive_cols . '>';
                        echo do_shortcode('[dce-elementor-template id="' . $settings['dce_views_select_template'] . '"]');
                        if ($settings['dce_views_style_format'] == 'grid')
                            echo '</div>';
                        break;
                    case 'text':
                        if ($settings['dce_views_style_format'] == 'grid') {
                            echo '<div class="item-page dce-view-col ' . $element_class . $element_class_post . '"' . $responsive_cols . '>';
                        }
                        if ($settings['dce_views_style_format'] == 'list') {
                            echo '<li class="' . $element_class . $element_class_post . '">';
                        }
                        echo DCE_Tokens::do_tokens($settings['dce_views_select_text']);
                        if ($settings['dce_views_style_format'] == 'grid') {
                            echo '</div>';
                        }
                        if ($settings['dce_views_style_format'] == 'list') {
                            echo '</li>';
                        }
                }
                $k++;
            }


            switch ($settings['dce_views_style_format']) {
                case 'table':
                    echo '</tbody></table>';
                    if (isset($settings['dce_views_style_data_table']) && $settings['dce_views_style_data_table']) {
                        ?>
                        <script type="text/javascript">
                            jQuery(document).ready(function () {
                                jQuery('.elementor-element-<?php echo $this->get_id(); ?> table.datatable').DataTable();
                            });
                        </script>
                        <?php
                    }
                    break;
                case 'list':
                    echo '</ul>';
                    break;
                case 'grid':
                default:
                    echo '</div>';
            }

            echo '</div>';

            if (!empty($settings['dce_views_pagination'])) {
                //var_dump($settings['dce_views_post_per_page']);
                $this->_posts_nav($the_query, $settings);
            }

            /* Restore original Post Data */
            wp_reset_postdata();
        } else {

// no posts found
            if (isset($settings['dce_views_fallback']) && $settings['dce_views_fallback']) {
                echo '<div class="dce-view-results dce-view-results-fallback dce-views-no-results">';
                if (isset($settings['dce_views_fallback_type']) && $settings['dce_views_fallback_type'] == 'template') {
                    $fallback_content = '[dce-elementor-template id="' . $settings['dce_views_fallback_template'] . '"]';
                } else {
                    $fallback_content = __($settings['dce_views_fallback_text'], DCE_TEXTDOMAIN . '_texts');
                }
                $fallback_content = do_shortcode($fallback_content); // TODO FIX
                $fallback_content = DCE_Tokens::do_tokens($fallback_content);
                echo $fallback_content;
                echo '</div>';
            } else {
                if ($settings['dce_views_where_form_ajax']) {
                    echo '<div class="dce-view-results dce-view-results-ajax"></div>';
                }
            }
        }

        $post = $original_post;
// Restore original query object
        $wp_query = null;
        $wp_query = $original_query;

        //delete_option('dce-views-query-request');

        return true;
    }

    public function _exposed_sort($settings = null) {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }

        $post_fields = DCE_Helper::get_post_fields(true, false);

        if (!$settings['dce_views_order_random']) {
            if (isset($settings['dce_views_order_by']) && !empty($settings['dce_views_order_by'])) {
                $options = '';
                $i = 0;
                foreach ($settings['dce_views_order_by'] as $key => $asort) {
                    if (!$i) {
                        $order_field = $asort['dce_views_order_field'];
                        $order_sort = $asort['dce_views_order_field_sort'];
                        if (isset($_GET['orderby'])) {
                            list($order_sort, $order_field) = explode('__', $_GET['orderby'], 2);
                        }
                    }
                    if ($asort['dce_views_order_field_sort_exposed']) {
                        $options .= '<option value="ASC__' . $asort['dce_views_order_field'] . '"' . ($asort['dce_views_order_field'] == $order_field && $order_sort == 'ASC' ? ' selected' : '') . '>' . $post_fields[$asort['dce_views_order_field']] . ' ASC</option>';
                        $options .= '<option value="DESC__' . $asort['dce_views_order_field'] . '"' . ($asort['dce_views_order_field'] == $order_field && $order_sort == 'DESC' ? ' selected' : '') . '>' . $post_fields[$asort['dce_views_order_field']] . ' DESC</option>';
                    }
                }
                if ($options) {
                    ?>
                    <form action="" method="get" class="dce-view-exposed-sort <?php echo $settings['dce_views_order_class']; ?>">
                        <?php if ($settings['dce_views_order_label']) { ?>
                            <label for="order_<?php echo $this->get_id(); ?>">
                                <?php echo $settings['dce_views_order_label']; ?>
                            </label>
                        <?php } ?>
                        <select class="dce-input-sort" id="order_<?php echo $this->get_id(); ?>" name="orderby" onchange="jQuery(this).closest('form').submit();">
                            <?php echo $options; ?>
                        </select>
                        <?php
                        //$params = explode('&', $_SERVER['']);
                        if (!empty($_GET) && isset($_GET['eid']) && $_GET['eid'] == $this->get_id()) {
                            foreach ($_GET as $gkey => $gval) {
                                //$val = explode('=', $aparam, 2);
                                if ($gkey != 'eid' && $gkey != 'orderby') {
                                    echo '<input type="hidden" name="' . $gkey . '" value="' . $gval . '">';
                                }
                            }
                        }
                        ?>
                        <input type="hidden" name="eid" value="<?php echo $this->get_id(); ?>">
                    </form>
                    <?php
                }
            }
        }
    }

    public function _exposed_form($settings = null) {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }

        if ((isset($settings['dce_views_where_form']) && !empty($settings['dce_views_where_form']))) {
            $post_fields = DCE_Helper::get_post_fields(true, false);
            ?>

            <div class="dce-view-form-wrapper dce-view-exposed-form elementor-button-align-stretch <?php echo $settings['dce_views_where_form_class_wrapper']; ?>">
                <?php if ($settings['dce_views_style_form_text']) { ?>
                    <<?php echo $settings['dce_views_style_form_text_size']; ?> class="dce-views-form-title"><?php echo $settings['dce_views_style_form_text']; ?></<?php echo $settings['dce_views_style_form_text_size']; ?>>
                <?php } ?>
                <form id="dce-view-form-<?php echo $this->get_id(); ?>" method="get" action="" class="elementor-form-fields-wrapper dce-view-form <?php echo $settings['dce_views_where_form_class']; ?>">
                    <?php
                    foreach ($settings['dce_views_where_form'] as $key => $afield) {
                        if (!$afield['dce_views_where_form_field'])
                            continue;
                        $auto_label = !empty($post_fields[$afield['dce_views_where_form_field']]) ? $post_fields[$afield['dce_views_where_form_field']] : $this->taxonomies[$afield['dce_views_where_form_field']];
                        
                        $filter_class = 'elementor-field-group elementor-column elementor-col-' . $afield['dce_views_where_form_width'];
                        if ( ! empty( $afield['dce_views_where_form_width_tablet'] ) ) {
                                $filter_class .= ' elementor-md-' . $afield['dce_views_where_form_width_tablet'];
                        }
                        if ( ! empty( $afield['dce_views_where_form_width_mobile'] ) ) {
                                $filter_class .= ' elementor-sm-' . $afield['dce_views_where_form_width_mobile'];
                        }

                        /*if ( $afield['dce_views_where_form_allow_multiple'] ) {
                             $filter_class .= ' elementor-field-type-' . $afield['dce_views_where_form_type'] . '-multiple';
                        }*/
                        ?>
                        <div class="dce-view-field-wrapper <?php echo $filter_class; ?> <?php echo $settings['dce_views_where_form_class_filter']; ?>">
                            <div class="dce-view-field-filter dce-view-form-col-inner">
                            <label class="elementor-field-label dce-view-input-label <?php echo $afield['dce_views_where_form_class_label']; ?>" for="dce_view_<?php echo $afield['dce_views_where_form_field']; ?>">
                                <?php echo (isset($afield['dce_views_where_form_label']) && $afield['dce_views_where_form_label']) ? $afield['dce_views_where_form_label'] : $auto_label; ?>
                                <?php if ($afield['dce_views_where_form_required'] && $afield['dce_views_where_form_required_label'] != 'none') { ?>
                                    <span class="dce-form-required">
                                        <?php if ($afield['dce_views_where_form_required_label'] == 'asterisk') { ?>*<?php } ?>
                                        <?php
                                        if ($afield['dce_views_where_form_required_label'] == 'text') {
                                            echo $afield['dce_views_where_form_required_label_text'];
                                        }
                                        ?>
                                    </span>
                                <?php } ?>
                            </label>
                            <?php
                            $input_values = array();
                            $presel = DCE_Helper::str_to_array(',', $afield['dce_views_where_form_preselect']);
                            $tax_keys = array_keys($this->taxonomies);
                            $dce_views_where_form_type = $afield['dce_views_where_form_type'];
                            //var_dump($tax_keys);
                            if (!$afield['dce_views_where_form_required']) {
                                $input_values[] = array('key' => '', 'value' => $afield['dce_views_where_form_required_empty_label'], 'selected' => false);
                            }
                            if ($afield['dce_views_where_form_type'] == 'auto' && in_array($afield['dce_views_where_form_field'], $tax_keys)) {
                                $taxonomies_terms = $this->taxonomies_terms[$afield['dce_views_where_form_field']];
                                //var_dump($this->taxonomies_terms);
                                foreach ($taxonomies_terms as $akey => $avalue) {
                                    if ($akey) {
                                        $asel = false;
                                        $pezzi = explode(' (', $avalue);
                                        $term_title = reset($pezzi);
                                        $pezzi = explode(')', str_replace('(', ')', $avalue));
                                        if (count($pezzi) > 2) {
                                            $term_slug = $pezzi[count($pezzi) - 2];
                                            //$akey = $term_slug;
                                            if (in_array($term_slug, $presel)) {
                                                $asel = true;
                                            }
                                        }
                                        if (in_array($akey, $presel)) {
                                            $asel = true;
                                        }
                                        $input_values[] = array('key' => $akey, 'value' => $term_title, 'selected' => $asel);
                                    }
                                }
                                $dce_views_where_form_type = 'select';
                            } else {
                                $options = explode(PHP_EOL, $afield['dce_views_where_form_value']);
                                $options = array_filter($options);
                                if (!empty($options)) {
                                    foreach ($options as $akey => $aopt) {
                                        $aopt = trim($aopt);
                                        $option = explode('|', $aopt, 2);
                                        $akey = trim(reset($option));
                                        $avalue = end($option);
                                        $asel = false;
                                        if (in_array($akey, $presel)) {
                                            $asel = true;
                                        }
                                        $input_values[] = array('key' => $akey, 'value' => $avalue, 'selected' => $asel);
                                    }
                                }
                            }

                            if (empty($input_values)) {
                                if (DCE_Helper::is_plugin_active('acf')) {

                                    $field_conf = get_field_object($afield['dce_views_where_form_field']);
                                    //var_dump($field_conf);
                                    if ($field_conf && isset($field_conf['choices']) && !empty($field_conf['choices'])) {

                                        foreach ($field_conf['choices'] as $akey => $avalue) {
                                            $asel = (in_array($akey, $field_conf['default_value'])) ? true : false;
                                            $input_values[] = array('key' => $akey, 'value' => $avalue, 'selected' => $asel);
                                        }
                                        if ($afield['dce_views_where_form_type'] == 'auto') {
                                            $afield['dce_views_where_form_type'] = $field_conf['type'];
                                            if ($field_conf['type'] == 'true_false') {
                                                $afield['dce_views_where_form_type'] = 'checkbox';
                                            }
                                            if ($field_conf['type'] == 'button_group') {
                                                $afield['dce_views_where_form_type'] = 'radio';
                                            }
                                        }
                                    } else {
                                        if ($afield['dce_views_where_form_type'] == 'auto') {
                                            $afield['dce_views_where_form_type'] = 'text';
                                        }
                                    }
                                } else {
                                    if ($afield['dce_views_where_form_type'] == 'auto') {
                                        $afield['dce_views_where_form_type'] = 'text';
                                    }
                                }
                            }
                            //var_dump($input_values);

                            switch ($dce_views_where_form_type) {
                                case 'select':
                                    ?>
                                    <span class="dce-view-input dce-view-select <?php echo $afield['dce_views_where_form_class_input']; ?>">
                                        <select class="elementor-field elementor-field-textual elementor-size-sm" name="<?php echo $afield['dce_views_where_form_field']; ?>" id="dce_view_<?php echo $afield['dce_views_where_form_field']; ?>"<?php if ($afield['dce_views_where_form_required']) { ?> required<?php } ?>>
                                            <?php
                                            foreach ($input_values as $aopt) {
                                                ?>
                                                <option value="<?php echo $aopt['key']; ?>"<?php echo ((isset($_GET[$afield['dce_views_where_form_field']]) && $_GET[$afield['dce_views_where_form_field']] == $aopt['key']) || (!isset($_GET[$afield['dce_views_where_form_field']]) && $aopt['selected']) ? ' selected' : ''); ?>>
                                                    <?php echo $aopt['value']; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </span>
                                    <?php
                                    break;
                                case 'radio':
                                    foreach ($input_values as $okey => $aopt) {
                                        $checked = (isset($_GET[$afield['dce_views_where_form_field']]) && $_GET[$afield['dce_views_where_form_field']] == $aopt['key']) || (empty($_GET[$afield['dce_views_where_form_field']]) && $aopt['selected']) ? ' checked' : '';
                                        ?>
                                        <span class="dce-view-input dce-view-radio <?php echo $afield['dce_views_where_form_class_input']; ?>">
                                            <input type="radio" value="<?php echo $aopt['key']; ?>" name="<?php echo $afield['dce_views_where_form_field']; ?>" id="dce_view_<?php echo $afield['dce_views_where_form_field'] . '_' . $okey; ?>"<?php echo $checked; ?><?php if ($afield['dce_views_where_form_required']) { ?> required<?php } ?>>
                                            <label for="dce_view_<?php echo $afield['dce_views_where_form_field'] . '_' . $okey; ?>"><?php echo $aopt['value']; ?></label>
                                        </span>
                                        <?php
                                    }
                                    break;
                                /* case 'textarea':
                                  break; */
                                case 'checkbox':
                                    foreach ($input_values as $okey => $aopt) {
                                        $checked = (isset($_GET[$afield['dce_views_where_form_field']]) && $_GET[$afield['dce_views_where_form_field']] == $aopt['key']) || (empty($_GET[$afield['dce_views_where_form_field']]) && $aopt['selected']) ? ' checked' : '';
                                        ?>
                                        <span class="dce-view-input dce-view-checkbox <?php echo $afield['dce_views_where_form_class_input']; ?>">
                                            <input type="checkbox" value="<?php echo $aopt['key']; ?>" name="<?php echo $afield['dce_views_where_form_field']; ?>[]" id="dce_view_<?php echo $afield['dce_views_where_form_field'] . '_' . $okey; ?>"<?php echo $checked; ?><?php if ($afield['dce_views_where_form_required']) { ?> required<?php } ?>>
                                            <label for="dce_view_<?php echo $afield['dce_views_where_form_field'] . '_' . $okey; ?>"><?php echo $aopt['value']; ?></label>
                                        </span>
                                        <?php
                                    }
                                    break;
                                case 'text':
                                default:
                                    ?>
                                    <span class="dce-view-input dce-view-text <?php echo $afield['dce_views_where_form_class_input']; ?>">
                                        <input class="elementor-field elementor-field-textual elementor-size-sm" type="text" placeholder="<?php echo $afield['dce_views_where_form_placeholder']; ?>" value="<?php echo isset($_GET[$afield['dce_views_where_form_field']]) ? $_GET[$afield['dce_views_where_form_field']] : $afield['dce_views_where_form_preselect']; ?>" name="<?php echo $afield['dce_views_where_form_field']; ?>" id="dce_view_<?php echo $afield['dce_views_where_form_field']; ?>"<?php if ($afield['dce_views_where_form_required']) { ?> required<?php } ?>>
                                    </span>
                                <?php
                            }
                            ?>
                            <?php if ($afield['dce_views_where_form_hint']) { ?>
                                <small class="dce-view-input-hint"><i class="fa fa-info" aria-hidden="true"></i> <?php echo $afield['dce_views_where_form_hint']; ?></small>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } 
                    $action_class = 'elementor-field-group elementor-column elementor-col-' . $settings['dce_views_where_form_action_width'];
                    if ( ! empty( $settings['dce_views_where_form_action_width_tablet'] ) ) {
                            $action_class .= ' elementor-md-' . $settings['dce_views_where_form_action_width_tablet'];
                    }
                    if ( ! empty( $settings['dce_views_where_form_action_width_mobile'] ) ) {
                            $action_class .= ' elementor-sm-' . $settings['dce_views_where_form_action_width_mobile'];
                    }
                    ?>
                    <div class="dce-view-exposed-form-action <?php echo $action_class; ?> <?php echo $settings['dce_views_where_form_class_buttons']; ?>">
                        <div class="dce-view-exposed-form-buttons elementor-field-type-submit dce-view-form-col-inner">
                            <input type="hidden" name="eid" value="<?php echo $this->get_id(); ?>">
                            <button class="button dce-button elementor-button elementor-size-sm find <?php echo $settings['dce_views_where_form_class_button']; ?>" type="submit"><span class="elementor-button-text"><?php echo $settings['dce_views_style_form_submit_text']; ?></span></button>
                            <?php if ($settings['dce_views_where_form_reset']) { ?><input class="button dce-button elementor-button elementor-size-sm reset <?php echo $settings['dce_views_where_form_class_button']; ?>" type="reset" value="<?php _e('Reset'); ?>"><?php } ?>
                        </div>
                    </div>
                </form>
            </div>
            <?php
            $this->_ajax($settings);
        }
    }

    public function _ajax($settings = null) {
        if (!$settings['dce_views_where_form_ajax'])
            return false;
        ?>
        <script>
            function dce_views_update_result() {
                //var result_container = '.elementor-element-<?php echo $this->get_id(); ?> .elementor-widget-container';
                var result_container = '.elementor-element-<?php echo $this->get_id(); ?> .dce-view-results';
                var sort_container = '.elementor-element-<?php echo $this->get_id(); ?> .dce-view-exposed-sort';
                var pagination_container = '.elementor-element-<?php echo $this->get_id(); ?> .dce-posts-pagination';

                jQuery(result_container).html('<div class="dce-preloader" style="text-align: center;"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>');
                var results = jQuery.get('?' + jQuery('#dce-view-form-<?php echo $this->get_id(); ?>').serialize(), function (data) {
                    //console.log(data);
                    jQuery(result_container).html(jQuery(data).find(result_container).html());
                    jQuery(sort_container).html(jQuery(data).find(sort_container).html());
                    jQuery(pagination_container).html(jQuery(data).find(pagination_container).html());
                });
            }
            jQuery(document).ready(function () {
                jQuery('#dce-view-form-<?php echo $this->get_id(); ?>').on('submit', function () {
                    dce_views_update_result();
                    return false;
                });

        <?php if ($settings['dce_views_where_form_ajax_onchange']) { ?>
                    jQuery('#dce-view-form-<?php echo $this->get_id(); ?> input, #dce-view-form-<?php echo $this->get_id(); ?> select').on('change', function () {
                        dce_views_update_result();
                        return false;
                    });
                    jQuery('#dce-view-form-<?php echo $this->get_id(); ?> input[type=text]').on('keyup', function () {
                        if (jQuery(this).val().length > 3) {
                            dce_views_update_result();
                        }
                        return false;
                    });
        <?php } ?>
            });
        </script>
        <?php
        return true;
    }

    public function _posts_nav($the_query = null, $settings = array()) {

        if (empty($settings)) {
            $settings = $this->get_settings_for_display();
        }
        if (empty($settings)) {
            return false;
        }
//global $wp_query;
//var_dump($the_query->max_num_pages);
        $max = intval($the_query->max_num_pages);
        $dce_views_pagination_page_limit = intval($settings['dce_views_pagination_page_limit']);
        if ($dce_views_pagination_page_limit && $dce_views_pagination_page_limit < $max) {
            $max = $dce_views_pagination_page_limit;
        }
        //var_dump($max);
        if ($max <= 1)
            return;

        $paged = $this->get_current_page();


        /** Add current page to the array */
        if ($paged >= 1)
            $links[] = $paged;

        /** Add the pages around the current page to the array */
        if ($paged >= 3) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }

        if (( $paged + 2 ) <= $max) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }
        ?>
        <nav class="navigation posts-navigation dce-posts-navigation elementor-pagination" role="navigation" arial-label="<?php _e('Pagination'); ?>">
            <ul class="dce-page-numbers">
                <?php
                if (empty($settings['dce_views_pagination_type']) || $settings['dce_views_pagination_type'] == 'prev_next' || $settings['dce_views_pagination_type'] == 'numbers_and_prev_next') {
                    /** Previous Post Link */
                    if ($paged > 1) {
                        echo '<li><a class="page-numbers pagination__prev" href="' . $this->get_posts_link('prev') . '">';
                        if (empty($settings['dce_views_pagination_prev_label'])) {
                            echo '&lt;';
                        } else {
                            echo $settings['dce_views_pagination_prev_label'];
                        }
                        echo '</a></li> ';
                    }
                }

                if (empty($settings['dce_views_pagination_type']) || $settings['dce_views_pagination_type'] == 'numbers' || $settings['dce_views_pagination_type'] == 'numbers_and_prev_next') {

                    if ($settings['dce_views_pagination_numbers_shorten']) {
                        //var_dump($settings['dce_views_pagination_numbers_shorten']);

                        /** Link to first page, plus ellipses if necessary */
                        if (!in_array(1, $links)) {
                            $class = 1 == $paged ? ' current' : '';

                            printf('<li><a class="page-numbers%s" href="%s">%s</a></li>' . "\n", $class, esc_url($this->get_posts_link('first')), '1');

                            if (!in_array(2, $links))
                                echo '<li class="dots">…</li>';
                        }

                        /** Link to current page, plus 2 pages in either direction if necessary */
                        sort($links);
                        foreach ((array) $links as $link) {
                            $class = $paged == $link ? ' current' : '';
                            printf('<li><a class="page-numbers%s" href="%s">%s</a></li>' . "\n", $class, esc_url($this->get_posts_link('current', $link)), $link);
                        }

                        /** Link to last page, plus ellipses if necessary */
                        if (!in_array($max, $links)) {
                            if (!in_array($max - 1, $links))
                                echo '<li class="dots">…</li>' . "\n";

                            $class = $paged == $max ? ' current' : '';
                            printf('<li><a class="page-numbers%s" href="%s">%s</a></li>' . "\n", $class, esc_url($this->get_posts_link('last', $max)), $max);
                        }
                    } else {

                        for ($p = 1; $p <= $max; $p++) {
                            $class = $paged == $p ? ' current' : '';
                            printf('<li><a class="page-numbers%s" href="%s">%s</a></li>' . "\n", $class, esc_url($this->get_posts_link('', $p)), $p);
                        }
                    }
                }

                if (empty($settings['dce_views_pagination_type']) || $settings['dce_views_pagination_type'] == 'prev_next' || $settings['dce_views_pagination_type'] == 'numbers_and_prev_next' || $settings['dce_views_pagination_type'] == 'infinite_scroll') {
                    /** Next Post Link */
                    if ($paged < $max) {
                        echo '<li><a class="page-numbers pagination__next" href="' . $this->get_posts_link() . '">';
                        if (empty($settings['dce_views_pagination_next_label'])) {
                            echo '&gt;';
                        } else {
                            echo $settings['dce_views_pagination_next_label'];
                        }
                        echo '</a></li>';
                    }
                }
                ?>
            </ul>
        </nav>
        <?php
        $this->_infinite($settings);
    }

    public function _infinite($settings = array()) {
        if ($settings['dce_views_pagination']) {
            if ($settings['dce_views_pagination_type'] == 'infinite_scroll') {
                ?>
                <!-- status elements -->
                <div class="scroller-status">
                    <div class="infinite-scroll-request loader-ellips" style="text-align: center;">
                        <br>
                        <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="infinite-scroll-last"><?php echo $settings['dce_views_limit_scroll_last']; ?></p>
                    <p class="infinite-scroll-error" style="text-align: center;"><a class="infinite__next" href="<?php echo $this->get_posts_link(); ?>"><i class="fa fa-angle-double-down" aria-hidden="true"></i></a></p>
                </div>
                <script>
                    jQuery(window).load(function () {
                        jQuery('.elementor-element-<?php echo $this->get_id(); ?> .dce-view-row').infiniteScroll({
                            // options
                            path: '.elementor-element-<?php echo $this->get_id(); ?> .pagination__next',
                            //path: '?pag={{#}}&eid=<?php echo $this->get_id(); ?>',
                            append: '.elementor-element-<?php echo $this->get_id(); ?> .dce-view-row .item-page',
                            history: 'replace',
                            hideNav: '.elementor-element-<?php echo $this->get_id(); ?> .dce-posts-navigation',
                            status: '.elementor-element-<?php echo $this->get_id(); ?> .scroller-status',
                            debug: true,
                        });
                    });
                </script>
                <?php
                return true;
            }
        }
        return false;
    }

    public function get_current_page() {
        return isset($_GET['pag']) && isset($_GET['eid']) && $_GET['eid'] == $this->get_id() ? absint($_GET['pag']) : 1;
    }

    public function get_posts_link($verso = 'next', $page = 1) {
        global $wp;
        $current_url = home_url(add_query_arg(array(), $wp->request));

        $paged = $this->get_current_page();
        switch ($verso) {
            case 'next':
                $page = $paged + 1;
                break;
            case 'prev':
                $page = $paged - 1;
                break;
            case 'current':
            case 'first':
            case 'last':
        }
        $ret = $current_url . '/?';
        if (!empty($_GET) && isset($_GET['eid'])) {
//var_dump($_GET);
            foreach ($_GET as $gkey => $gval) {
                if ($gkey != 'pag' && $gkey != 'eid') {
                    $ret .= '&' . $gkey . '=' . $gval;
                }
            }
        }
        if ($ret != $current_url . '/?') {
            $ret .= '&';
        }

//if (strpos($ret, 'eid=') === false) {
        $ret .= 'eid=' . $this->get_id();
        $ret .= '&pag=' . $page;
//}
        return $ret;
    }

    public function get_wp_query_args($settings = null) {
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }

        $this->taxonomies = $taxonomies = DCE_Helper::get_taxonomies();
        $taxonomies_terms = array();
        foreach ($taxonomies as $tkey => $atax) {
            $taxonomies_terms[$tkey] = DCE_Helper::get_taxonomy_terms($tkey);
        }
        $this->taxonomies_terms = $taxonomies_terms;

        $args = array(
//'ignore_sticky_posts' => 1,
//'post__not_in' => array_merge($posts_excluded, $exclude_io),
        );

// FROM
        if (isset($settings['dce_views_from_dynamic']) && $settings['dce_views_from_dynamic']) {
            global $wp_query;
            if (is_archive()) {
                $args = $wp_query->query_vars;
            } else {
                $post_id = $wp_query->query['p'];
                $taxonomies = get_post_taxonomies($post_id);
                $terms = array();
//$terms = wp_get_post_terms($post_id);
                foreach ($taxonomies as $atax) {
                    $terms = $terms + wp_get_post_terms($post_id, $atax);
                }


                if (empty($terms)) {
// same type
                    $cpt = get_post_type();
                    $args['post_type'] = $cpt;
                } else {
// same taxonomy terms associated
                    foreach ($terms as $akey => $aterm) {
                        $tkey = $aterm->taxonomy;
                        switch ($tkey) {
                            case 'category':
                                if (isset($args['tag__in'])) {
                                    $args['category__in'] = array_merge($args['category__in'], array($aterm->term_id));
                                } else {
                                    $args['category__in'] = array($aterm->term_id);
                                }
                                break;

                            case 'post_tag':
                                var_dump($aterm->term_id);
                                if (isset($args['tag__in'])) {
                                    $args['tag__in'] = array_merge($args['tag__in'], array($aterm->term_id));
                                } else {
                                    $args['tag__in'] = array($aterm->term_id);
                                }
                                break;

                            default:
                                if (isset($args['tax_query'][$tkey])) {
                                    $args['tax_query'][$tkey] = array(
                                        'taxonomy' => $tkey,
                                        'field' => 'term_id',
                                        'terms' => array_merge($args['tax_query'][$tkey]['terms'], array($aterm->term_id)),
                                        'operator' => 'IN', // optional??
                                    );
                                } else {
                                    $args['tax_query'][$tkey] = array(
                                        'taxonomy' => $tkey,
                                        'field' => 'term_id',
                                        'terms' => array($aterm->term_id),
                                        'operator' => 'IN', // optional??
                                    );
                                }
                        }
                    }
                }
// exclude himself
                $args['post__not_in'] = array($post_id);
            }
        } else {

            if (!empty($settings['dce_views_cpt'])) {
                if (count($settings['dce_views_cpt']) > 1) {
                    $args['post_type'] = $settings['dce_views_cpt'];
                } else {
                    $args['post_type'] = reset($settings['dce_views_cpt']);
                }
            }
            if (!empty($settings['dce_views_status'])) {
                if (count($settings['dce_views_status']) > 1) {
                    $args['post_status'] = $settings['dce_views_status'];
                } else {
                    $args['post_status'] = reset($settings['dce_views_status']);
                    if ($settings['dce_views_cpt'] == array('attachment')) {
                        $args['post_status'] = 'any';
                    }
                }
            } elseif ($settings['dce_views_cpt'] == array('attachment')) {
                $args['post_status'] = 'any';
            }


            if ($settings['dce_views_pagination']) {
                if ($settings['dce_views_post_per_page'] > 0) {
                    $args['posts_per_page'] = $settings['dce_views_post_per_page'];
                }
            } else {
                $args['nopaging'] = true;
                $args['posts_per_page'] = -1;
            }

// FROM - filter by taxonomy term
            foreach ($this->taxonomies as $tkey => $atax) {
                if (!empty($settings['dce_views_term_' . $tkey])) {
                    switch ($tkey) {
                        case 'category':
                            $args['category__in'] = array_map('intval', $settings['dce_views_term_' . $tkey]);
                            break;

                        case 'post_tag':
                            $args['tag__in'] = array_map('intval', $settings['dce_views_term_' . $tkey]);
//$args['category__not_in'] = $settings['dce_views_term_' . $tkey];
                            break;

                        default:
//var_dump($this->taxonomies_terms);
                            if ($tkey && !empty($this->taxonomies_terms[$tkey])) {
                                $args['tax_query'][] = array(
                                    'taxonomy' => $tkey,
                                    'field' => 'term_id',
                                    'terms' => array_map('intval', $settings['dce_views_term_' . $tkey]),
                                    'operator' => 'IN', // optional??
                                );
                            }
                    }
                }
            }
            if (isset($args['tax_query'])) {
                $args['tax_query']['relation'] = $settings['dce_views_tax_relation'];
            }
        }

// COLLECT ALL WHERE CONDITIONS
        $where_fields = $settings['dce_views_where'];
        if (!empty($settings['dce_views_where_form'])) {
            foreach ($settings['dce_views_where_form'] as $afield) {
                if (isset($_GET[$afield['dce_views_where_form_field']]) || $settings['dce_views_where_form_result']) {
                    $default_value = $afield['dce_views_where_form_preselect'];
                    if (in_array($afield['dce_views_where_form_type'], array('select', 'radio', 'checkbox'))) {
                        $options = explode(PHP_EOL, $afield['dce_views_where_form_value']);
                        $i = 0;
                        foreach ($options as $okey => $aopt) {
                            $aopt = trim($aopt);
                            $option = explode('|', $aopt, 2);
                            if (!$i && !$afield['dce_views_where_form_value']) {
                                $default_value = reset($option);
                            }
                            $i++;
                        }
                    }
                    $afield_value = isset($_GET[$afield['dce_views_where_form_field']]) && $_GET['eid'] == $this->get_id() ? $_GET[$afield['dce_views_where_form_field']] : $default_value;
//if ($default_value) {
                    if (isset($this->taxonomies[$afield['dce_views_where_form_field']])) {
                        if (is_array($afield_value)) {
                            $tax_ids = array_map('intval', $afield_value);
                        } else {
                            $tax_ids = array(intval($afield_value));
                        }
                        if (!empty($tax_ids) && $tax_ids[0]) {
                            switch ($afield['dce_views_where_form_field']) {
                                case 'category':
// if (isset($args['category__in'])) {
//    $tax_ids = array_intersect($tax_ids, $args['category__in']);
// }
                                    $args['category__in'] = $tax_ids;
                                    break;

                                case 'post_tag':
                                    $args['tag__in'] = $tax_ids;
                                    break;

                                default:
                                    if ($afield['dce_views_where_form_field'] && !empty($this->taxonomies_terms[$afield['dce_views_where_form_field']])) {
                                        $args['tax_query'][] = array(
                                            'taxonomy' => $afield['dce_views_where_form_field'],
                                            'field' => 'term_id',
                                            'terms' => $tax_ids,
                                            'operator' => 'IN', // optional??
                                        );
                                    }
                            }
                        }
                    } else {
                        $where_fields[] = array(
                            'dce_views_where_field' => $afield['dce_views_where_form_field'],
                            'dce_views_where_value' => $afield_value,
                            'dce_views_where_operator' => $afield['dce_views_where_form_operator'],
                            'dce_views_where_rule' => $afield['dce_views_where_form_rule'],
                            'dce_views_where_form_type' => $afield['dce_views_where_form_type'],
                        );
                    }
                }
            }
        }

        if ($settings['dce_views_pagination'] && $settings['dce_views_pagination_page_limit']) {
            $args['max_num_pages'] = intval($settings['dce_views_pagination_page_limit']);
        }

        $post__in = array();
        $first = true;

// WHERE - NATIVE
        if (!empty($where_fields)) {
            foreach ($where_fields as $awhere) {
//if ($awhere['dce_views_where_value']) {
                if ($awhere['dce_views_where_field'] && $awhere['dce_views_where_operator'] && !DCE_Helper::is_post_meta($awhere['dce_views_where_field'])) {
                    /*
                      switch ($awhere['dce_views_where_field']) {
                      case 'ID':
                      if  (is_numeric($awhere['dce_views_where_value'])) {
                      if ($settings['dce_views_cpt'] == ['page']) {
                      $args['page_id'] = $awhere['dce_views_where_value'];
                      } else {
                      $args['p'] = $awhere['dce_views_where_value'];
                      }
                      } else {
                      $ids = $this->explode(",",$awhere['dce_views_where_value']);
                      if (count($ids) > 1) {
                      if ($settings['dce_views_cpt'] == ['page']) {
                      $args['page_id'] = $ids;
                      } else {
                      $args['post__in'] = $ids;
                      }
                      }
                      }
                      break;
                      case 'post_slug':
                      if ($settings['dce_views_cpt'] == ['page']) {
                      $args['pagename'] = $awhere['dce_views_where_value'];
                      } else {
                      $args['name'] = $awhere['dce_views_where_value'];
                      }
                      break;
                      case 'post_title':
                      $args['title'] = $awhere['dce_views_where_value'];
                      break;
                      case 'post_parent':
                      if  (is_numeric($awhere['dce_views_where_value'])) {
                      $args['post_parent'] = $awhere['dce_views_where_value'];
                      } else {
                      $ids = $this->explode(",",$awhere['dce_views_where_value']);
                      if (count($ids) > 1) {
                      $args['post_parent__in'] = $ids;
                      $args['author__not_in'] = $ids;
                      } else {
                      $args['post_parent'] = trim($awhere['dce_views_where_value']);
                      }
                      }
                      break;
                      case 'post_author':
                      if  (is_numeric($awhere['dce_views_where_value'])) {
                      $args['author'] = $awhere['dce_views_where_value'];
                      } else {
                      $ids = $this->explode(",",$awhere['dce_views_where_value']);
                      if (count($ids) > 1) {
                      $args['author__in'] = $ids;
                      $args['author__not_in'] = $ids;
                      } else {
                      $args['author_name'] = $awhere['dce_views_where_value'];
                      }
                      }
                      break;
                      case 'post_password':
                      if ($awhere['dce_views_where_value'] == 'true' || $awhere['dce_views_where_value'] == 'false') {
                      $args['has_password'] = $awhere['dce_views_where_value'];
                      } else {
                      $args['post_password'] = $awhere['dce_views_where_value'];
                      }
                      break;
                      case 'post_excerpt':
                      case 'post_content':
                      $args['s'] = $awhere['dce_views_where_value'];
                      break;
                      case 'post_date':
                      case 'post_date_gtm':
                      case 'post_modified':
                      case 'post_modified_gmt':
                      $dc = false;
                      if (in_array($awhere['dce_views_where_operator'], array('<','<='))) {
                      $dc = array(
                      'before' => $awhere['dce_views_where_value'],
                      );
                      }
                      if (in_array($awhere['dce_views_where_operator'], array('>','>='))) {
                      $dc = array(
                      'after' => $awhere['dce_views_where_value'],
                      );
                      }
                      if ($dc && in_array($awhere['dce_views_where_operator'], array('>=','<='))) {
                      $dc['inclusive'] = true;
                      }
                      if ($dc) {
                      $dc['column'] = $awhere['dce_views_where_field'];
                      if (isset($args['date_query'])) {
                      $args['date_query'] = array($dc);
                      } else {
                      $args['date_query'][] = $dc;
                      }
                      }
                      break;
                      case 'comment_count':
                      $args['comment_count'] = array(
                      'value' => $awhere['dce_views_where_value'],
                      'compare' => $awhere['dce_views_where_operator'],
                      );
                      break;
                      }
                     */
//var_dump($awhere);
// need some raw query because wp_query has limitations        
                    $post_ids = $this->get_post_ids($awhere);
                    $this->post__in[$awhere['dce_views_where_field']] = $post_ids;
                    if ($awhere['dce_views_where_rule']) {
                        if (!$first) {
                            $post__in = array_intersect($post__in, $post_ids);
                        } else {
                            $post__in = $post_ids;
                        }
                    } else {
                        $post__in = array_merge($post__in, $post_ids);
                    }
                    $first = false;
                }
//}
            }
//var_dump($post__in);
        }



        if ($settings['dce_views_attachment_mime_type']) {
            $types = $this->explode(',', $settings['dce_views_attachment_mime_type']);
            if (count($types) > 1) {
                $args['post_mime_type'] = $types;
            } else {
                $args['post_mime_type'] = $settings['dce_views_attachment_mime_type'];
            }
        }

// WHERE - META
        if (!empty($where_fields)) {
            foreach ($where_fields as $awhere) {
                if ($awhere['dce_views_where_field'] && $awhere['dce_views_where_operator'] && DCE_Helper::is_post_meta($awhere['dce_views_where_field'])) {
                    $mt = array(
                        'key' => $awhere['dce_views_where_field'],
                        'value' => $awhere['dce_views_where_value'],
                        'compare' => $awhere['dce_views_where_operator'],
                    );
                    if (in_array($awhere['dce_views_where_operator'], array('IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'))) {
                        $values = $this->explode(',', $awhere['dce_views_where_value']);
                        if (is_numeric(reset($values))) {
                            $mt['type'] = 'numeric';
                        }
                        if (count($values) > 1) {
                            $mt['values'] = $values;
                        }
                    }
//$args['meta_query'][] = $mt;
//$args['meta_query']['relation'] = $afield['dce_views_where_rule'];
//$args['meta_key'][] = $afield['dce_views_where_field'];

                    $post_ids = $this->get_post_ids($awhere);
                    $this->post__in[$awhere['dce_views_where_field']] = $post_ids;
                    if ($awhere['dce_views_where_rule']) {
                        if (!$first) {
                            $post__in = array_intersect($post__in, $post_ids);
                        } else {
                            $post__in = $post_ids;
                        }
                    } else {
                        $post__in = array_merge($post__in, $post_ids);
                    }
                    $first = false;
                }
            }
        }

//var_dump($this->post__in);
        if (!$first) {
            if (!empty($post__in)) {
                $args['post__in'] = $post__in;
            } else {
// NO RESULTS
                $args['post__in'] = array(0);
            }
        }

        if (isset($args['post__in']) && isset($args['post__not_in'])) {
            $args['post__in'] = array_diff($args['post__in'], $args['post__not_in']);
        }

// ORDER BY
        if ($settings['dce_views_order_random']) {
            $args['orderby'] = 'rand';
        } else {
            if (isset($settings['dce_views_order_by']) && !empty($settings['dce_views_order_by'])) {
                if (isset($_GET['orderby']) && isset($_GET['eid']) && $_GET['eid'] == $this->get_id()) {
                    list($order_sort, $order_field) = explode('__', $_GET['orderby'], 2);
                    foreach ($settings['dce_views_order_by'] as $key => $asort) {
                        if ($asort['dce_views_order_field_sort_exposed']) {
                            if ($order_field == $asort['dce_views_order_field']) {
                                $args['orderby'][$order_field] = $order_sort;
//var_dump($args);
                                break;
                            }
                        }
                    }
                } else {
                    foreach ($settings['dce_views_order_by'] as $key => $asort) {
                        if ($asort['dce_views_order_field']) {
                            if (!isset($args['orderby'][$asort['dce_views_order_field']])) {
                                if (DCE_Helper::is_post_meta($asort['dce_views_order_field'])) {
                                    $args['meta_key'][] = $asort['dce_views_order_field'];
//$args['meta_key'] = $asort['dce_views_order_field'];
                                    /* if (!isset($args['meta_query'])) {
                                      $args['meta_query'] = array(
                                      'relation' => 'OR',
                                      );
                                      }
                                      $args['meta_query'][$asort['dce_views_order_field'].'_clause'] = array(
                                      'key' => $asort['dce_views_order_field'],
                                      'compare' => 'EXISTS',
                                      ); */
//$args['orderby'][$asort['dce_views_order_field'].'_clause'] = $asort['dce_views_order_field_sort'];
                                    $args['orderby']['meta_value'] = $asort['dce_views_order_field_sort'];
                                } else {
                                    $dce_views_order_field = $asort['dce_views_order_field'];
//$dce_views_order_field = '.'.$dce_views_order_field;
//$dce_views_order_field = str_replace('.post_', '', $dce_views_order_field);
//$dce_views_order_field = str_replace('.', '', $dce_views_order_field);
                                    $args['orderby'][$dce_views_order_field] = $asort['dce_views_order_field_sort'];
                                }
                            }
                        }
                    }
                }
            }
            if (isset($args['orderby']) && count($args['orderby']) == 1) {
                $dce_views_order_field = array_key_first($args['orderby']);
                $args['order'] = reset($args['orderby']); //[$args['meta_key']];
                if (isset($args['meta_key'])) {
                    $args['meta_key'] = reset($args['meta_key']);
                    switch (DCE_Helper::get_post_meta_type($args['meta_key'])) {
                        case 'number':
                            $args['orderby'] = 'meta_value_num';
                            break;
                        case 'date':
                        default :
                            $args['orderby'] = $dce_views_order_field;
//$args['orderby'] = $args['orderby'][$args['meta_key']];
//$args['orderby'] = $args['meta_key'].'_clause';
                    }
                } else {
                    $args['orderby'] = $dce_views_order_field;
                }
            }
//var_dump($args);
        }

// OFFSET
        if ($settings['dce_views_limit_offset']) {
            $args['offset'] = $settings['dce_views_limit_offset'];
        }

// PAGE
        if (isset($_GET['pag']) && $_GET['pag'] > 0 && isset($_GET['eid']) && $_GET['eid'] == $this->get_id()) {
            $args['paged'] = $this->get_current_page();
        }

        if (!isset($args['post_status'])) {
            $args['post_status'] = 'publish';
        }

        return $args;
    }

    public function get_post_ids($awhere, $retry = null) {
        global $wpdb;

        if (!$retry) {
            if (isset($_GET[$awhere['dce_views_where_field']]) && $_GET['eid'] == $this->get_id()) {
                $search_value = $_GET[$awhere['dce_views_where_field']];
            } else {
                $search_value = $awhere['dce_views_where_value'];
            }
        } else {
            $search_value = $retry;
//var_dump($search_value); die();
        }

        if ($awhere['dce_views_where_operator'] == 'IN' || $awhere['dce_views_where_operator'] == 'NOT IN') {
            $search_value = '(' . implode(',', $this->explode(',', $search_value)) . ')';
        }
        if ($awhere['dce_views_where_operator'] == 'BETWEEN' || $awhere['dce_views_where_operator'] == 'NOT BETWEEN') {
            $search_value = implode('" AND "', $this->explode(',', $search_value));
        }

        if (DCE_Helper::is_post_meta($awhere['dce_views_where_field'])) {


            $search_query = 'SELECT pm.post_id AS "ID", p.post_type, p.post_parent FROM ' . $wpdb->prefix . 'postmeta pm, ' . $wpdb->prefix . 'posts p';
            $acf_repeater_block_fields = DCE_Helper::get_all_acf(false, array('repeater', 'block'));
            if (DCE_Helper::is_plugin_active('acf') && array_key_exists($awhere['dce_views_where_field'], $acf_repeater_block_fields)) {
// is a repeater or block...
                $search_query .= ' WHERE p.ID = pm.post_id AND pm.meta_key LIKE "' . $awhere['dce_views_where_field'] . '_%" AND pm.meta_value ';
            } else {
                $search_query .= ' WHERE p.ID = pm.post_id AND pm.meta_key LIKE "' . $awhere['dce_views_where_field'] . '" AND pm.meta_value ';
            }
        } else {
            $search_query = 'SELECT ID, post_type, post_parent FROM ' . $wpdb->prefix . 'posts';
            $search_query .= ' WHERE post_type NOT LIKE "revision" AND ' . $awhere['dce_views_where_field'] . ' ';
        }


        $search_query .= $awhere['dce_views_where_operator'];
        if ($awhere['dce_views_where_operator'] != 'IS NULL' && $awhere['dce_views_where_operator'] != 'IS NOT NULL') {
            $search_query .= ' ';
            if ($awhere['dce_views_where_operator'] != 'IN' && $awhere['dce_views_where_operator'] != 'NOT IN') {
                $search_query .= '"';
            }
            if ($awhere['dce_views_where_operator'] == 'LIKE' || $awhere['dce_views_where_operator'] == 'NOT LIKE') {
                $search_query .= '%';
            }
            $search_query .= $search_value;
            if ($awhere['dce_views_where_operator'] == 'LIKE' || $awhere['dce_views_where_operator'] == 'NOT LIKE') {
                $search_query .= '%';
            }
            if ($awhere['dce_views_where_operator'] != 'IN' && $awhere['dce_views_where_operator'] != 'NOT IN') {
                $search_query .= '"';
            }
        }
        // var_dump($search_query);

        $results = $wpdb->get_results($search_query);
        $post_ids = array();
        foreach ($results as $key => $apost) {
            $pid = intval($apost->ID);
            if (DCE_Helper::is_post_meta($awhere['dce_views_where_field'])) {
                if ($apost->post_type == 'revision') {
                    if (!in_array(intval($apost->post_parent), $post_ids)) {
                        $post_ids[] = intval($apost->post_parent);
                    }
                } else {
                    if (!in_array($pid, $post_ids)) {
                        $post_ids[] = $pid;
                    }
                }
            } else {
                if (!in_array($pid, $post_ids)) {
                    $post_ids[] = $pid;
                }
            }
        }
//var_dump($post_ids);
        if (empty($post_ids) && !$retry) {
            if (isset($awhere['dce_views_where_form_type']) && $awhere['dce_views_where_form_type'] == 'text') {
                $words = explode(' ', $search_value);
//var_dump($words);
                if ($words > 2) {
                    foreach ($words as $key => $value) {
                        if (strlen($value) > 3) {
//var_dump($value);
                            $post_ids = array_merge($post_ids, $this->get_post_ids($awhere, $value));
                        }
                    }
                }
            }
        }
        return $post_ids;
    }

    public function get_field_value($afield, $settings = null) {
        global $post;
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        $field_value = DCE_Helper::get_post_value($post->ID, $afield['dce_views_select_field']);

        if ($afield['dce_views_select_render'] == 'rewrite' && $afield['dce_views_select_rewrite']) {
            $field_value = DCE_Tokens::replace_var_tokens($afield['dce_views_select_rewrite'], 'field', $field_value);
            $field_value = DCE_Tokens::do_tokens($field_value);
        }
        if (!$field_value && $afield['dce_views_select_no_results']) {
            $field_value = DCE_Tokens::do_tokens($afield['dce_views_select_no_results']);
        }
        return $field_value;
    }

    public function explode($delimiter, $string, $format = null) {
        return DCE_Helper::str_to_array($delimiter, $string, $format);
    }

}

/*
  $options = array(
  'posts_per_page' => -1,
  'suppress_filters' => false, // important!
  'post_type' => 'post',
  'post_status' => 'publish',
  );
  $keyword = 'quote';

  add_filter( 'posts_where', 'my_filter_post_where' );
  $posts = get_posts( $options );
  remove_filter( 'posts_where', 'my_filter_post_where' );

  function my_filter_post_where( $where) {
  global $wpdb;
  global $keyword;

  $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $keyword ) ) . '%\'';

  return $where;
  }
 */