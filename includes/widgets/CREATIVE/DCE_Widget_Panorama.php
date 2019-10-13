<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;

use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Panorama A-Frame
 *
 * Elementor widget for Elementor Dynamic Content
 *
 */

class DCE_Widget_Panorama extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-panorama';
    }
    static public function is_enabled() {
        return true;
    }
    public function get_title() {
        return __('Panorama', DCE_TEXTDOMAIN);
    }
    public function get_description() {
      return __('Display a spherical picture in 360 grades through VR mode', DCE_TEXTDOMAIN);
    }
    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/panorama/';
    }
    public function get_icon() {
        return 'icon-dyn-panorama';
    }
    public function get_script_depends() {
        return [ 'dce-aframe'];
    }
    static public function get_position() {
        return 3;
    }
    /*public function get_style_depends() {
        return [ 'dce-panorama' ];
    }*/
    protected function _register_controls() {
        $this->start_controls_section(
            'section_panorama', [
              'label' => __('Panorama', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
          'panorama_image',
          [
             'label' => __( 'Panorama Image', DCE_TEXTDOMAIN ),
             'type' => Controls_Manager::MEDIA,
             'default' => [
                'url' => DCE_Helper::get_placeholder_image_src(),
             ],
          ]
        );
        $this->add_responsive_control(
            'height_scene',
            [
                'label' => __( 'Scene height', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'rem', 'vh' ],
                'default' => [
                    'unit' => 'px',
                    'size' => 550
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} a-scene' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'template',
                'separator'   => 'after'
            ]
        );
         $this->add_control(
            'params_heading',
            [
                'label' => __( 'Parameters', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
         $this->add_control(
            'fullscreen_vr',
            [
                'label' => __( 'Fullscreen', DCE_TEXTDOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
                'label_off' => __( 'No', DCE_TEXTDOMAIN ),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
          'vr_mode_ui',
          [
              'label' => __( 'VR mode UI', DCE_TEXTDOMAIN ),
              'type' => Controls_Manager::SWITCHER,
              'default' => '',
              'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
              'label_off' => __( 'No', DCE_TEXTDOMAIN ),
              'return_value' => 'yes',
          ]
        );
        $this->add_control(
          'keyboard_shortcuts',
          [
              'label' => __( 'Keyboard Shortcuts', DCE_TEXTDOMAIN ),
              'type' => Controls_Manager::SWITCHER,
              'description' => __('Enables the shortcut to press "F" to enter VR.',DCE_TEXTDOMAIN),
              'default' => '',
              'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
              'label_off' => __( 'No', DCE_TEXTDOMAIN ),
              'return_value' => 'yes',
          ]
        );
        $this->add_control(
          'reversemousecontrol',
          [
              'label' => __( 'Reverse mouse control', DCE_TEXTDOMAIN ),
              'type' => Controls_Manager::SWITCHER,
              'default' => '',
              'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
              'label_off' => __( 'No', DCE_TEXTDOMAIN ),
              'return_value' => 'yes',
          ]
        );
        $this->end_controls_section();
    }

    protected function render() {
      $settings = $this->get_settings_for_display();
      if ( empty( $settings ) )
        return;
      $fullScreen = '';
      $keyboard = '';
      $vrmodeui = '';
      $reversemousecontrol = '';
      if(!$settings['fullscreen_vr']){
        $fullScreen = ' embedded';
      }
      if(!$settings['vr_mode_ui']){
        $vrmodeui = ' vr-mode-ui="enabled: false"';
      }
      if(!$settings['keyboard_shortcuts']){
        $keyboard = ' keyboard-shortcuts="enterVR: false"';
      }
      if($settings['reversemousecontrol']){
        $reversemousecontrol = '<a-camera mouse-cursor reverse-mouse-drag="true" id="cam" zoom="1.3"></a-camera>'; 
        //'<a-entity camera look-controls="reverseMouseDrag: true"></a-entity>';
      }
      // fog="type: exponential; color: #AAA"
      ?>
      <a-scene <?php echo $fullScreen.$keyboard.$vrmodeui; ?>>
        <?php echo $reversemousecontrol; ?>
        <a-sky src="<?php echo $settings['panorama_image']['url']; ?>" rotation="0 -130 0"></a-sky>
      </a-scene>
       <?php
    }

    protected function _content_template_() {
      ?>
      <a-scene embedded vr-mode-ui="enabled: false" keyboard-shortcuts="enterVR: false">
        <a-sky src="{{settings.panorama_image.url}}" rotation="0 -130 0"></a-sky>
      </a-scene>
      <?php
        
    }

}
