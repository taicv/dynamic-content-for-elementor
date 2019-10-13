<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elements Post Author
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */
class DCE_Widget_Author extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-author';
    }
    
    static public function is_enabled() {
        return false;
    }

    public function get_title() {
        return __('Author', DCE_TEXTDOMAIN);
    }

    public function get_icon() {
        return 'eicon-person';
    }

    static public function get_position() {
        return 5;
    }

    protected function _register_controls() {

       $this->start_controls_section(
            'section_content', [
                'label' => __('Author', DCE_TEXTDOMAIN),
            ]
        );
        $this->add_control(
            'author', [
                'label' => __('Author', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'options' => $this->user_fields_labels(),
                'default' => 'display_name',
            ]
        );
        $this->add_control(
            'html_tag', [
                'label' => __('HTML Tag', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __('H1', DCE_TEXTDOMAIN),
                    'h2' => __('H2', DCE_TEXTDOMAIN),
                    'h3' => __('H3', DCE_TEXTDOMAIN),
                    'h4' => __('H4', DCE_TEXTDOMAIN),
                    'h5' => __('H5', DCE_TEXTDOMAIN),
                    'h6' => __('H6', DCE_TEXTDOMAIN),
                    'p' => __('p', DCE_TEXTDOMAIN),
                    'div' => __('div', DCE_TEXTDOMAIN),
                    'span' => __('span', DCE_TEXTDOMAIN),
                ],
                'default' => 'p',
            ]
        );
        $this->add_responsive_control(
            'align', [
                'label' => __('Alignment', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', DCE_TEXTDOMAIN),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'link_to', [
                'label' => __('Link to', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', DCE_TEXTDOMAIN),
                    'home' => __('Home URL', DCE_TEXTDOMAIN),
                    'post' => sprintf(
                            /* translators: %s: Post type singular name (e.g. Post or Page) */
                            __('%s URL', DCE_TEXTDOMAIN), $post_type_object->labels->singular_name
                    ),
                    'author' => __('Author URL', DCE_TEXTDOMAIN),
                    'custom' => __('Custom URL', DCE_TEXTDOMAIN),
                ],
            ]
        );
        $this->add_control(
            'link', [
                'label' => __('Link', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::URL,
                'placeholder' => __('http://your-link.com', DCE_TEXTDOMAIN),
                'condition' => [
                    'link_to' => 'custom',
                ],
                'default' => [
                    'url' => '',
                ],
                'show_label' => false,
            ]
        );
        $this->end_controls_section();
        // ------------------------------------------------- STYLE
        $this->start_controls_section(
            'section_style', [
                'label' => sprintf(
                        /* translators: %s: Post type singular name (e.g. Post or Page) */
                        __('%s Author', DCE_TEXTDOMAIN), $post_type_object->labels->singular_name
                ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'color', [
                'label' => __('Text Color', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-content-for-elementor-author' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dynamic-content-for-elementor-author a' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'author!' => 'image',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-author',
                'condition' => [
                    'author!' => 'image',
                ],
            ]
        );
        $this->add_responsive_control(
            'space', [
                'label' => __('Size (%)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'size_units' => [ '%'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dynamic-content-for-elementor-author img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'author' => 'image',
                ],
            ]
        );
        $this->add_responsive_control(
            'opacity', [
                'label' => __('Opacity (%)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dynamic-content-for-elementor-author img' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'author' => 'image',
                ],
            ]
        );
        $this->add_control(
            'angle', [
                'label' => __('Angle (deg)', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'deg'],
                'default' => [
                    'unit' => 'deg',
                    'size' => 0,
                ],
                'range' => [
                    'deg' => [
                        'max' => 360,
                        'min' => -360,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dynamic-content-for-elementor-author img' => '-webkit-transform: rotate({{SIZE}}deg); -moz-transform: rotate({{SIZE}}deg); -ms-transform: rotate({{SIZE}}deg); -o-transform: rotate({{SIZE}}deg); transform: rotate({{SIZE}}deg);',
                ],
                'condition' => [
                    'author' => 'image',
                ],
            ]
        );
        $this->add_control(
            'hover_animation', [
                'label' => __('Hover Animation', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'image_border',
                'label' => __('Image Border', DCE_TEXTDOMAIN),
                'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-author img',
                'condition' => [
                    'author' => 'image',
                ],
            ]
        );
        $this->add_control(
            'image_border_radius', [
                'label' => __('Border Radius', DCE_TEXTDOMAIN),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dynamic-content-for-elementor-author img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'author' => 'image',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-author img',
                'condition' => [
                    'author' => 'image',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_active_settings();
        if ( empty( $settings ) )
            return;

        $author = $this->user_data($settings['author']);

        switch ($settings['link_to']) {
            case 'custom' :
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = false;
                }
                break;

            case 'author' :
                $link = esc_url(get_author_posts_url(get_the_author_meta('ID')));
                break;

            case 'post' :
                $link = esc_url(get_the_permalink());
                break;

            case 'home' :
                $link = esc_url(get_home_url());
                break;

            case 'none' :
            default:
                $link = false;
                break;
        }
        $target = $settings['link']['is_external'] ? 'target="_blank"' : '';

        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';

        $html = sprintf('<%1$s class="dynamic-content-for-elementor-author %2$s">', $settings['html_tag'], $animation_class);
        if ($link) {
            $html .= sprintf('<a href="%1$s" %2$s>%3$s</a>', $link, $target, $author);
        } else {
            $html .= $author;
        }
        $html .= sprintf('</%s>', $settings['html_tag']);

        echo $html;
    }

    protected function _content_template() {
        /*
        ?>
        <#
        var author_data = [];
        <?php
        foreach ($this->user_data() as $key => $value) {
            printf('author_data[ "%1$s" ] = "%2$s";', $key, $value);
        }
        ?>
        var author = author_data[ settings.author ];

        var link_url;
        switch( settings.link_to ) {
        case 'custom':
        link_url = settings.link.url;
        break;
        case 'author':
        link_url = '<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>';
        break;
        case 'post':
        link_url = '<?php echo esc_url(get_the_permalink()); ?>';
        break;
        case 'home':
        link_url = '<?php echo esc_url(get_home_url()); ?>';
        break;
        case 'none':
        default:
        link_url = false;
        }
        var target = settings.link.is_external ? 'target="_blank"' : '';

        var animation_class = '';
        if ( '' !== settings.hover_animation ) {
        animation_class = 'elementor-animation-' + settings.hover_animation;
        }

        var html = '<' + settings.html_tag + ' class="dynamic-content-for-elementor-author ' + animation_class + '">';
        if ( link_url ) {
        html += '<a href="' + link_url + '" ' + target + '>' + author + '</a>';
        } else {
        html += author;
        }
        html += '</' + settings.html_tag + '>';

        print( html );
        #>
        <?php
         * 
         */
    }

    protected function user_fields_labels() {

        $fields = [
            'first_name' => __('First Name', DCE_TEXTDOMAIN),
            'last_name' => __('Last Name', DCE_TEXTDOMAIN),
            'first_last' => __('First Name + Last Name', DCE_TEXTDOMAIN),
            'last_first' => __('Last Name + First Name', DCE_TEXTDOMAIN),
            'nickname' => __('Nick Name', DCE_TEXTDOMAIN),
            'display_name' => __('Display Name', DCE_TEXTDOMAIN),
            'user_login' => __('User Name', DCE_TEXTDOMAIN),
            'description' => __('User Bio', DCE_TEXTDOMAIN),
            'image' => __('User Image', DCE_TEXTDOMAIN),
        ];

        return $fields;
    }

    protected function user_data($selected = '') {

        global $post;

        $author_id = $post->post_author;

        $fields = [
            'first_name' => get_the_author_meta('first_name', $author_id),
            'last_name' => get_the_author_meta('last_name', $author_id),
            'first_last' => sprintf('%s %s', get_the_author_meta('first_name', $author_id), get_the_author_meta('last_name', $author_id)),
            'last_first' => sprintf('%s %s', get_the_author_meta('last_name', $author_id), get_the_author_meta('first_name', $author_id)),
            'nickname' => get_the_author_meta('nickname', $author_id),
            'display_name' => get_the_author_meta('display_name', $author_id),
            'user_login' => get_the_author_meta('user_login', $author_id),
            'description' => get_the_author_meta('description', $author_id),
            'image' => get_avatar(get_the_author_meta('email', $author_id), 256),
        ];

        if (empty($selected)) {
            // Return the entire array
            return $fields;
        } else {
            // Return only the selected field
            return $fields[$selected];
        }
    }

}
