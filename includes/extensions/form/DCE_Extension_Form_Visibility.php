<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\DCE_Tokens;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

function _dce_extension_form_visibility($field) {
    switch ($field) {
        case 'enabled':
            return true;
        case 'docs':
            return 'https://www.dynamic.ooo/widget/conditional-fields-for-elementor-pro-form/';
        case 'description' :
            return __('Add Field Logic Condition to Elementor PRO Form', 'dynamic-content-for-elementor');
    }
}

if (!DCE_Helper::is_plugin_active('elementor-pro')) {

    class DCE_Extension_Form_Visibility extends DCE_Extension_Prototype {

        public $name = 'Form Field Condition';
        private $is_common = false;
        public static $depended_plugins = ['elementor-pro'];

        static public function is_enabled() {
            return _dce_extension_form_visibility('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_visibility('description');
        }

        public function get_docs() {
            return _dce_extension_form_visibility('docs');
        }

    }

} else {

	// TODO: remove php check validation for hidden fields
	
    class DCE_Extension_Form_Visibility extends DCE_Extension_Prototype {

        public $name = 'Form Field Condition';
        public static $depended_plugins = ['elementor-pro'];
        public static $docs = 'https://www.dynamic.ooo/widget/conditional-fields-for-elementor-pro-form/';
        private $is_common = false;

        static public function is_enabled() {
            return _dce_extension_form_visibility('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_visibility('description');
        }

        public function get_docs() {
            return _dce_extension_form_visibility('docs');
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
            return 'dce_form_visibility';
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
            return __('Field Condition', 'dynamic-content-for-elementor');
        }

        /**
         * Add Actions
         *
         * @since 0.5.5
         *
         * @access private
         */
        protected function add_actions() {
            add_action("elementor/widget/render_content", array($this, '_render_form'), 10, 2);
            
            add_action( 'elementor_pro/forms/validation', array($this, '_validate_form'), 10, 2 );
        }
        
        public function _validate_form( $record, $ajax_handler ) {
            
            
            // reset form validation
            $ajax_handler->errors = [];
            $ajax_handler->messages['errors'] = [];
            $ajax_handler->set_success( true );
            
            $settings = $record->get('form_settings');
            $field_settings = array();
            foreach($settings['form_fields'] as $afield) {
                $field_settings[$afield['custom_id']] = $afield;
            }
            foreach ( $record->get('fields') as $id => $field ) {
                    $field_type = $field['type'];
                    if ( ! empty( $field['required'] ) && '' === $field['value'] && 'upload' !== $field_type ) {
                        // ADD CONDITIONAL VERIFICATION
                        if (empty($field_settings[$id]['dce_field_visibility_mode']) || $field_settings[$id]['dce_field_visibility_mode'] == 'visible') { 
                            // ADD CONDITIONAL VERIFICATION
                            $ajax_handler->add_error( $id, \ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::FIELD_REQUIRED, $settings ) );
                        }
                    }

                    /**
                     * Elementor form field validation.
                     *
                     * Fires when a single form field is being validated.
                     *
                     * It allows developers to validate individual field types.
                     *
                     * The dynamic portion of the hook name, `$field_type`, refers to the field type.
                     *
                     * @since 2.0.0
                     *
                     * @param array        $field        Form field.
                     * @param Form_Record  $this         An instance of the form record.
                     * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
                     */
                    do_action( "elementor_pro/forms/validation/{$field_type}", $field, $record, $ajax_handler );
            }
        }

        public function _render_form($content, $widget) {
            $js = '';

            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                if ($widget->get_name() == 'form') {
                    $settings = $widget->get_settings_for_display();
                    //var_dump($settings['form_fields']); die();
                    ob_start();
                    // add custom js
                    ?>
                    <script>
                        jQuery(document).ready(function(){
                        <?php
                        if (!empty($settings['form_fields'])) {
                            $conditions = array();
                            
                            // BUTTON
                            if ($settings['dce_field_visibility_mode'] != 'visible') {
                                if (empty($settings['button_css_id'])) {
                                    $settings['button_css_id'] = 'form-btn-submit-'.$widget->get_id();
                                    $content = str_replace('type="submit"', 'type="submit" id="'.$settings['button_css_id'].'"', $content);
                                }
                                $conditions[$settings['button_css_id']] = array(
                                    'dce_field_visibility_mode' => $settings['dce_field_visibility_mode'],
                                    'dce_field_visibility_operator' => $settings['dce_field_visibility_operator'],
                                    'dce_field_visibility_field' => $settings['dce_field_visibility_field'],
                                    'dce_field_visibility_value' => $settings['dce_field_visibility_value'],
                                    'dce_field_visibility_render' => $settings['dce_field_visibility_render'],
                                    'element_css_id' => $settings['button_css_id'],
                                    'element_css_group' => '.elementor-element-'.$widget->get_id().' .elementor-field-group.elementor-field-type-submit',
                                );
                            }
                            
                            // FIELDS
                            foreach ($settings['form_fields'] as $key => $afield) {
                                $afield['element_css_id'] = 'form-field-'.$afield['custom_id'];
                                $afield['element_css_group'] = '.elementor-field-group-'.$afield['custom_id'];
                                $conditions[$afield['custom_id']] = $afield;
                            }
                            
                            foreach ($conditions as $key => $afield) {
                                if ($afield['dce_field_visibility_mode'] != 'visible') {
                                    $field_change_selector = '#form-field-'.$afield['dce_field_visibility_field'];
                                    if (isset($conditions[$afield['dce_field_visibility_field']]['field_type'])) {
                                        $field_type = $conditions[$afield['dce_field_visibility_field']]['field_type'];
                                        switch($field_type) {
                                            case 'radio':
                                            case 'checkbox':
                                                $field_change_selector = '.elementor-field-type-'.$field_type.'.elementor-field-group-'.$afield['dce_field_visibility_field'].' input[type='.$field_type.']';
                                                if ($field_type == 'checkbox' && $afield['dce_field_visibility_value']) {
                                                    $field_change_selector .= '[value="'.$afield['dce_field_visibility_value'].'"]';
                                                }
                                                break;
                                            case 'acceptance':
                                                $field_change_selector = '.elementor-field-type-'.$field_type.'.elementor-field-group-'.$afield['dce_field_visibility_field'].' input[type=checkbox]';
                                                break;
                                        }
                                    }
                                    $element_css_id = '#'.$afield['element_css_id'];
                                    if (isset($afield['field_type'])) {
                                        $field_type = $afield['field_type'];
                                        switch($field_type) {
                                            case 'radio':
                                            case 'checkbox':
                                                $element_css_id = '.elementor-field-type-'.$field_type.'.elementor-field-group-'.$afield['custom_id'].' input[type='.$field_type.']';
                                                break;
                                            case 'acceptance':
                                                $element_css_id = '.elementor-field-type-'.$field_type.'.elementor-field-group-'.$afield['custom_id'].' input[type=checkbox]';
                                                break;
                                        }
                                    }
                                    
									$value = $afield['dce_field_visibility_value'];
									if (!is_numeric($afield['dce_field_visibility_value'])) {
										$value = "'".$value."'";
									}
                                    switch ($afield['dce_field_visibility_operator']) {
                                        case 'empty':
                                            $display_condition = "jQuery(this).val() == ''";
                                            break;
                                        case 'not_empty':
                                            $display_condition = "jQuery(this).val() != ''";
                                            break;
                                        case 'gt':
                                            $display_condition = "jQuery(this).val() > " . $value;
                                            break;
										case 'ge':
                                            $display_condition = "jQuery(this).val() >= " . $value;
                                            break;
										case 'lt':
											$display_condition = "jQuery(this).val() < " . $value;
                                            break;	
                                        case 'le':
											$display_condition = "jQuery(this).val() <= " . $value;
                                            break;
                                        case 'equal_to':
                                            $display_condition = "jQuery(this).val() == '" . $afield['dce_field_visibility_value'] . "'";
                                            break;
                                        case 'not_equal':
                                            $display_condition = "jQuery(this).val() != '" . $afield['dce_field_visibility_value'] . "'";
                                            break;
                                        case 'contain':
                                            $display_condition = "jQuery(this).val().includes('" . $afield['dce_field_visibility_value'] . "') !== false && jQuery(this).val() != ''";
                                            break;
										case 'not_contain':
                                            $display_condition = "jQuery(this).val().includes('" . $afield['dce_field_visibility_value'] . "') === false && jQuery(this).val() != ''";
                                            break;
                                        case 'is_checked':
                                            $display_condition = "jQuery(this).prop('checked')";
                                            if ($afield['dce_field_visibility_value']) {
                                                $display_condition .= " && jQuery(this).val() == '" . $afield['dce_field_visibility_value'] . "'";
                                            }
                                            break;
                                    }

                                    $disabled_end = $afield['dce_field_visibility_mode'] == 'show' ? 'false' : 'true';
                                    $disabled_init = $afield['dce_field_visibility_mode'] == 'show' ? 'true' : 'false';
                                    $display_init = $afield['dce_field_visibility_mode'] == 'show' ? 'hide' : 'show';
                                    ?>
                                    jQuery('<?php echo $field_change_selector; ?>').on('change', function(){
                                        //alert(jQuery(this).attr('id'));
                                        //alert('<?php echo $element_css_id; ?>');
                                        //alert("<?php echo $display_condition; ?>");
                                        //alert(jQuery(this).val());
                                        if (<?php echo $display_condition; ?>) {
                                            <?php if (empty($afield['dce_field_visibility_render'])) { ?>jQuery('<?php echo $afield['element_css_group']; ?>').<?php echo $afield['dce_field_visibility_mode']; ?>();<?php } ?>
                                            //document.getElementById('<?php echo $element_css_id; ?>').disabled = <?php echo $disabled_end; ?>;
                                            jQuery('<?php echo $element_css_id; ?>').prop('disabled', <?php echo $disabled_end; ?>);
                                        } else {
                                            //alert('<?php echo $display_init; ?>');
                                            <?php if (empty($afield['dce_field_visibility_render'])) { ?>jQuery('<?php echo $afield['element_css_group']; ?>').<?php echo $display_init; ?>();<?php } ?>
                                            //document.getElementById('<?php echo $element_css_id; ?>').disabled = <?php echo $disabled_init; ?>;
                                            jQuery('<?php echo $element_css_id; ?>').prop('disabled', <?php echo $disabled_init; ?>);
                                        }
                                    });
                                    //alert('<?php echo $field_change_selector; ?>');
                                    jQuery('<?php echo $field_change_selector; ?>').change();
                                    <?php
                                }
                            }
                        }
                        ?>
                        //alert("It works");
                        });
                    </script>
                    <?php
                    $js = ob_get_contents();
                    ob_end_clean();
                }
            }

            return $content . $js;
        }

        public static function _add_form_fields_visibility(Controls_Stack $element, $control_id, $control_data, $options = []) {
            //echo 'adsa: '; var_dump($control_id); //die();
            if ($element->get_name() == 'form' && $control_id == 'form_fields') {
                //var_dump($element->get_name()); die();

                $control_data['fields']['form_fields_visibility_tab'] = array(
                    "type" => "tab",
                    "tab" => "visibility",
                    "label" => "Condition",
                    "tabs_wrapper" => "form_fields_tabs",
                    "name" => "form_fields_visibility_tab",
                );

                $control_data['fields']['dce_field_visibility_mode'] = array(
                    'label' => __('Condition', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'visible' => [
                            'title' => __('Always Visible', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-check-square-o',
                        ],
                        'show' => [
                            'title' => __('Show IF', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-eye',
                        ],
                        'hide' => [
                            'title' => __('Hide IF', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-eye-slash',
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'visible',
                    "tab" => "visibility",
                    "tabs_wrapper" => "form_fields_tabs",
                    "inner_tab" => "form_fields_visibility_tab",
                    "name" => "dce_field_visibility_mode",
                );

                $control_data['fields']['dce_field_visibility_field'] = array(
                    "type" => Controls_Manager::TEXT,
                    "tab" => "visibility",
                    "label" => __('Field ID', 'dynamic-content-for-elementor'),
                    "tabs_wrapper" => "form_fields_tabs",
                    "inner_tab" => "form_fields_visibility_tab",
                    "name" => "dce_field_visibility_field",
                    'condition' => array(
                        'dce_field_visibility_mode!' => 'visible',
                    )
                );

                $control_data['fields']['dce_field_visibility_operator'] = array(
                    'label' => __('Operator', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => array(
                        "empty" => "empty",
                        "not_empty" => "not empty",
                        "equal_to" => "equals to",
                        "not_equal" => "not equals",
                        "gt" => "greater than",
						"ge" => "greater than or equal",
                        "lt" => "less than",
						"le" => "less than or equal",
                        "contain" => "contains",
						"not_contain" => "not contains",
                        "is_checked" => "is checked",
                    ),
                    'default' => 'empty',
                    "tab" => "visibility",
                    "tabs_wrapper" => "form_fields_tabs",
                    "inner_tab" => "form_fields_visibility_tab",
                    "name" => "dce_field_visibility_operator",
                    'condition' => array(
                        'dce_field_visibility_mode!' => 'visible',
                    )
                );

                $control_data['fields']['dce_field_visibility_value'] = array(
                    "type" => Controls_Manager::TEXT,
                    "tab" => "visibility",
                    "label" => __('Value', 'dynamic-content-for-elementor'),
                    "tabs_wrapper" => "form_fields_tabs",
                    "inner_tab" => "form_fields_visibility_tab",
                    "name" => "dce_field_visibility_value",
                    'condition' => array(
                        'dce_field_visibility_mode!' => 'visible',
                        'dce_field_visibility_operator' => array('equal_to', 'not_equal', 'gt', 'lt', 'ge', 'le', 'not_contain', 'contain', 'is_checked'),
                    )
                );

                $control_data['fields']['dce_field_visibility_render'] = array(
                    'label' => __('Disable only', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    "tab" => "visibility",
                    "tabs_wrapper" => "form_fields_tabs",
                    "inner_tab" => "form_fields_visibility_tab",
                    "name" => "dce_field_visibility_render",
                    'condition' => array(
                        'dce_field_visibility_mode!' => 'visible',
                    )
                );
            }

            if ($element->get_name() == 'form' && $control_id == 'button_css_id') {
                //var_dump ($control_data); die();
                $element->add_control(
                        'dce_field_visibility_mode',
                        [
                            'label' => __('Display mode', 'dynamic-content-for-elementor'),
                            'type' => Controls_Manager::CHOOSE,
                            'options' => [
                                'visible' => [
                                    'title' => __('Always Visible', 'dynamic-content-for-elementor'),
                                    'icon' => 'fa fa-check-square-o',
                                ],
                                'show' => [
                                    'title' => __('Show IF', 'dynamic-content-for-elementor'),
                                    'icon' => 'fa fa-eye',
                                ],
                                'hide' => [
                                    'title' => __('Hide IF', 'dynamic-content-for-elementor'),
                                    'icon' => 'fa fa-eye-slash',
                                ]
                            ],
                            'toggle' => false,
                            'default' => 'visible',
                            'separator' => 'before',
                        ]
                );

                $element->add_control(
                        'dce_field_visibility_field',
                        [
                            "type" => Controls_Manager::TEXT,
                            "label" => __('Field ID', 'dynamic-content-for-elementor'),
                            'condition' => array(
                                'dce_field_visibility_mode!' => 'visible',
                            )
                        ]
                );

                $element->add_control(
                        'dce_field_visibility_operator',
                        [
                            'label' => __('Operator', 'dynamic-content-for-elementor'),
                            'type' => Controls_Manager::SELECT,
                            'options' => array(
								"empty" => "empty",
								"not_empty" => "not empty",
								"equal_to" => "equals to",
								"not_equal" => "not equals",
								"gt" => "greater than",
								"ge" => "greater than or equal",
								"lt" => "less than",
								"le" => "less than or equal",
								"contain" => "contains",
								"not_contain" => "not contains",
								"is_checked" => "is checked",
							),
                            'default' => 'empty',
                            'condition' => array(
                                'dce_field_visibility_mode!' => 'visible',
                            )
                        ]
                );

                $element->add_control(
                        'dce_field_visibility_value',
                        [
                            "type" => Controls_Manager::TEXT,
                            "label" => __('Value', 'dynamic-content-for-elementor'),
                            'condition' => array(
                                'dce_field_visibility_mode!' => 'visible',
                                'dce_field_visibility_operator' => array('equal_to', 'not_equal', 'gt', 'lt', 'ge', 'le', 'not_contain', 'contain', 'is_checked'),
                            )
                        ]
                );

                $element->add_control(
                        'dce_field_visibility_render',
                        [
                            'label' => __('Disable only', 'dynamic-content-for-elementor'),
                            'type' => Controls_Manager::SWITCHER,
                            'default' => 'yes',
                            'condition' => array(
                                'dce_field_visibility_mode!' => 'visible',
                            )
                        ]
                );
            }

            return $control_data;
        }

    }

}