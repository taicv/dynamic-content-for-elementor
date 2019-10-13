<?php

namespace Elementor;

use \Elementor\Controls_Manager;

/**
 * Description of DCE_Controls_Manager
 *
 * @author fra
 */
class DCE_Controls_Manager extends Controls_Manager {
    /* public function __construct($controls_manager) {
      $this->_clone_controls_manager($controls_manager);
      } */

    // get init data from original control_manager
    public function _clone_controls_manager($controls_manager) {
        
        $controls = $controls_manager->get_controls();
        foreach ($controls as $key => $value) {
            $this->controls[$key] = $value;
        }

        $control_groups = $controls_manager->get_control_groups();
        foreach ($control_groups as $key => $value) {
            $this->control_groups[$key] = $value;
        }
        //$this->control_groups = $controls_manager->get_control_groups();
        //var_dump($this->control_groups); die();

        $this->stacks = $controls_manager->get_stacks();
        $this->tabs = $controls_manager::get_tabs();
    }

    public $excluded_extensions = array();

    public function set_excluded_extensions($extensions) {
        $this->excluded_extensions = $extensions;
    }

    /**
     * Add control to stack.
     *
     * This method adds a new control to the stack.
     *
     * @since 1.0.0
     * @access public
     *
     * @param Controls_Stack $element      Element stack.
     * @param string         $control_id   Control ID.
     * @param array          $control_data Control data.
     * @param array          $options      Optional. Control additional options.
     *                                     Default is an empty array.
     *
     * @return bool True if control added, False otherwise.
     */
    public function add_control_to_stack(Controls_Stack $element, $control_id, $control_data, $options = []) {

        $exc_ext_token = !isset($this->excluded_extensions['DCE_Extension_Tokens']);
        $exc_ext_form_visibility = !isset($this->excluded_extensions['DCE_Extension_Form_Visibility']);

        if ($exc_ext_form_visibility) {
            // add Visibility to Form Fields
            if ($element->get_name() == 'form') {
                if (\DynamicContentForElementor\DCE_Helper::is_plugin_active('elementor-pro')) {
                    $control_data = \DynamicContentForElementor\Extensions\DCE_Extension_Form_Visibility::_add_form_fields_visibility($element, $control_id, $control_data, $options);
                }
            }
        }

        if ($exc_ext_token) {
            //add Dynamic Tags to $control_data
            $control_data = \DynamicContentForElementor\Extensions\DCE_Extension_Tokens::_add_dynamic_tags($control_data);
        }

        return parent::add_control_to_stack($element, $control_id, $control_data, $options);
    }

    /**
     * Open new stack.
     *
     * This method adds a new stack to the control stacks list. It adds any
     * given stack to the current control instance.
     *
     * @since 1.0.0
     * @access public
     *
     * @param Controls_Stack $controls_stack Controls stack.
     */
    /* public function open_stack(Controls_Stack $controls_stack) {
      $stack_id = $controls_stack->get_unique_name();
      //echo $stack_id;
      $controls = [];
      if ($this->can_add_dynamic($controls_stack)) {
      $controls = [
      'dynamic' => [
      'active' => true,
      ]
      ];
      }
      $this->stacks[$stack_id] = [
      'tabs' => [],
      'controls' => $controls,
      ];
      } */

    /**
     * Get element stack.
     *
     * Retrieve a specific stack for the list of stacks from the current instance.
     *
     * @since 1.0.0
     * @access public
     *
     * @param Controls_Stack $controls_stack  Controls stack.
     *
     * @return null|array Stack data if it exist, `null` otherwise.
     */
    /* public function get_element_stack(Controls_Stack $controls_stack) {
      $stack_id = $controls_stack->get_unique_name();
      //var_dump($stack_id);
      //$controls = $controls_stack->get_stack( false )['controls'];

      if (!isset($this->stacks[$stack_id])) {
      return null;
      }
      $stacks = $this->stacks[$stack_id];

      //if ($this->can_add_dynamic($controls_stack)) {
      if (substr($stack_id, 0, 10) != 'condition_') {
      $stacks = parent::get_element_stack($controls_stack);
      if (!empty($stacks) && isset($stacks['controls'])) {
      foreach ($stacks['controls'] as $ckey => $acontrol) {
      $stacks['controls'][$ckey]['dynamic'] = array('active' => true); // force the setting to be Dynamic
      //$controls_stack->add_control($id, $args, array('overwrite' => true));
      }
      }
      }

      if ($stack_id == 'form') {
      //var_dump($controls_stack); die(); //
      }

      return $stacks;
      } */

    /* public function can_add_dynamic(Controls_Stack $controls_stack) {
      $add_dynamic = true;

      //var_dump(is_subclass_of($controls_stack, 'Tag'));
      $control_class = get_class($controls_stack);
      $pos = strpos($control_class, 'DynamicTags');
      if ($pos !== false) {
      //$add_dynamic = false;
      }

      $stack_id = $controls_stack->get_unique_name();
      if (substr($stack_id, 0, 10) != 'condition_') {
      $add_dynamic = false;
      }

      return $add_dynamic;
      } */
}
