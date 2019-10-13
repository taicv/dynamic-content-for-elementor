<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\DCE_Tokens;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

function _dce_extension_form_save($field) {
    switch ($field) {
        case 'enabled':
            return true;
        case 'docs':
            return 'https://www.dynamic.ooo/widget/save-elementor-pro-form/';
        case 'description' :
            return __('Add Save Actions to Elementor PRO Form', 'dynamic-content-for-elementor');
    }
}

if (!DCE_Helper::is_plugin_active('elementor-pro')) {

    class DCE_Extension_Form_Save extends DCE_Extension_Prototype {

        public $name = 'Form Save';
        private $is_common = false;
        public static $depended_plugins = ['elementor-pro'];

        static public function is_enabled() {
            return _dce_extension_form_save('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_save('description');
        }

        public function get_docs() {
            return _dce_extension_form_save('docs');
        }

    }

} else {

    class DCE_Extension_Form_Save extends \ElementorPro\Modules\Forms\Classes\Action_Base {

        public $name = 'Form Save';
        public static $depended_plugins = ['elementor-pro'];
        public static $docs = 'https://www.dynamic.ooo/widget/save-elementor-pro-form/';

        static public function is_enabled() {
            return _dce_extension_form_save('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_save('description');
        }

        public function get_docs() {
            return _dce_extension_form_save('docs');
        }

        public static function get_plugin_depends() {
            return self::$depended_plugins;
        }

        static public function get_satisfy_dependencies($ret = false) {
            return true;
        }

        /**
         * Get Name
         *
         * Return the action name
         *
         * @access public
         * @return string
         */
        public function get_name() {
            return 'dce_form_save';
        }

        /**
         * Get Label
         *
         * Returns the action label
         *
         * @access public
         * @return string
         */
        public function get_label() {
            return __('Save', 'dynamic-content-for-elementor');
        }

        public function init($param = null) {
            //add_action( 'elementor_pro/forms/new_record', 'dce_elementor_form_save', 10, 10 );

            /* add_action( 'elementor_pro/init', function() {
              // Instantiate the action class
              $dce_form_action = new DCE_Extension_Form();
              // Register the action with form widget
              \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $dce_form_action->get_name(), $dce_form_action );
              }); */
        }

        /**
         * Register Settings Section
         *
         * Registers the Action controls
         *
         * @access public
         * @param \Elementor\Widget_Base $widget
         */
        public function register_settings_section($widget) {

            $roles = DCE_Helper::get_roles(false);
            $post_types = DCE_Helper::get_post_types();
            $taxonomies = DCE_Helper::get_taxonomies();

            $widget->start_controls_section(
                    'section_dce_form_save',
                    [
                        'label' => $this->get_label(), //__('DCE', 'dynamic-content-for-elementor'),
                        'condition' => [
                            'submit_actions' => $this->get_name(),
                        ],
                    ]
            );

            $widget->add_control(
                    'dce_form_save_type',
                    [
                        'label' => __('Save fields as', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::CHOOSE,
                        'options' => [
                            'post' => [
                                'title' => __('Post', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-file-text-o',
                            ],
                            'user' => [
                                'title' => __('User', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-user',
                            ],
                            'term' => [
                                'title' => __('Term', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-tag',
                            ],
                        ],
                        'default' => 'post',
                        'toggle' => false,
                        'label_block' => 'true',
                    ]
            );

            $widget->add_control(
                    'dce_form_save_type_post_title', [
                'label' => __('Post Title', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => 'Form Entry by [field id="name"]',
                'description' => __('Can use static text, field Shortcode, Token or mixed', 'dynamic-content-for-elementor') . '<br>' . __('or leave empty for rand value', 'dynamic-content-for-elementor'),
                'condition' => [
                    'dce_form_save_type' => 'post',
                ],
                    ]
            );
            $widget->add_control(
                    'dce_form_save_type_post_content', [
                'label' => __('Post Content', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '[field id="message"]',
                'description' => __('Can use static text, field Shortcode, Token or mixed', 'dynamic-content-for-elementor'),
                'condition' => [
                    'dce_form_save_type' => 'post',
                ],
                    ]
            );
            $widget->add_control(
                    'dce_form_save_type_post_type', [
                'label' => __('Post Type', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'options' => $post_types + array('dce_elementor_from' => __('Default', 'dynamic-content-for-elementor')),
                'default' => 'dce_elementor_from',
                'condition' => [
                    'dce_form_save_type' => 'post',
                ],
                    ]
            );
            $widget->add_control(
                    'dce_form_save_type_post_status', [
                'label' => __('Post Status', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'publish' => [
                        'title' => __('Publish', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-file-text-o',
                    ],
                    'draft' => [
                        'title' => __('Draft', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-file-text',
                    ],
                ],
                'default' => 'publish',
                'toggle' => false,
                'label_block' => 'true',
                'condition' => [
                    'dce_form_save_type' => 'post',
                ],
                    ]
            );

            $widget->add_control(
                    'dce_form_save_type_user_username', [
                'label' => __('UserName Field', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                //'default' => 'user_'.time(),
                'description' => __('Use field Shortcode for UserName', 'dynamic-content-for-elementor') . '<br>' . __('or leave empty for rand value', 'dynamic-content-for-elementor'),
                'condition' => [
                    'dce_form_save_type' => 'user',
                ],
                    ]
            );
            $widget->add_control(
                    'dce_form_save_type_user_email', [
                'label' => __('User Email Field', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '[field id="email"]',
                'description' => __('Use field Shortcode for Email', 'dynamic-content-for-elementor'),
                'condition' => [
                    'dce_form_save_type' => 'user',
                ],
                    ]
            );
            $widget->add_control(
                    'dce_form_save_type_user_role', [
                'label' => __('User Role', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'options' => $roles,
                'default' => 'subscriber',
                'condition' => [
                    'dce_form_save_type' => 'user',
                ],
                    ]
            );

            $widget->add_control(
                    'dce_form_save_type_term_name', [
                'label' => __('Term Name', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => 'Term [field id="name"]',
                'description' => __('Can use static text, field Shortcode, Token or mixed', 'dynamic-content-for-elementor') . '<br>' . __('or leave empty for rand value', 'dynamic-content-for-elementor'),
                'condition' => [
                    'dce_form_save_type' => 'term',
                ],
                    ]
            );
            $widget->add_control(
                    'dce_form_save_type_term_description', [
                'label' => __('Term Description', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '[field id="message"]',
                'description' => __('Can use static text, field Shortcode, Token or mixed', 'dynamic-content-for-elementor'),
                'condition' => [
                    'dce_form_save_type' => 'term',
                ],
                    ]
            );
            $widget->add_control(
                    'dce_form_save_type_term_taxonomy', [
                'label' => __('Term Taxonomy', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'options' => $taxonomies,
                'default' => 'category',
                'condition' => [
                    'dce_form_save_type' => 'term',
                ],
                    ]
            );

            $widget->end_controls_section();
        }

        /**
         * Run
         *
         * Runs the action after submit
         *
         * @access public
         * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
         * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
         */
        public function run($record, $ajax_handler) {
            $settings = $record->get('form_settings');

            $fields = DCE_Helper::get_form_data($record);
            $settings = DCE_Helper::get_dynamic_value($settings, $fields);

            $this->dce_elementor_form_save($fields, $settings, $ajax_handler);
            
        }

        /**
         * On Export
         *
         * Clears form settings on export
         * @access Public
         * @param array $element
         */
        public function on_export($element) {
            $tmp = array();
            if (!empty($element)) {
                foreach ($element as $key => $value) {
                    if (substr($key, 0, 4) == 'dce_') {
                        $element[$key];
                    }
                }
            }
        }

        function dce_elementor_form_save($record, $settings = null, $ajax_handler = null) {

            $fields = array();
            if (is_object($record)) {
                // from add action
                $data = $record->get_formatted_data(true);
                foreach ($data as $label => $value) {
                    $fields[$label] = sanitize_text_field($value);
                }
            } else {
                // from form extension
                $fields = $record;
                /* $form_record = new \ElementorPro\Modules\Forms\Classes\Form_Record();
                  $record = $form_record; */
            }

            if (is_object($record)) {
                $fields['form_name'] = $record->get_form_settings('form_name');
            } else {
                $fields['form_name'] = $settings['form_name'];
            }

            // Insert the post into the database
            // https://developer.wordpress.org/reference/functions/wp_insert_post/
            // https://developer.wordpress.org/reference/functions/wp_insert_user/
            // https://developer.wordpress.org/reference/functions/wp_insert_term/

            switch ($settings['dce_form_save_type']) {
                case 'post':
                    $settings['dce_form_save_type_post_title'] = DCE_Helper::get_dynamic_value($settings['dce_form_save_type_post_title'], $fields);
                    $settings['dce_form_save_type_post_content'] = DCE_Helper::get_dynamic_value($settings['dce_form_save_type_post_content'], $fields);
                    $db_ins = array(
                        'post_title' => $settings['dce_form_save_type_post_title'],
                        'post_status' => $settings['dce_form_save_type_post_status'],
                        'post_type' => $settings['dce_form_save_type_post_type'],
                        'post_content' => $settings['dce_form_save_type_post_content'],
                    );
                    $obj_id = wp_insert_post($db_ins);
                    break;
                case 'user':
                    $settings['dce_form_save_type_user_username'] = sanitize_title(DCE_Helper::get_dynamic_value($settings['dce_form_save_type_user_username'], $fields));
                    if (!$settings['dce_form_save_type_user_username']) {
                        $settings['dce_form_save_type_user_username'] = 'user_' . time();
                    }
                    $settings['dce_form_save_type_user_email'] = DCE_Helper::get_dynamic_value($settings['dce_form_save_type_user_email'], $fields);

                    $user_email_exist = get_user_by('email', $settings['dce_form_save_type_user_email']);
                    $user_login_exist = get_user_by('login', $settings['dce_form_save_type_user_username']);
                    if ($user_email_exist || $user_login_exist) {
                        $ajax_handler->add_error_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SUBSCRIBER_ALREADY_EXISTS, $settings));
                        return false;
                    }

                    $db_ins = array(
                        'user_login' => $settings['dce_form_save_type_user_username'],
                        'user_email' => $settings['dce_form_save_type_user_email'],
                        'role' => $settings['dce_form_save_type_user_role'],
                    );
                    $obj_id = wp_insert_user($db_ins);
                    break;
                case 'term':
                    $db_ins = array(
                        'description' => $settings['dce_form_save_type_term_description'],
                    );
                    $settings['dce_form_save_type_term_name'] = DCE_Helper::get_dynamic_value($settings['dce_form_save_type_term_name'], $fields);
                    if ($settings['dce_form_save_type_term_name']) {
                        $settings['dce_form_save_type_term_name'] = 'Term ' . time();
                    }
                    $obj_id = wp_insert_term($settings['dce_form_save_type_term_name'], $settings['dce_form_save_type_term_taxonomy'], $db_ins);
                    break;
            }

            if ($obj_id) {
                if (!empty($fields) && is_array($fields)) {
                    foreach ($fields as $akey => $adata) {
                        //update_post_meta( $obj_id, $akey, $adata );
                        $meta_upd = 'update_' . $settings['dce_form_save_type'] . '_meta';
                        call_user_func($meta_upd, $obj_id, $akey, $adata);
                    }
                }
            } else {
                $ajax_handler->add_error_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SERVER_ERROR, $settings));
            }
        }

    }

}