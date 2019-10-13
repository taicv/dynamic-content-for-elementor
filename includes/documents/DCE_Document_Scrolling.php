<?php

namespace DynamicContentForElementor\Documents;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;

use DynamicContentForElementor\DCE_Helper;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *
 * Inertia Scroll Document
 *
 */
class DCE_Document_Scrolling extends DCE_Document_Prototype {
    
    public $name = "Scrolling";
        
       
	protected $is_common = true;
   
	public function get_script_depends() {
		return [
			'dce-tweenMax-lib', 'inertiaScroll','scrollify','dce-lax-lib','dce-scrolling'
		];
	}
	public static function get_description() {
		return __( 'Scrolling settings.' );
	}
	protected function add_common_sections_actions() {


		// Activate sections for document
		add_action( 'elementor/documents/register_controls', function( $element ) {

			$this->add_common_sections( $element );

		}, 10, 2 );

		// Activate sections for widgets
		/*add_action( 'elementor/element/common/_section_style/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );*/

		// Activate sections for columns
		/*add_action( 'elementor/element/column/section_advanced/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );*/

		// Activate sections for sections
		/*add_action( 'elementor/element/section/section_advanced/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );*/

	}

	private function add_controls( $document, $args ) {

		$element_type = $document->get_type();

		//
	    // ------------------------------------------
	    $dce_data = DCE_Helper::dce_dynamic_data();
	    // ------------------------------------------
	    $id_page = $dce_data['id'];

	    $global_is = $dce_data['is'];
	    $type_page = $dce_data['type'];


		 // se volessi filtrare i campi in base al tipo
		/*if ( $document->get_name() === 'section' ) {

		}*/

		/* ----------------------------------- */
		/*$document->start_controls_section(
        'my_custom_section',
        [
          'label' => __( 'My Custom Section', 'my-domain' ),
          'tab' => Controls_Manager::TAB_SETTINGS
        ]
      );*/
      $document->add_control(
        'enable_dceScrolling',
        [
			'label' => __( 'Scrolling settings', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
           'render_type' => 'template',
            'frontend_available' => true,
		]
      );
	  $document->add_control(
        'scroll_info',
        [
			'label' => __( 'Settings Scroll', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'default' => '',
           
          	'raw' 				=> __( '<div>Scrolling management compromises various elements of the page (not just Elementor). In order to function correctly and obtain the transformations, it is necessary to indicate the css selectors of the theme used.<br><b><hr>By default we indicate the elements of the theme OceanWP.</b>', DCE_TEXTDOMAIN ),
			'content_classes' 	=> 'dce-document-settings',
			'separator' => 'after',
			'condition' => [
	                'enable_dceScrolling' => 'yes',
	            ],
		]
      );
      $document->add_control(
        'scroll_viewport',
        [
			'label' => __( 'Viewport', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::TEXT,
            'default' => '#outer-wrap',
           
            'frontend_available' => true,
            'condition' => [
	                'enable_dceScrolling' => 'yes',
	            ],
		]
      );
      $document->add_control(
        'scroll_contentScroll',
        [
			'label' => __( 'Content Scroll', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::TEXT,
            'default' => '#wrap',
           
            'frontend_available' => true,
            'condition' => [
	                'enable_dceScrolling' => 'yes',
	            ],
		]
      );
      
      /*$document->add_control(
        'scroll_target',
        [
			'label' => __( 'Target', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::TEXT,
            'default' => '#main',
           
            'frontend_available' => true,
            'condition' => [
	                'enable_dceScrolling' => 'yes',
	            ],
		]
      );*/
      $document->add_control(
        'scroll_opt_heading',
        [
			'label' => __( 'Settings Scroll', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'default' => '',
           
          	'raw' 				=> __( '<b>Scrolling Options:</b>', DCE_TEXTDOMAIN ),
			'content_classes' 	=> 'dce-document-settings',
			'condition' => [
	                'enable_dceScrolling' => 'yes',
	            ],
		]
      );
      
      $document->add_control(
			'scroll_id_page',
			[
				'label' => __( 'ID Page', DCE_TEXTDOMAIN ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => $id_page,
				'frontend_available' => true,
				'condition' => [
	                'enable_dceScrolling' => 'yes',
	            ],
			]
		);
     






      // ----------------------------------- EFFECTS --------------------------
		 $document->add_control(
        'enable_scrollEffects',
        [
			'label' => __( 'Effects', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            'render_type' => 'template',
            'frontend_available' => true,
            'separator' => 'before',
            'condition' => [
	                'enable_dceScrolling' => 'yes',
	                
	            ],
		]
      );
      /*$document->add_control(
		  'reload_scrollEffects_btn',
		  [
		     'type'    => Controls_Manager::RAW_HTML,
		     'raw' => '<div class="elementor-update-preview" style="background-color: transparent;margin: 0;">
			<div class="elementor-update-preview-title">'.__( 'Update changes to page', DCE_TEXTDOMAIN).'</div>
			<div class="elementor-update-preview-button-wrapper">
				<button class="elementor-update-preview-button elementor-button elementor-button-success">'. __( 'Apply', DCE_TEXTDOMAIN).'</button>
			</div>
		</div>',
			 'content_classes' => 'dce-btn-reload',
			 
			 'condition' => [
	                'enable_scrollEffects' => 'yes',
	            ],
		  ]
		);*/
       /*$document->add_control(
			'scrollEffects_id_page',
			[
				'label' => __( 'ID Page', DCE_TEXTDOMAIN ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => $id_page,
				'frontend_available' => true,
				'condition' => [
	                'enable_scrollEffects' => 'yes',
	            ],
			]
		);*/
       /*
      	linger 	n/a
		lazy 	100
		eager 	100
		lazy 	100
		slalom 	50
		crazy 	n/a
		spin 	360
		spinRev 	360
		spinIn 	360
		spinOut 	360
		blurInOut 	40
		blurIn 	40
		blurOut 	40
		fadeInOut 	n/a
		fadeIn 	n/a
		fadeOut 	n/a
		driftLeft 	100
		driftRight 	100
		leftToRight 	1
		rightToLeft 	1
		zoomInOut 	0.2
		zoomIn 	0.2
		zoomOut 	0.2
		swing 	30
		speedy 	30
	  */
      $document->add_control(
            'animation_effects', [
		        'label' => __('Animation Effects', DCE_TEXTDOMAIN),
		        'type' => Controls_Manager::SELECT2,
		        'multiple' => true,
		        'label_block' => true,
		        'options' => [
		            '' => __('None', DCE_TEXTDOMAIN),
		            'scaleDown' => __('Scale Down', DCE_TEXTDOMAIN),
		           
		           
		            // 'gallery' => __('Gallery', DCE_TEXTDOMAIN),
		            'opacity' => __('Opacity', DCE_TEXTDOMAIN),
		            'fixed' => __('Fixed', DCE_TEXTDOMAIN),
		            //'parallax' => __('Parallax', DCE_TEXTDOMAIN),
		            'rotation'	=> __('Rotation', DCE_TEXTDOMAIN),
		            //'linger' => __('Linger', DCE_TEXTDOMAIN),
		            'lazy' => __('Lazy', DCE_TEXTDOMAIN),
		            'eager' => __('Eger', DCE_TEXTDOMAIN),
		            'slalom' => __('Slalom', DCE_TEXTDOMAIN),
		            // 'crazy' => __('Crazy', DCE_TEXTDOMAIN),
		            
		            'spin' => __('Spin', DCE_TEXTDOMAIN),
		            'spinRev' => __('SpinRev', DCE_TEXTDOMAIN),
		            // 'spinIn' => __('SpinIn', DCE_TEXTDOMAIN),
		            // 'spinOut' => __('SpinOut', DCE_TEXTDOMAIN),
		            
		            // 'blurInOut' => __('BlurInOut', DCE_TEXTDOMAIN),
		            // 'blurIn' => __('BlurIn', DCE_TEXTDOMAIN),
		            // 'blurOut' => __('BlurOut', DCE_TEXTDOMAIN),

		            // 'fadeInOut' => __('FadeInOut', DCE_TEXTDOMAIN),
		            // 'fadeIn' => __('FadeIn', DCE_TEXTDOMAIN),
		            // 'fadeOut' => __('FadeOut', DCE_TEXTDOMAIN),
		            
		            'driftLeft' => __('DriftLeft', DCE_TEXTDOMAIN),
		            'driftRight' => __('DriftRight', DCE_TEXTDOMAIN),

		            'leftToRight' => __('LeftToRight', DCE_TEXTDOMAIN),
		            'rightToLeft' => __('RightToLeft', DCE_TEXTDOMAIN),

		            'zoomInOut' => __('ZoomInOut', DCE_TEXTDOMAIN),
		            'zoomIn' => __('ZoomIn', DCE_TEXTDOMAIN),
		            'zoomOut' => __('ZoomOut', DCE_TEXTDOMAIN),

		            'swing' => __('Swing', DCE_TEXTDOMAIN),
		            'speedy' => __('Speedy', DCE_TEXTDOMAIN),
		        ],
		        'default' => ['scaleDown'],
	            'frontend_available' => true,
	            'render_type' => 'template',
	            'condition' => [
	                'enable_scrollEffects' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                
	            ],
            ]
        );
      $document->add_control(
        'remove_first_scrollEffects',
        [
			'label' => __( 'Remove Effect on first row', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            'render_type' => 'template',
            'frontend_available' => true,
            'condition' => [
	                'enable_scrollEffects' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                
	            ],
		]
      );
        $document->add_control(
            'custom_class_section', [
		        'label' => __('Custom section CLASS', DCE_TEXTDOMAIN),
	            'type' => Controls_Manager::TEXT,
	            'default' => '',
	            'placeholder' => 'Write custom CLASS',
	            'frontend_available' => true,
	            'separator' => 'before',
	            'condition' => [
	                'enable_scrollEffects' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                
	            ],
            ]
        );







      // ----------------------------------- SNAP --------------------------
      $document->add_control(
        'enable_scrollify',
        [
			'label' => __( 'Snap', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            //'render_type' => 'template',
            'frontend_available' => true,
            'separator' => 'before',
            'condition' => [
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
		]
      );
      /*$document->add_control(
		  'reload_scrollify_btn',
		  [
		     'type'    => Controls_Manager::RAW_HTML,
		     'raw' => '<div class="elementor-update-preview" style="background-color: transparent;margin: 0;">
			<div class="elementor-update-preview-title">'.__( 'Update changes to page', DCE_TEXTDOMAIN).'</div>
			<div class="elementor-update-preview-button-wrapper">
				<button class="elementor-update-preview-button elementor-button elementor-button-success">'. __( 'Apply', DCE_TEXTDOMAIN).'</button>
			</div>
		</div>',
			 'content_classes' => 'dce-btn-reload',
			 'separator' => 'after',
			 'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	            ],
		  ]
		);*/
	$document->add_control(
            'custom_class_section_sfy', [
		        'label' => __('Custom section CLASS', DCE_TEXTDOMAIN),
	            'type' => Controls_Manager::TEXT,
	            'default' => '',
	            'placeholder' => 'Write custom CLASS',
	            'frontend_available' => true,
	            
	            'label_block' => true,
	            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
            ]
        );
     /* $document->add_control(
			'scrollify_id_page',
			[
				'label' => __( 'ID Page', DCE_TEXTDOMAIN ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => $dce_data['id'],
				'frontend_available' => true,
				'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	            ],
			]
		);*/

      	$document->add_control(
          	'interstitialSection',
          	[
	            'label'       => __( 'Interstitial Section', DCE_TEXTDOMAIN ),
	            'type'        => Controls_Manager::TEXT,
	            'default'     => __( '', DCE_TEXTDOMAIN ),
	            'placeholder' => __( 'header, footer', DCE_TEXTDOMAIN ),
	            'frontend_available' => true,
	            'label_block' => true,
	            'condition' => [
		                'enable_scrollify' => 'yes',
		                'enable_dceScrolling' => 'yes',
		                //'directionScroll' => 'vertical'
		            ],
          	]
        );
      $document->add_control(
        'scrollSpeed',
        [
            'label' => __( 'Scroll Speed', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 1000,
            ],
            'range' => [
                'px' => [
                    'min' => 500,
                    'max' => 2400,
                    'step' => 10,
                ],
            ],
            'size_units' => [ 'ms', ],
            'frontend_available' => true,
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
        ]
    );
     $document->add_control(
        'offset',
        [
            'label' => __( 'Offset', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => -500,
                    'max' => 500,
                    'step' => 1,
                ],
            ],
            'size_units' => [ 'px' ],
            'frontend_available' => true,
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
        ]
    );
    /*$document->add_control(
    	'ease_scrollify',
        [
			'label' => __( 'Ease', DCE_TEXTDOMAIN ),
			'type' => Controls_Manager::SELECT,
			'default' => 'easeOutQuad',
			'options' => DCE_Helper::get_ease_timingFunctions(), 
			'frontend_available' => true,
			'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
		]
	);*/
	$document->add_control(
        'setHeights',
        [
			'label' => __( 'Set Heights', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            'frontend_available' => true,
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
		]
      );
	$document->add_control(
        'overflowScroll',
        [
			'label' => __( 'Overflow Scroll', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            'frontend_available' => true,
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
		]
      );
	$document->add_control(
        'updateHash',
        [
			'label' => __( 'Update Hash', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            'frontend_available' => true,
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
		]
      );
	$document->add_control(
        'touchScroll',
        [
			'label' => __( 'Touch Scroll', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            'frontend_available' => true,
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
		]
      );
	// -----------------------------------------
	$document->add_control(
        'enable_scrollify_nav',
        [
			'label' => __( 'Enable navigation', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            'frontend_available' => true,
            'render_type' => 'template',
            'separator' => 'before',
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                //'directionScroll' => 'vertical'
	            ],
		]
      );
	  /*$document->add_control(
	  'scrollify_nav_border_type',
	    [
	       'label'       => __( 'Select', DCE_TEXTDOMAIN ),
	       'type' => Controls_Manager::SELECT,
	       'default' => 'solid',
	       'options' => [
	          'solid'  => __( 'Solid', DCE_TEXTDOMAIN ),
	          'dashed' => __( 'Dashed', DCE_TEXTDOMAIN ),
	          'dotted' => __( 'Dotted', DCE_TEXTDOMAIN ),
	          'double' => __( 'Double', DCE_TEXTDOMAIN ),
	          'none'   => __( 'None', DCE_TEXTDOMAIN ),
	       ],
	      //'frontend_available' => true,
	       'selectors' => [ // You can use the selected value in an auto-generated css rule.
	          '{{WRAPPER}}.dce-scrollify .dce-scrollify-pagination a' => 'border-style: {{VALUE}}',
	       ],
	       'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                'enable_scrollify_nav' => 'yes',
	            ],
	    ]
	  );*/
	  
	  $document->add_control(
        'scrollify_nav_size',
        [
            'label' => __( 'Size', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 10,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 80,
                    'step' => 1,
                ],
            ],
            'size_units' => [ 'px' ],
            'selectors' => [
	              '{{WRAPPER}}.dce-scrollify .dce-scrollify-pagination a:after' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
	          ],
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                'enable_scrollify_nav' => 'yes',
	            ],
        ]
    );
	  $document->add_control(
        'scrollify_nav_border_size',
        [
            'label' => __( 'Border size', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 1,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 20,
                    'step' => 1,
                ],
            ],
            'size_units' => [ 'px' ],
            'selectors' => [
	              '{{WRAPPER}}.dce-scrollify .dce-scrollify-pagination a:after' => 'border-width: {{SIZE}}{{UNIT}}',
	          ],
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                'enable_scrollify_nav' => 'yes',
	            ],
        ]
    );
	   $document->add_control(
        'scrollify_nav_border_active_size',
        [
            'label' => __( 'Active border size', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 3,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 20,
                    'step' => 1,
                ],
            ],
            'size_units' => [ 'px' ],
            'selectors' => [
	              '{{WRAPPER}}.dce-scrollify .dce-scrollify-pagination a.active:after' => 'box-shadow: inset 0 0 0 {{SIZE}}{{UNIT}};',
	          ],
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                'enable_scrollify_nav' => 'yes',
	            ],
        ]
    );
	  $document->add_control(
        'scrollify_nav_space',
        [
            'label' => __( 'Space', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 10,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'size_units' => ['px'],
            'selectors' => [
	              '{{WRAPPER}}.dce-scrollify .dce-scrollify-pagination a' => 'padding: {{SIZE}}{{UNIT}}',
	          ],
            'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                'enable_scrollify_nav' => 'yes',
	            ],
        ]
    );
	  $document->add_control(
	    'scrollify_nav_color',
	      [
	          'label' => __( 'Color', DCE_TEXTDOMAIN ),
	          'type' => Controls_Manager::COLOR,
	          'scheme' => [
	              'type' => Scheme_Color::get_type(),
	              'value' => Scheme_Color::COLOR_1,
	          ],
	          'default' => '#444444',
	          'selectors' => [
	              '{{WRAPPER}}.dce-scrollify .dce-scrollify-pagination a:after' => 'color: {{VALUE}}',
	          ],
	          'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                'enable_scrollify_nav' => 'yes',
	            ],
	      ]
	  );
	   $document->add_control(
	    'scrollify_nav_color_active',
	      [
	          'label' => __( 'Active color', DCE_TEXTDOMAIN ),
	          'type' => Controls_Manager::COLOR,
	          'scheme' => [
	              'type' => Scheme_Color::get_type(),
	              'value' => Scheme_Color::COLOR_1,
	          ],
	          'default' => '',
	          'selectors' => [
	              '{{WRAPPER}}.dce-scrollify .dce-scrollify-pagination a.active, {{WRAPPER}}.dce-scrollify .dce-scrollify-pagination a.active:after' => 'color: {{VALUE}}',
	          ],
	          'condition' => [
	                'enable_scrollify' => 'yes',
	                'enable_dceScrolling' => 'yes',
	                'enable_scrollify_nav' => 'yes',
	            ],
	      ]
	  );
	    // ----------------------------------- INERTIA --------------------------
      $document->add_control(
        'enable_inertiaScroll',
        [
			'label' => __( 'InertiaScroll', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'separator' => 'before',
            'label_on' => __( 'Yes', DCE_TEXTDOMAIN ),
            'label_off' => __( 'No', DCE_TEXTDOMAIN ),
            'return_value' => 'yes',
            'frontend_available' => true,
            'condition' => [
	                'enable_dceScrolling' => 'yes',
	            ],
		]
      );
      /*$document->add_control(
        'scroll_direction_info',
        [
			'label' => __( 'Direction info', DCE_TEXTDOMAIN ),
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'default' => '',
           
          	'raw' 				=> __( '<div>Definisce se lo scorrimento delle sezioni è Naturale (verticale) oppure Orizzontale (richiede trasformazioni)</div>', DCE_TEXTDOMAIN ),
			'content_classes' 	=> 'dce-document-settings',
			
			'condition' => [
	                'enable_dceScrolling' => 'yes',
	            ],
		]
      );*/
      $document->add_control(
         'coefSpeed_inertiaScroll', [
	            'label' => __('Coef. of Speed (0-1) Default: 0.05', DCE_TEXTDOMAIN),
	            'type' => Controls_Manager::SLIDER,
	            'default' => [
	                'size' => '0.05',
	            ],
	            'range' => [
	                'px' => [
	                    'max' => 1,
	                    'min' => 0,
	                    'step' => 0.01,
	                ],
	            ],
	            'frontend_available' => true,
	            'condition' => [
	                'enable_dceScrolling' => 'yes',
	                'enable_inertiaScroll' => 'yes'

	            ],
          ]
        );
     /* $document->add_control(
          'directionScroll', [
            'label' => __('Direction', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'vertical' => __('Vertical', DCE_TEXTDOMAIN),
                'horizontal' => __('Horizontal', DCE_TEXTDOMAIN),
            ],
            'default' => 'vertical',
            //'prefix_class' => 'scroll-direction-',
            'frontend_available' => true,
            'condition' => [
	                'enable_dceScrolling' => 'yes',
	                'enable_inertiaScroll' => 'yes'
	            ],
          ]
        );*/
      $document->add_control(
			'directionScroll',
			[
				'label' => __( 'direction of Scroll', DCE_TEXTDOMAIN ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'vertical',
				'options' => [
			          'vertical'  => __( 'Vertical', DCE_TEXTDOMAIN ),
			          'horizontal'  => __( 'Horizontal', DCE_TEXTDOMAIN ),
			    ],
				'frontend_available' => true,
				'condition' => [
	                'enable_dceScrolling' => 'yes',
	                'enable_inertiaScroll' => 'yes'
	            ],
			]
		);
      $document->add_control(
        'scroll_target',
        [
			'label' => __( 'Target (optional)', DCE_TEXTDOMAIN ),
			'description' => 'the ID tag of the main item to be scrolled',
            'type' => Controls_Manager::TEXT,
            'default' => '',
           
            'frontend_available' => true,
            'condition' => [
	                'enable_dceScrolling' => 'yes',
	                'enable_inertiaScroll' => 'yes'

	            ],
		]
      );
      /*$document->add_control(
			'inertiaScroll_id_page',
			[
				'label' => __( 'ID Page', DCE_TEXTDOMAIN ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => $id_page,
				'frontend_available' => true,
				'condition' => [
	                'enable_scrollEffects' => 'yes',
	            ],
			]
		);*/
	}

	protected function add_actions() {
		//$settings = $this->get_settings_for_display();
		//page-settings
		//document
		//common (i widget)
		$element_data;

		// Activate controls for Post
		add_action( 'elementor/element/post/section_dce_document_scroll/before_section_end', function( $element, $args ) {

			$this->add_controls( $element, $args );
			/*$element_data = $element;

			  foreach ($element_data as $name => $value) {
			      echo "$name: $value\n";
			  }*/
		}, 10, 2 );
		add_action( 'elementor/element/page/section_dce_document_scroll/before_section_end', function( $element, $args ) {

			$this->add_controls( $element, $args );
			/*$element_data = $element;

			  foreach ($element_data as $name => $value) {
			      echo "$name: $value\n";
			  }*/
		}, 10, 2 );

		add_action( 'elementor/element/section/section_dce_document_scroll/before_section_end', function( $element, $args ) {

			$this->add_controls( $element, $args );
			
		}, 10, 2 );
		add_action( 'elementor/element/product/section_dce_document_scroll/before_section_end', function( $element, $args ) {

			$this->add_controls( $element, $args );
			/*$element_data = $element;

			  foreach ($element_data as $name => $value) {
			      echo "$name: $value\n";
			  }*/
		}, 10, 2 );
		
		
		/*add_action( 'elementor/frontend/post/after_render', function( $element, $args ) {

			echo 'ciao';

		}, 10, 2 );*/

	}

}
