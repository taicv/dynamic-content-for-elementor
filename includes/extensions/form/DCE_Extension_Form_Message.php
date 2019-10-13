<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\DCE_Tokens;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

function _dce_extension_form_message($field) {
    switch ($field) {
        case 'enabled':
            return false;
        case 'docs':
            return 'https://www.dynamic.ooo/widget/message-generator-for-elementor-pro-form/';
        case 'description' :
            return __('Add custom Form Message to Elementor PRO Form', 'dynamic-content-for-elementor');
    }
}

if (!DCE_Helper::is_plugin_active('elementor-pro')) {

    class DCE_Extension_Form_Message extends DCE_Extension_Prototype {

        public $name = 'Form Message';
        private $is_common = false;
        public static $depended_plugins = ['elementor-pro'];

        static public function is_enabled() {
            return _dce_extension_form_message('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_message('description');
        }

        public function get_docs() {
            return _dce_extension_form_message('docs');
        }

    }

} else {

    class DCE_Extension_Form_Message extends \ElementorPro\Modules\Forms\Classes\Action_Base {

        public $name = 'Form Message';
        public static $depended_plugins = ['elementor-pro'];
        public static $docs = 'https://www.dynamic.ooo/widget/message-generator-for-elementor-pro-form/';

        static public function is_enabled() {
            return _dce_extension_form_message('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_message('description');
        }

        public function get_docs() {
            return _dce_extension_form_message('docs');
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
            return 'dce_form_message';
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
            return __('Message', 'dynamic-content-for-elementor');
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

            $widget->start_controls_section(
                    'section_dce_form_message',
                    [
                        'label' => $this->get_label(), //__('DCE', 'dynamic-content-for-elementor'),
                        'condition' => [
                            'submit_actions' => $this->get_name(),
                        ],
                    ]
            );

            $widget->add_control(
                    'dce_form_message_type', [
                'label' => __('Message type', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'text' => [
                        'title' => __('Text', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'template' => [
                        'title' => __('Template', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-th-large',
                    ]
                ],
                'toggle' => false,
                'default' => 'text',
                    ]
            );

            $widget->add_control(
                    'dce_form_message_text', [
                'label' => __('Name', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => '[date|U]',
                'description' => __('The PDF file name, the .pdf extension will automatically added', 'dynamic-content-for-elementor'),
                'label_block' => true,
                'condition' => [
                    'dce_form_message_type' => 'text',
                ],
                    ]
            );

            $widget->add_control(
                    'dce_form_message_template',
                    [
                        'label' => __('Template', 'dynamic-content-for-elementor'),
                        'type' => 'ooo_query',
                        'placeholder' => __('Template Name', 'dynamic-content-for-elementor'),
                        'label_block' => true,
                        'query_type' => 'posts',
                        'object_type' => 'elementor_library',
                        'description' => 'Use a Elementor Template as body fo this Email.',
                        'condition' => [
                            'dce_form_message_type' => 'template',
                        ],
                    ]
            );

            $widget->add_control(
                    'dce_form_message_post',
                    [
                        'label' => __('Post', 'dynamic-content-for-elementor'),
                        'type' => 'ooo_query',
                        'placeholder' => __('Select a Post', 'dynamic-content-for-elementor'),
                        'label_block' => true,
                        'query_type' => 'posts',
                        'description' => __('Force a Post as Template content for Dynamic fields. Leave empty for use current Page.', 'dynamic-content-for-elementor'),
                        'condition' => [
                            'dce_form_message_type' => 'template',
                        ],
                    ]
            );
            $widget->add_control(
                    'dce_form_message_user',
                    [
                        'label' => __('User', 'dynamic-content-for-elementor'),
                        'type' => 'ooo_query',
                        'placeholder' => __('Select a User', 'dynamic-content-for-elementor'),
                        'label_block' => true,
                        'query_type' => 'users',
                        'description' => __('Force a User as Template content for Dynamic fields. Leave empty for use current User.', 'dynamic-content-for-elementor'),
                        'condition' => [
                            'dce_form_message_type' => 'template',
                        ],
                    ]
            );

            $widget->add_control(
                    'dce_form_message_hide', [
                'label' => __('Hide Form after submit', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                    ]
            );

            $widget->add_control(
                    'dce_form_message_help', [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . __('Need Help', 'elementor') . ' <i class="eicon-help-o"></i></a></div>',
                'separator' => 'before',
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

            $this->dce_elementor_form_message($fields, $settings, $ajax_handler);
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

        function dce_elementor_form_message($fields, $settings = null, $ajax_handler = null) {
            
            $message_html = '';
            if ($settings['dce_form_message_type'] == 'template') {
                if (!empty($settings['dce_form_message_template'])) {
                    $dce_short = '[dce-elementor-template id="' . $settings['dce_form_message_template'] . '"';
                    if (!empty($settings['dce_form_message_post'])) {
                        $dce_short .= ' post_id="' . $settings['dce_form_message_post'] . '"]';
                    }
                    if (!empty($settings['dce_form_message_user'])) {
                        $dce_short .= ' author_id="' . $settings['dce_form_message_user'] . '"]';
                    }
                    $dce_short .= ' inlinecss="true"';
                    $dce_short .= ']';

                    $message_html = do_shortcode($dce_short);
                    $message_html = '</div><div class="elementor-message-dce" role="alert">'.$message_html;
                    $message_html .= '<style>.elementor-form .elementor-message {display: none !important;}</style>';
                }
            } else {
                $message_html = $settings['dce_form_message_text'];
            }
            
            if ($settings['dce_form_message_hide']) {
                $message_html .= '<style>.elementor-form-fields-wrapper {display: none !important;}</style>';
            }
            
            $message_html = DCE_Helper::get_dynamic_value($message_html, $fields);
            
            //$ajax_handler->add_success_message($message_html);
            //$ajax_handler->messages['success'] = array($message_html);
            //$ajax_handler->is_success = true;
            if ( $ajax_handler->is_success ) {
                wp_send_json_success( [
                    'message' => $message_html,
                    'data' => $ajax_handler->data,
                ] );
                die();
            }
            $ajax_handler->add_error_message($message_html);  
        }

    }

}