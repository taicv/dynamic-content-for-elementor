<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Threesixty-Slider
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */

class DCE_Widget_ThreesixtySlider extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-threesixtyslider';
    }  
    static public function is_enabled() {
        return true;
    }
    public function get_title() {
        return __('Threesixty 360', DCE_TEXTDOMAIN);
    }
    public function get_description() {
      return __('Generate a rotation through a series of images', DCE_TEXTDOMAIN);
    }
    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/threesixty-360/';
    }
    public function get_icon() {
        return 'icon-dyn-360';
    }
    public function get_script_depends() {
        return [ 'jquery', 'dce-threesixtyslider-lib', 'dce-threesixtyslider'];
    }
    public function get_style_depends() {
        return [ 'dce-threesixtySlider' ];
    }
    static public function get_position() {
        return 3;
    }
    protected function _register_controls() {
        $this->start_controls_section(
            'section_threesixtyslider', [
                'label' => __('ThreesixtySlider', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_responsive_control(
            'height', [
                'label' => __('Width', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 400,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => [ 'px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-threesixty img' => 'width: {{SIZE}}{{UNIT}};', //'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dce-threesixty' => 'padding-bottom: {{SIZE}}{{UNIT}};'
                ],
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'pathimages', [
                'label' => __('Path images', DCE_TEXTDOMAIN),
                'description' => __('The absolute path of the images for the construction of the 360. ( Ex: https://www.SITE.ext/images360/ )<br>The images in the folder must be called with the sequential number (ex: 1.png, or 1.svg or 1.jpg.So 2.jpg, 3.jpg, 4.jpg etc.)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'default' => plugins_url('/assets/lib/threesixty-slider/imagesCube/', DCE__FILE__),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
          'format_file',
          [
             'label'       => __( 'Format files', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::SELECT,
             'default' => 'svg',
             'options' => [
                'png'  => __( 'PNG', DCE_TEXTDOMAIN ),
                'jpg' => __( 'JPG', DCE_TEXTDOMAIN ),
                'svg' => __( 'SVG', DCE_TEXTDOMAIN ),
             ],
             'frontend_available' => true,
          ]
        );
        $this->add_control(
          'total_frame',
          [
             'label'   => __( 'Total frames', DCE_TEXTDOMAIN ),
             'description'=> 'Total no. of image you have for slider',
             'type'    => Controls_Manager::NUMBER,
             'default' => 36,
             'min'     => 5,
             'max'     => 180,
             'step'    => 1,
             'frontend_available' => true,
          ]
        );
        $this->add_control(
          'end_frame',
          [
             'label'   => __( 'End frame', DCE_TEXTDOMAIN ),
             'description'=> 'End frame for the auto spin animation',
             'type'    => Controls_Manager::NUMBER,
             'default' => 36,
             'min'     => 5,
             'max'     => 180,
             'step'    => 1,
             'frontend_available' => true,
          ]
        );
        /*$this->add_control(
          'current_frame',
          [
             'label'   => __( 'Current frame', DCE_TEXTDOMAIN ),
             'description'=> 'This the start frame for auto spin',
             'type'    => Controls_Manager::NUMBER,
             'default' => 1,
             'min'     => 1,
             'max'     => 180,
             'step'    => 1,
             'frontend_available' => true,
          ]
        );*/
        $this->add_control(
            'navigation', [
                'label' => __('Navigation', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'responsive', [
                'label' => __('Responsive', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        global $global_ID;
        //$path = $settings('pathimages'); //$this->countFolder();
        //$path = get_home_path().'/assets/lib/threesixty-slider/imagestest360/';
        //echo "Path: ".$path; // Return "Path: /var/www/htdocs/"
        //echo plugins_url('/assets/lib/threesixty-slider/imagestest360/', DCE__FILE__);
        echo '<div class="threesixty dce-threesixty car">';
        echo '  <div class="spinner">';
        echo '      <span>0%</span>';
        echo '  </div>';
        echo '  <ol class="threesixty_images"></ol>';
        echo '</div>';
    }
    protected function countFolder($dir) {
     return (count(scandir($dir)) - 2);
    }

}