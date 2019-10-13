<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Elementor;
 
use \Elementor\Controls_Manager;

/**
 * Description of DCE_Controls_Manager
 *
 * @author fra
 */
class DCE_Controls_Manager extends Controls_Manager {

    /**
    * Get control.
    *
    * Retrieve a specific control from the current controls instance.
    *
    * @since 1.0.0
    * @access public
    *
    * @param string $control_id Control ID.
    *
    * @return bool|Base_Control Control instance, or False otherwise.
    */
    public function get_control( $control_id ) {
        //var_dump($control_id);
        $controls = $this->get_controls();
        $control = parent::get_control($control_id);
        if ($control_id == 'text') {
            $control = new DCE_Control_Text();
        }
        //var_dump($control->get_default_settings());
        return $control;
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
    public function open_stack( Controls_Stack $controls_stack ) {
        $stack_id = $controls_stack->get_unique_name();

        $this->stacks[ $stack_id ] = [
                'tabs' => [],
                'controls' => [
                    'dynamic' => [
                        'active' => true,
                    ]
                ],
        ];
    }
        
    /**
    * Get stack.
    *
    * Retrieve the stack of controls.
    *
    * @since 1.9.2
    * @access public
    *
    * @return array Stack of controls.
    */
    /*public function get_stack() {
            $stack = Plugin::$instance->controls_manager->get_element_stack( $this );

            if ( null === $stack ) {
                    $this->init_controls();

                    return Plugin::$instance->controls_manager->get_element_stack( $this );
            }

            return $stack;
    }*/
    
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
    public function get_element_stack( Controls_Stack $controls_stack ) {
            $stack_id = $controls_stack->get_unique_name();
            //var_dump($stack_id);
            //$controls = $controls_stack->get_stack( false )['controls'];
            
            if ( ! isset( $this->stacks[ $stack_id ] ) ) {      
                return null;
            }
            //$stacks = $this->stacks[ $stack_id ];
            $stacks = parent::get_element_stack($controls_stack);
            //var_dump($controls_stack->get_id());
            if (!empty($stacks) && isset($stacks['controls'])) {
                foreach($stacks['controls'] as $ckey => $acontrol) {
                    $stacks['controls'][$ckey]['dynamic'] = array('active' => true);
                    //$controls_stack->add_control($id, $args, array('overwrite' => true));
                }
            }
            //var_dump($controls); die();
            
            if ($stack_id == 'alert') {
                //echo '<pre>';var_dump($stacks);echo '</pre>';
            }
            
            return $stacks;
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
	public function add_control_to_stack( Controls_Stack $element, $control_id, $control_data, $options = [] ) {
            /*if ($control_data['type'] == 'text') {
                //$dynamic_tags = $element->get_settings(\Elementor\Core\DynamicTags\Manager::DYNAMIC_SETTING_KEY);
                if (empty($dynamic_tags)) {
                    $dynamic_tags = array();
                }
                //var_dump($control_id); die();
                $dynamic_tags[$control_id] = 'token';
                //$element->set_settings(\Elementor\Core\DynamicTags\Manager::DYNAMIC_SETTING_KEY, $dynamic_tags);
            }*/

            return parent::add_control_to_stack($element, $control_id, $control_data, $options);
    }
}
