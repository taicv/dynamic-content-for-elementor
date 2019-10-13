<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Animated Text BETA
 *
 * Elementor widget for Dinamic Content Elements
 *
 */

class DCE_Widget_AnimateText extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-animateText';
    }

    static public function is_enabled() {
        return false;
    }

    public function get_title() {
        return __('AnimateText', DCE_TEXTDOMAIN);
    }

    public function get_icon() {
        return 'icon-dyn-animate_text';
    }
    static public function get_position() {
        return 2;
    }
    /**
     * A list of scripts that the widgets is depended in
     * @since 1.3.0
     * */
    public function get_script_depends() {
        return [ 'jquery', 'dce-anime-lib'];
    }
    
    /*public function get_style_depends() {
        return [ 'dce-parallax'];
    }*/

    protected function _register_controls() {
        $this->start_controls_section(
                'section_animateText', [
            'label' => __('AnimateText', DCE_TEXTDOMAIN),
                ]
        );
        $this->add_control(
      'animate_effect', [
          'label' => __('Effects', DCE_TEXTDOMAIN),
          'type' => Controls_Manager::SELECT,
          'options' => [
              '1' => __('Effect 1', DCE_TEXTDOMAIN),
              '2' => __('Effect 2', DCE_TEXTDOMAIN),
              '3' => __('Effect 3', DCE_TEXTDOMAIN),
              '4' => __('Effect 4', DCE_TEXTDOMAIN),
              '5' => __('Effect 5', DCE_TEXTDOMAIN),
              '6' => __('Effect 6', DCE_TEXTDOMAIN),
              '7' => __('Effect 7', DCE_TEXTDOMAIN),
              '8' => __('Effect 8', DCE_TEXTDOMAIN),
              '9' => __('Effect 9', DCE_TEXTDOMAIN),
              '10' => __('Effect 10', DCE_TEXTDOMAIN),
              '11' => __('Effect 11', DCE_TEXTDOMAIN),
              '12' => __('Effect 12', DCE_TEXTDOMAIN),
              '13' => __('Effect 13', DCE_TEXTDOMAIN),
              '14' => __('Effect 14', DCE_TEXTDOMAIN),
              '15' => __('Effect 15', DCE_TEXTDOMAIN),
              '16' => __('Effect 16', DCE_TEXTDOMAIN),
              
          ],
          'frontend_available' => true,
          'default' => '1',
          'separator' => 'after',
      ]
  );
        $repeater = new Repeater();

        $repeater->start_controls_tabs('tabs_repeater'); // start tabs ---------------------------------
        $repeater->start_controls_tab('tab_content', [ 'label' => __('Content', DCE_TEXTDOMAIN)]);
        //
        
        $repeater->add_control(
            'text_word', [
                'label' => __('Word', DCE_TEXTDOMAIN),
                'description' => __('Text before elemnet', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );
        
        $repeater->end_controls_tab();
        $repeater->start_controls_tab('tab_style', [ 'label' => __('Style', DCE_TEXTDOMAIN)]);       
        //
        
        $repeater->add_control(
            'color_item', [
                'label' => __('Text color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el, {{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el a' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'meta!' => ['attachments','articles','avatar']
                ],

            ]
        );
        
        $repeater->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'typography_item',
                'label' => 'Typography item',
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .dce-grid-users {{CURRENT_ITEM}}.tx-el',
                'condition' => [
                    'meta!' => ['attachments','avatar']
                ],
            ]
        );
        
        
        

        
        
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs(); // end tabs ----------------------------------------------------

        $this->add_control(
            'words', [
                'label' => __('Words', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'text_word' => 'Write a word',
                    ]
                    
                ],
                'frontend_available' => true,
                'fields' => array_values($repeater->get_controls()),
                'title_field' => '#',
                'title_field' => '{{{ text_word }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        echo '<div class="dce-animateText">';
        $effect = $settings['animate_effect'];
        if($effect == '1'){


          



          $words = $settings['words'];
          if (!empty($words)) {
              foreach ($words as $key => $w) :
                  //echo 'a ';
                  $text_word = $w['text_word'];
                  
                  ?>
                  <h1 class="ml1 w<?php echo $key+1; ?> dce-animate-word">
                  <span class="text-wrapper">
                    <span class="line line1"></span>
                    <span class="letters letters-<?php echo $key+1 ?>"><?php echo $text_word; ?></span>
                    <span class="line line2"></span>
                  </span>
                  </h1>
                  <?php


                  //$counter_item++;
              endforeach;
          }





          
        }else if($effect == '2'){
          $words = $settings['words'];
          if (!empty($words)) {
              foreach ($words as $key => $w) :
                  //echo 'a ';
                  $text_word = $w['text_word'];
                  
                  ?>
                 <h1 class="ml2 w<?php echo $key+1; ?> dce-animate-word">
                  <span class="letters letters-<?php echo $key+1 ?>"><?php echo $text_word; ?></span>
                </h1>
                <?php


                  //$counter_item++;
              endforeach;
          }
        }else if($effect == '3'){
          ?>
          <h1 class="ml3">Great Thinkers</h1>
          <?php
        }else if($effect == '4'){
          ?>
           <h1 class="ml4">
            <span class="letters letters-1">Ready</span>
            <span class="letters letters-2">Set</span>
            <span class="letters letters-3">Go!</span>
          </h1>
          <?php
        }else if($effect == '5'){
          ?>
            <h1 class="ml5">
              <span class="text-wrapper">
                <span class="line line1"></span>
                <span class="letters letters-left">Signal</span>
                <span class="letters ampersand">&amp;</span>
                <span class="letters letters-right">Noise</span>
                <span class="line line2"></span>
              </span>
            </h1>
          <?php
        }else if($effect == '6'){
          ?>
          <h1 class="ml6">
            <span class="text-wrapper">
              <span class="letters">Beautiful Questions</span>
            </span>
          </h1>
          <?php
        }else if($effect == '7'){
          ?>
          <h1 class="ml7">
            <span class="text-wrapper">
              <span class="letters">Reality is broken</span>
            </span>
          </h1>
          <?php
        }else if($effect == '8'){
          ?>
          <h1 class="ml8">
            <span class="letters-container">
                <span class="letters letters-left">Hi</span>
                <span class="letters bang">!</span>
            </span>
            <span class="circle circle-white"></span>
            <span class="circle circle-dark"></span>
            <span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span>
          </h1>
          <?php
        }else if($effect == '9'){
          ?>
          <h1 class="ml9">
            <span class="text-wrapper">
              <span class="letters">Coffee mornings</span>
            </span>
          </h1>
          <?php
        }else if($effect == '10'){
          ?>
          <h1 class="ml10">
            <span class="text-wrapper">
              <span class="letters">Domino Dreams</span>
            </span>
          </h1>
          <?php
        }else if($effect == '11'){
          ?>
          <h1 class="ml11">
            <span class="text-wrapper">
              <span class="line line1"></span>
              <span class="letters">Hello Goodbye</span>
            </span>
          </h1>
          <?php
        }else if($effect == '12'){
          ?>
          <h1 class="ml12">A new production</h1>
          <?php
        }else if($effect == '13'){
          ?>
          <h1 class="ml13">Rising Strong</h1>
          <?php
        }else if($effect == '14'){
          ?>
          <h1 class="ml14">
            <span class="text-wrapper">
              <span class="letters">Find Your Element</span>
              <span class="line"></span>
            </span>
          </h1>
          <?php
        }else if($effect == '15'){
          ?>
          <h1 class="ml15">
            <span class="word">Out</span>
            <span class="word">now</span>
          </h1>
          <?php
        }else if($effect == '16'){
          ?>
          <h1 class="ml16">Made with love</h1>
          <?php
        }
        echo '</div>';
    }

    protected function _content_template_() {
        
    }
}
