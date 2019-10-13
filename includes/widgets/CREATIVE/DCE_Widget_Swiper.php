<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Swiper
 *
 * Elementor widget for Dinamic Content Elements
 *
 */
class DCE_Widget_Swiper extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-swiper';
    }
    static public function is_enabled() {
        return false;
    }    
    public function get_title() {
        return __('Swiper', DCE_TEXTDOMAIN);
    }
    public function get_icon() {
        return 'icon-dyn-carousel';
    }
    public function get_script_depends() {
        return [ 'jquery-swiper', 'dce-swiper'];
    }
    public function get_style_depends() {
        return [ 'dce-photoSwipe_default','dce-photoSwipe_skin','dce-swiper' ];
    }
    protected function _register_controls() {
        $this->start_controls_section(
            'section_swiper_slides', [
                'label' => __('Swiper', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_responsive_control(
            'height', [
                'label' => __('Height', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 500,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                  'unit' => 'px',
                ],
                'mobile_default' => [
                   'unit' => 'px',
                ],
                'size_units' => [ 'px', 'rem', 'vh'],
                'range' => [
                    'rem' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-swiper .swiper-container' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'frontend_available' => true,
            ]
        );
        $this->add_responsive_control(
            'spaceV', [
                'label' => __('Spazio Vericale', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                    'unit' => 'px',
                ],
                /* 'tablet_default' => [
                  'unit' => 'px',
                  ],
                  'mobile_default' => [
                  'unit' => 'px',
                  ], */
                'size_units' => [ 'px', 'em', 'vh'],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-swiper .swiper-container' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};', //'height: {{SIZE}}{{UNIT}};',
                ],
                'frontend_available' => true,
            ]
        );
        $this->add_responsive_control(
            'spaceH', [
                'label' => __('Spazio Orizzontale', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                  'unit' => 'px',
                ],
                'mobile_default' => [
                  'unit' => 'px',
                ],
                'size_units' => [ 'px', 'em'],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-swiper .swiper-container' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};', //'height: {{SIZE}}{{UNIT}};',
                ],
                'frontend_available' => true,
            ]
        );
        $repeater = new Repeater();

        $repeater->start_controls_tabs('swiper_repeater');

        $repeater->start_controls_tab('tab_content', [ 'label' => __('Item', DCE_TEXTDOMAIN)]);
        $repeater->add_control(
            'id_name', [
                'label' => __('Name', DCE_TEXTDOMAIN),
                'description' => __('Il nome LABEL della sezione.', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'default' => 'Section',
            ]
        );
        $repeater->add_control(
            'slug_name', [
                'label' => __('Slug', DCE_TEXTDOMAIN),
                'description' => __('Lo SLUG della slide, usato nell\'indirizzo URL e negli identificativi interni. (deve essere univoco)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::TEXT,
                'default' => 'section-fp',
            ]
        );
        //
        //
		$repeater->add_control(
            'colorbg_section', [
                'label' => __('Background Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
           ]
        );
        $repeater->add_control(
            'bg_image', [
                'label' => __('Image', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );
        $repeater->add_control(
            'template', [
                'label' => __('Select Template', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                //'options' => get_post_taxonomies( $post->ID ),
                'options' => DCE_Helper::get_all_template(),
                'default' => '',
            ]
        );
        //




        $repeater->end_controls_tab();

        /* $repeater->start_controls_tab( 'tab_media', [ 'label' => __( 'Media', DCE_TEXTDOMAIN ) ] );



          $repeater->end_controls_tab(); */

        $repeater->start_controls_tab('tab_style', [ 'label' => __('Style', DCE_TEXTDOMAIN)]);

        // Single Slide Style ......

        $repeater->end_controls_tab();


        $repeater->end_controls_tabs();

        $this->add_control(
            'swiper', [
                'label' => __('Slides', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                ],
                'fields' => array_values($repeater->get_controls()),
                'title_field' => '{{{id_name}}}',
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();
        // ------------------------------------------------------------------------------- Base Settings, Slides grid, Grab Cursor
        $this->start_controls_section(
            'section_swiper_settings', [
                'label' => __('Settings', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'direction', [
                'label' => __('Direzione', DCE_TEXTDOMAIN),
                'description' => __('La direzione dello slider', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'horizontal' => __('Horizontal', DCE_TEXTDOMAIN),
                    'vertical' => __('Vertical', DCE_TEXTDOMAIN),
                ],
                'default' => 'horizontal',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'speed', [
                'label' => __('Velocità', DCE_TEXTDOMAIN),
                'description' => __('Durata della transizione tra diapositive (in ms)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => 300,
                'min' => 0,
                'max' => 3000,
                'step' => 10,
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'effects', [
                'label' => __('Effect of transition', DCE_TEXTDOMAIN),
                'description' => __('L\'effetto di transizione tra le slides', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'slide' => __('Slide', DCE_TEXTDOMAIN),
                    'fade' => __('Fade', DCE_TEXTDOMAIN),
                    'cube' => __('Cube', DCE_TEXTDOMAIN),
                    'coverflow' => __('Coverflow', DCE_TEXTDOMAIN),
                    'flip' => __('Flip', DCE_TEXTDOMAIN),
                    'custom1' => __('Custom1', DCE_TEXTDOMAIN),
                ],
                'default' => 'slide',
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'centeredSlides', [
                'label' => __('Centered Slides', DCE_TEXTDOMAIN),
                'description' => __('Se è vero, la diapositiva attiva sarà centrata, non sul lato sinistro.', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'special_options', [
                'label' => __('Specials options', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'setWrapperSize', [
                'label' => __('Set Wrapper Size', DCE_TEXTDOMAIN),
                'description' => __('imposta la larghezza / altezza sul wrapper swiper pari alla dimensione totale di tutte le diapositive. Principalmente dovrebbe essere utilizzato come opzione di backup di compatibilità per il browser che non supporta bene il layout di flessibilità', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'virtualTranslate', [
                'label' => __('Virtual Translate', DCE_TEXTDOMAIN),
                'description' => __('Utile quando è necessario creare una transizione personalizzata (vedi effects)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'autoHeight', [
                'label' => __('Auto Height', DCE_TEXTDOMAIN),
                'description' => __('Impostato su SI e lo slider wrapper adotterà la sua altezza all\'altezza della diapositiva attualmente attiva', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'roundLengths', [
                'label' => __('Round Lengths', DCE_TEXTDOMAIN),
                'description' => __('Impostare valori veraci a valori rotondi della larghezza e dell\'altezza delle diapositive per evitare testi sfocati sulle schermate di risoluzione usuali (se si dispone di tali)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'nested', [
                'label' => __('Nidificato', DCE_TEXTDOMAIN),
                'description' => __('Impostare su SI su Swiper nidificato, per intercettazioni corrette degli eventi di tocco. Utilizzare solo su spazzole annidate che utilizzano la stessa direzione del genitore', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'grabCursor', [
                'label' => __('Grab Cursor', DCE_TEXTDOMAIN),
                'description' => __('Questa opzione può un po\' migliorare l\'usabilità del desktop. Se è vero , l\'utente vedrà il cursore afferrare quando si trova su Swiper', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );

        $this->end_controls_section();
        // ------------------------------------------------------------------------------- Grid: Slide $ Flip
        $this->start_controls_section(
            'section_swiper_grid', [
                'label' => __('Slider/Coveflow Grid', DCE_TEXTDOMAIN),
                'condition' => [
                    'effects' => ['slide', 'coverflow'],
                ]
            ]
        );
        $this->add_control(
            'more_options', [
                'label' => __('Slides Grid', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'spaceBetween', [
                'label' => __('Space Between', DCE_TEXTDOMAIN),
                'description' => __('Distanza tra diapositive in px.', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'tablet_default' => '',
                'mobile_default' => '',
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'frontend_available' => true
            ]
        );
        $this->add_responsive_control(
            'slidesPerView', [
                'label' => __('Slides Per View', DCE_TEXTDOMAIN),
                'description' => __('Numero di diapositive per visualizzazione (diapositive visibili allo stesso tempo sul contenitore) Se il valore è 0 indica "auto" (NOTA: auto non è compatibile con: slidesPerColumn > 1). Se viene impostato "auto" e anche "loop", è necessario impostare "loopedSlides".', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => '',
                //'tablet_default' => '',
                //'mobile_default' => '',
                'min' => 0,
                'max' => 12,
                'step' => 1,
                'frontend_available' => true
            ]
        );
        $this->add_responsive_control(
            'slidesPerGroup', [
                'label' => __('Slides Per Group', DCE_TEXTDOMAIN),
                'description' => __('Nmposta i numeri di diapositive per definire e abilitare la scorrimento del gruppo. Utile da utilizzare con diapositivePerView > 1', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'tablet_default' => '',
                'mobile_default' => '',
                'min' => 0,
                'max' => 12,
                'step' => 1,
                'frontend_available' => true
            ]
        );

        $this->end_controls_section();
        // ------------------------------------------------------------------------------- Autoplay
        $this->start_controls_section(
            'section_swiper_autoplay', [
                'label' => __('Autoplay', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'autoplay', [
                'label' => __('Auto Play', DCE_TEXTDOMAIN),
                'description' => __('Ritardo tra transizioni (in ms). Se questo parametro non è specificato (di default), la riproduzione automatica sarà disattivata', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => '',
                'min' => 0,
                'max' => 3000,
                'step' => 100,
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'autoplayStopOnLast', [
                'label' => __('Autoplay stop on last slide', DCE_TEXTDOMAIN),
                'description' => __('Abilitare questo parametro e l\'autoplay verrà interrotto quando raggiunge l\'ultima diapositiva (non ha alcun effetto in modalità loop/ciclico)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'autoplayDisableOnInteraction', [
                'label' => __('Autoplay Disable on interaction', DCE_TEXTDOMAIN),
                'description' => __('Impostato su NO e l\'autoplay non verrà disattivato dopo le interazioni utente (swipes), verrà riavviato ogni volta dopo l\'interazione', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->end_controls_section();
        // ------------------------------------------------------------------------------- Progress
        $this->start_controls_section(
            'section_swiper_progress', [
                'label' => __('Progress', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'watchSlidesProgress', [
                'label' => __('Watch Slides Progress', DCE_TEXTDOMAIN),
                'description' => __('Attiva questa funzionalità per calcolare ogni progresso delle diapositive', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'watchSlidesVisibility', [
                'label' => __('Watch Slides Visibility', DCE_TEXTDOMAIN),
                'description' => __('Abilita questa opzione e le diapositive che sono in visualizzazione avranno una classe visibile supplementare', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
                'condition' => [
                    'watchSlidesProgress' => 'yes',
                ]
            ]
        );
        $this->end_controls_section();
        // ------------------------------------------------------------------------------- Freemode
        $this->start_controls_section(
            'section_swiper_freemode', [
                'label' => __('Freemode', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'freeMode', [
                'label' => __('Free Mode', DCE_TEXTDOMAIN),
                'description' => __('Se true, le diapositive non avranno posizioni fisse', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true
            ]
        );
        $this->add_control(
            'freeModeMomentum', [
                'label' => __('Free Mode Momentum', DCE_TEXTDOMAIN),
                'description' => __('Se è vero, allora la diapositiva continuerà a muoversi per un po\' dopo averlo rilasciato', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
                'condition' => [
                    'freeMode' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'freeModeMomentumRatio', [
                'label' => __('Free Mode Momentum Ratio', DCE_TEXTDOMAIN),
                'description' => __('Il valore più elevato produce distanza più grande di slancio dopo aver rilasciato il dispositivo di scorrimento', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 0,
                'max' => 10,
                'step' => 0.1,
                'frontend_available' => true,
                'condition' => [
                    'freeMode' => 'yes',
                    'freeModeMomentum' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'freeModeMomentumVelocityRatio', [
                'label' => __('Free Mode Momentum Velocity Ratio', DCE_TEXTDOMAIN),
                'description' => __('Il valore più elevato produce una velocità di slancio maggiore dopo aver rilasciato il dispositivo di scorrimento', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 0,
                'max' => 10,
                'step' => 0.1,
                'frontend_available' => true,
                'condition' => [
                    'freeMode' => 'yes',
                    'freeModeMomentum' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'freeModeMomentumBounce', [
                'label' => __('Free Mode Momentum Bounce', DCE_TEXTDOMAIN),
                'description' => __('Impostare su false se si desidera disattivare il rimbalzo della moto in modalità libera', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
                'condition' => [
                    'freeMode' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'freeModeMomentumBounceRatio', [
                'label' => __('Free Mode Momentum Bounce Ratio', DCE_TEXTDOMAIN),
                'description' => __('Il valore più elevato produce un effetto di rimbalzo più grande del momento', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 0,
                'max' => 10,
                'step' => 0.1,
                'frontend_available' => true,
                'condition' => [
                    'freeMode' => 'yes',
                    'freeModeMomentumBounce' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'freeModeMinimumVelocity', [
                'label' => __('Free Mode Momentum Velocity Ratio', DCE_TEXTDOMAIN),
                'description' => __('Velocità di spostamento minima necessaria per attivare la mossa di modalità libera', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::NUMBER,
                'default' => 0.02,
                'min' => 0,
                'max' => 1,
                'step' => 0.01,
                'frontend_available' => true,
                'condition' => [
                    'freeMode' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'freeModeSticky', [
                'label' => __('Free Mode Sticky', DCE_TEXTDOMAIN),
                'description' => __('Impostare su true per abilitare lo snap a scorrimento delle posizioni in modalità libera', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
                'condition' => [
                    'freeMode' => 'yes',
                ]
            ]
        );
        $this->end_controls_section();

        // ------------------------------------------------------------------------------- Parallax
        /* $this->start_controls_section(
          'section_swiper_tarallax',
          [
          'label'         => __( 'Parallax', DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section(); */
        // ------------------------------------------------------------------------------- Touches, Touch Resistance
        /* $this->start_controls_section(
          'section_swiper_touch',
          [
          'label'         => __( 'Touches & Touch Resistance', DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section(); */
        // ------------------------------------------------------------------------------- Swiping / No swiping
        /* $this->start_controls_section(
          'section_swiper_swiping',
          [
          'label'         => __( 'Swiping / No swiping', DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section(); */
        // ------------------------------------------------------------------------------- Navigation Controls, Pagination, Navigation Buttons, Scollbar, Accessibility
        /* $this->start_controls_section(
          'section_swiper_navigation',
          [
          'label'         => __( 'Navigation', DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section(); */
        // ------------------------------------------------------------------------------- Keyboard / Mousewheel
        $this->start_controls_section(
            'section_swiper_keyboardMousewheel', [
                'label' => __('Keyboard / Mousewheel', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'keyboardControl', [
                'label' => __('Keyboard Control', DCE_TEXTDOMAIN),
                'description' => __('Impostare su true per abilitare lo scorrimento da tastiera', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'mousewheelControl', [
                'label' => __('Mousewheel Control', DCE_TEXTDOMAIN),
                'description' => __('Impostare su true per abilitare lo scorrimento con la rotella del mouse', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );
        $this->end_controls_section();
        // ------------------------------------------------------------------------------- Hash/History Navigation
        /* $this->start_controls_section(
          'section_swiper_hashHistory',
          [
          'label'         => __( 'Hash/History Navigation', DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section(); */
        // ------------------------------------------------------------------------------- Images
        /* $this->start_controls_section(
          'section_swiper_images',
          [
          'label'         => __( 'Images', DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section(); */
        // ------------------------------------------------------------------------------- Loop
        $this->start_controls_section(
            'section_swiper_loop', [
                'label' => __('Loop', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'loop', [
                'label' => __('Loop', DCE_TEXTDOMAIN),
                'description' => __('Impostare su true per abilitare la modalità di ciclo continuo', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', DCE_TEXTDOMAIN),
                'label_off' => __('No', DCE_TEXTDOMAIN),
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );
        $this->end_controls_section();
        // ------------------------------------------------------------------------------- Zoom
        /* $this->start_controls_section(
          'section_swiper_zoom',
          [
          'label'         => __( 'Zoom', DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section(); */
        // ------------------------------------------------------------------------------- Controller
        /* $this->start_controls_section(
          'section_swiper_controller',
          [
          'label'         => __( 'Controller', DCE_TEXTDOMAIN),
          ]
          );
          $this->end_controls_section(); */
        // -------------------------------------------------------------------------------
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        global $global_ID;

        $effect = ' ' . $settings['effects'];
        $direction = ' direction-' . $settings['direction'];
        //
        echo '<div class="dce-swiper' . $effect . $direction . '">';
        echo '	<div class="swiper-container">';
        echo '		<div class="swiper-wrapper">';
        $counter_item = 1;
        $swiperItems = $settings['swiper'];
        if (!empty($swiperItems)) {
            foreach ($swiperItems as $swpitem) :

                $id_name = $swpitem['id_name'];
                $slug_name = $swpitem['slug_name'];

                $colorbg_section = $swpitem['colorbg_section'];
                $bg_image = $swpitem['bg_image']['url'];

                $template = $swpitem['template'];

                $bgcolor = '';
                if ($colorbg_section)
                    $bgcolor = 'background-color:' . $colorbg_section . ';';
                $bgimg = '';
                if ($bg_image)
                    $bgimg = ' background-image:url(' . $bg_image . ');';
                //echo $swpitem['id_name'];
                //echo $swpitem['slug_name'];
                echo '<div id="' . $slug_name . '" class="swiper-slide">';

                echo '<div class="slide-inner" style="' . $bgcolor . $bgimg . '"></div>';
                //echo do_shortcode('[dce-elementor-template id="'.$template.'"]');
                echo '</div>';


                $counter_item++;
            endforeach;
        }
        echo '		</div>';
        ?>
        <!-- If we need pagination -->
        <div class="swiper-pagination"></div>

        <!-- If we need navigation buttons -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <!-- If we need scrollbar -->
        <!-- <div class="swiper-scrollbar"></div> -->
        <?php
        echo '	</div>';
        echo '</div>';
    }

}
