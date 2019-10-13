<?php
namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class DCE_DynamicTag_Token extends Tag {
	public function get_name() {
		return 'dce-token';
	}

	public function get_title() {
		return __( 'Token', 'dynamic-content-for-elementor' );
	}

	public function get_group() {
		return 'dce';
	}

	public function get_categories() {
		return [ 
                    'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY, 
                    'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
                    'number', //\Elementor\Modules\DynamicTags\Module::NUMBER_CATEGORY,
                ];
	}
        
        public function get_docs() {
		return 'https://www.dynamic.ooo/widget/tokens/';
	}
        
        /**
	* Register Controls
	*
	* Registers the Dynamic tag controls
	*
	* @since 2.0.0
	* @access protected
	*
	* @return void
	*/
	protected function _register_controls() {
		$this->add_control(
			'dce_token',
			[
				'label' => __( 'Token', 'dynamic-content-for-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => '[post:title], [post:meta_key], [user:display_name], [term:name], [wp_query:posts]'
			]
		);
                
                $this->add_control(
                    'dce_token_help', [
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="'.$this->get_docs().'" target="_blank">'.__( 'Need Help', 'elementor' ).' <i class="eicon-help-o"></i></a></div>',
                    'separator' => 'before',
                        ]
                );
	}

	public function render() {
            $settings = $this->get_settings_for_display(null, true);
            if (empty($settings))
                return;
            
            echo \DynamicContentForElementor\DCE_Tokens::do_tokens($settings['dce_token']);
	}
}
