<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\DCE_Helper;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dynamic Content Excerpt
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */
class DCE_Widget_Excerpt extends DCE_Widget_Prototype {

    static $remove_recursion_loop = [];

    public function get_name() {
        return 'dyncontel-excerpt';
    }

    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('Excerpt', DCE_TEXTDOMAIN);
    }

    public function get_description() {
        return __('Add your content in a shortener', DCE_TEXTDOMAIN);
    }

    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/excerpt/';
    }

    public function get_icon() {
        return 'icon-dyn-excerpt';
    }

    static public function get_position() {
        return 2;
    }

    protected function _register_controls() {

        $post_type_object = get_post_type_object(get_post_type());
        // $post_type_object->labels->singular_name

        $this->start_controls_section(
                'section_content', [
            'label' => __('Excerpt', DCE_TEXTDOMAIN),
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
            'default' => 'div',
                ]
        );

        $this->add_control(
                'excerpt_advanced',
                [
                    'label' => __('Advanced manipulation', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
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
                'post' => __('Post URL', DCE_TEXTDOMAIN),
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

        $this->start_controls_section(
                'section_advanced_excerpt', [
            'label' => __('Advanced Manipulation', DCE_TEXTDOMAIN),
            'condition' => [
                'excerpt_advanced!' => '',
            ],
                ]
        );

        $this->add_control(
                'excerpt_no_custom',
                [
                    'label' => __('Generate Excerpts', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Generate excerpts even if a post hasn\'t a custom excerpt attached.', DCE_TEXTDOMAIN),
                ]
        );

        $this->add_control(
                'excerpt_length',
                [
                    'label' => __('Excerpt Length', DCE_TEXTDOMAIN),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'default' => 40,
                ]
        );
        $this->add_control(
                'excerpt_length_type',
                [
                    'label' => __('Length Unit', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'words',
                    'options' => [
                        'words' => __('Words', DCE_TEXTDOMAIN),
                        'charachters' => __('Charachters', DCE_TEXTDOMAIN),
                    //'sentences' => __('Sentences', DCE_TEXTDOMAIN),
                    ],
                ]
        );
        $this->add_control(
                'excerpt_ellipsis',
                [
                    'label' => __('Excerpt Ellipsis', DCE_TEXTDOMAIN),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'description' => __('Will substitute the part of the post that is omitted in the excerpt.', DCE_TEXTDOMAIN),
                    'default' => '&hellip;',
                ]
        );
        $this->add_control(
                'excerpt_finish',
                [
                    'label' => __('Finish', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'exact',
                    'options' => [
                        'exact' => __('Exact', DCE_TEXTDOMAIN),
                        'exact_w_spaces' => __('Exact (count spaces as well)', DCE_TEXTDOMAIN),
                        'word' => __('Word', DCE_TEXTDOMAIN),
                        'sentence' => __('Sentence', DCE_TEXTDOMAIN),
                    ],
                ]
        );

        $this->add_control(
                'excerpt_no_shortcode',
                [
                    'label' => __('Remove Shortcode', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->add_control(
                'excerpt_strip_tags',
                [
                    'label' => __('Strip Tags', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                ]
        );
        $this->add_control(
                'excerpt_allowed_tags',
                [
                    'label' => __('Remove all tags except the following', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => 'a,b,strong,i',
                    'description' => __('Write a list of HTML tag to maintain separated by comma.', DCE_TEXTDOMAIN),
                    'label_block' => true,
                    'condition' => [
                        'excerpt_strip_tags!' => '',
                    ],
                ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
                'section_style', [
            'label' => __('Excerpt', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_STYLE,
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
                'color', [
            'label' => __('Text Color', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::COLOR,
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .dce-excerpt, {{WRAPPER}} .dce-excerpt a' => 'color: {{VALUE}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .dce-excerpt',
                ]
        );

        $this->add_control(
                'rollhover_heading',
                [
                    'label' => __('Roll-Hover', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'link_to!' => 'none',
                    ],
                ]
        );
        $this->add_control(
                'hover_color', [
            'label' => __('Hover Text Color', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::COLOR,
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .dce-excerpt a:hover' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'link_to!' => 'none',
            ],
                ]
        );
        $this->add_control(
                'hover_animation', [
            'label' => __('Hover Animation', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::HOVER_ANIMATION,
            'condition' => [
                'link_to!' => 'none',
            ],
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'section_dce_settings', [
            'label' => __('Dynamic Content', DCE_TEXTDOMAIN),
            'tab' => Controls_Manager::TAB_SETTINGS,
                ]
        );
        $this->add_control(
                'data_source',
                [
                    'label' => __('Source', DCE_TEXTDOMAIN),
                    'description' => __('Select the data source', DCE_TEXTDOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'label_on' => __('Same', DCE_TEXTDOMAIN),
                    'label_off' => __('other', DCE_TEXTDOMAIN),
                    'return_value' => 'yes',
                ]
        );
        $this->add_control(
                'other_post_source', [
            'label' => __('Select from other source post', DCE_TEXTDOMAIN),
            'type' => Controls_Manager::SELECT,
            'groups' => DCE_Helper::get_all_posts(get_the_ID(), true),
            'default' => '',
            'condition' => [
                'data_source' => '',
            ],
                ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;
        //
        // ------------------------------------------
        $dce_data = DCE_Helper::dce_dynamic_data($settings['other_post_source']);
        $id_page = $dce_data['id'];
        $global_is = $dce_data['is'];
        $type_page = $dce_data['type'];
        // ------------------------------------------
        //

        if (isset($remove_recursion_loop[$id_page])) {
            return;
        }

        $remove_recursion_loop[$id_page] = true;

        $post = get_post($id_page);
        $excerpt = $post->post_excerpt; //get_the_excerpt($id_page); //$this->the_excerpt_max_charlength($id_page, 240);
        if ($settings['excerpt_advanced']) {
            $excerpt = $this->get_the_excerpt($post);
        }
        //var_dump($excerpt);

        //echo '------ '.$excerpt.' ------';
        if (empty($excerpt))
            return;



        switch ($settings['link_to']) {
            case 'custom' :
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = false;
                }
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

        $html = sprintf('<%1$s class="dce-excerpt %2$s">', $settings['html_tag'], $animation_class);
        if ($link) {
            $html .= sprintf('<a href="%1$s" %2$s>%3$s</a>', $link, $target, $excerpt);
        } else {
            $html .= $excerpt;
        }
        $html .= sprintf('</%s>', $settings['html_tag']);

        echo $html;
    }

    public function get_the_excerpt($post) {

        $settings = $this->get_settings_for_display();

        $excerpt = $post->post_excerpt;

        if (!trim(strip_tags($excerpt))) {
            if ($settings['excerpt_no_custom']) {
                $pieces = explode('<!--more-->', $post->post_content, 2);
                //if ( count($pieces) == 2 ) {
                $excerpt = reset($pieces);
                //}
            }
        }

        // remove shortcodes
        if ($settings['excerpt_no_shortcode']) {
            $excerpt = strip_shortcodes($excerpt);
            $excerpt = DCE_Helper::vc_strip_shortcodes($excerpt);
        }

        $excerpt = apply_filters('the_excerpt', $excerpt);

        // From the default wp_trim_excerpt():
        // Some kind of precaution against malformed CDATA in RSS feeds I suppose
        $excerpt = str_replace(']]>', ']]&gt;', $excerpt);

        // Strip HTML if $allowed_tags_option is set to 'remove_all_tags_except'
        if ($settings['excerpt_strip_tags']) {
            $allowed_tags = DCE_Helper::str_to_array(',', $settings['excerpt_allowed_tags'], 'strtolower');
            if (!empty($allowed_tags)) {
                $tag_string = '<' . implode('><', $allowed_tags) . '>';
            } else {
                $tag_string = '';
            }
            $excerpt = strip_tags($excerpt, $tag_string);
        }

        // Create the excerpt
        $excerpt = DCE_Helper::text_reduce($excerpt, $settings['excerpt_length'], $settings['excerpt_length_type'], $settings['excerpt_finish']);

        $excerpt = $excerpt . $settings['excerpt_ellipsis'];

        return $excerpt;
    }

}
