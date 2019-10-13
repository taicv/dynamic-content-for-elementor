<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;

use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Template
 *
 * Elementor widget for Dinamic Content Elements
 *
 */

class DCE_Widget_Template extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-template';
    }
    
    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('Dynamic Template', DCE_TEXTDOMAIN);
    }

    public function get_icon() {
        return 'icon-dyn-template';
    }

    /**
     * A list of scripts that the widgets is depended in
     * @since 1.3.0
     * */
    public function get_script_depends() {
        return [ ];
    }
    static public function get_position() {
        return 4;
    }
    protected function _register_controls() {
        $this->start_controls_section(
                'section_dynamictemplate', [
                'label' => __('Template', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
          'dynamic_template', [
            'label' => __('Select Template', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT2,
            'label_block' => true,
            //'options' => get_post_taxonomies( $post->ID ),
            'options' => DCE_Helper::get_all_template(),
            'default' => ''
          ]
        );
        $this->add_control(
            'data_source',
            [
              'label' => __( 'Source', DCE_TEXTDOMAIN ),
              'description' => __( 'Select the data source', DCE_TEXTDOMAIN ),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __( 'Same', DCE_TEXTDOMAIN ),
              'label_off' => __( 'other', DCE_TEXTDOMAIN ),
              'return_value' => 'yes',
              'separator' => 'before'
            ]
        );
        $this->add_control(
            'other_post_source', [
              'label' => __('Select source from other post', DCE_TEXTDOMAIN),
              'type' => Controls_Manager::SELECT,
              
              'groups' => DCE_Helper::get_all_posts(get_the_ID(),true),
              'default' => '',
              'condition' => [
                'data_source' => '',
              ], 
            ]
        );
        $this->end_controls_section();


       
    }

    protected function render() {
      $settings = $this->get_active_settings();
        if ( empty( $settings ) )
            return;
        //
        // ------------------------------------------
        $id_page = ''; //get_the_ID();
        $type_page = '';
        //
        if( $settings['data_source'] != 'yes' ){
            global $global_ID;
            global $global_TYPE;

            $original_global_ID = $global_ID;
            
            $id_page = $settings['other_post_source'];
            $type_page = get_post_type($id_page);

            $global_ID = $id_page;
            $global_TYPE = $type_page;
        }


        $dce_default_template = $settings[ 'dynamic_template' ];
        //echo $dce_default_template; 
        ?>
        <div class="dce-template">
          <?php
          if (!empty($dce_default_template)) {
              include DCE_PATH . 'template/template.php';
              echo $pagina_temlate;

              
          }

          ?>
        </div>
        <?php
        if( $settings['data_source'] != 'yes' ){
            $global_ID = $original_global_ID;
        }
    }

}
