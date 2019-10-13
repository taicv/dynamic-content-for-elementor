<?php

namespace DynamicContentForElementor;

/**
 * Main Helper Class
 *
 * @since 0.1.0
 */
class DCE_Helper {

    public static function is_plugin_active($plugin) {
        return self::is_plugin_active_for_local($plugin) || self::is_plugin_active_for_network($plugin);
    }
    public static function is_plugin_active_for_local($plugin) {
        if (is_multisite())
            return false;
        $active_plugins = get_option('active_plugins', array());
        return self::check_plugin($plugin, $active_plugins);
    }
    public static function is_plugin_active_for_network($plugin) {
        if (!is_multisite())
            return false;
        $active_plugins = get_site_option('active_sitewide_plugins');
        $active_plugins = array_keys($active_plugins);
        return self::check_plugin($plugin, $active_plugins);
    }
    public static function check_plugin($plugin, $active_plugins = array()) {
        if (in_array($plugin, (array) $active_plugins)) {
            return true;
        }
        if (!empty($active_plugins)) {
            foreach ($active_plugins as $aplugin) {
                $tmp = basename($aplugin);
                $tmp = pathinfo($tmp, PATHINFO_FILENAME);
                if ($plugin == $tmp) {
                    return true;
                }
            }
        }
        if (!empty($active_plugins)) {
            foreach ($active_plugins as $aplugin) {
                $pezzi = explode('/', $aplugin);
                $tmp = reset($pezzi);
                if ($plugin == $tmp) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * Custom Function for Remove Specific Tag in the string.
    */
    public static function strip_tag($string, $tag) {
        $string =  preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
        $string = preg_replace('/<\/'.$tag.'>/i', '', $string);
        return $string;
    }

    public static function remove_empty_p($content) {
        //$content = force_balance_tags( $content );
        //$content = preg_replace( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content );
        //$content = preg_replace( '~\s?<p>(\s| )+</p>\s?~', '', $content );
        $content = str_replace("<p></p>", "", $content);
        return $content;
    }

    public static function get_user_metas($grouped = false, $like = '') {
        global $wp_meta_keys;

        $userMetas = $userMetasGrouped = array();

        // ACF
        $acf_groups = get_posts(array('post_type' => 'acf-field-group', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
        if (!empty($acf_groups)) {
            foreach ($acf_groups as $aacf_group) {
                $is_user_group = false;
                $aacf_meta = maybe_unserialize($aacf_group->post_content);
                if (!empty($aacf_meta['location'])) {
                    foreach ($aacf_meta['location'] as $gkey => $gvalue) {
                        foreach ($gvalue as $rkey => $rvalue) {
                            if (substr($rvalue['param'], 0, 5) == 'user_') {
                                $is_user_group = true;
                            }
                        }
                    }
                }
                if ($is_user_group) {
                    $acf = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1, 'post_status' => 'publish', 'post_parent' => $aacf_group->ID, 'suppress_filters' => false));
                    if (!empty($acf)) {
                        foreach ($acf as $aacf) {
                            $aacf_meta = maybe_unserialize($aacf->post_content);
                            if ($like) {
                                $pos_key = stripos($aacf->post_excerpt, $like);
                                $pos_name = stripos($aacf->post_title, $like);
                                if ($pos_key === false && $pos_name === false) {
                                    continue;
                                }
                            }
                            $userMetas[$aacf->post_excerpt] = $aacf->post_title . ' [' . $aacf_meta['type'] . ']';
                            $userMetasGrouped['ACF'][$aacf->post_excerpt] = $userMetas[$aacf->post_excerpt];
                        }
                    }
                    //echo '<pre>';var_dump($aacf_meta);echo '</pre>';
                }
            }
        }

        // PODS
        /* $pods = get_posts(array('post_type' => '_pods_field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
          if (!empty($pods)) {
          foreach ($pods as $apod) {
          $type = get_post_meta($apod->ID, 'type', true);
          $userMetas[$apod->post_name] = $apod->post_title.' ['.$type.']';
          $userMetasGrouped['PODS'][$apod->post_name] = $userMetas[$apod->post_name];
          }
          } */

        // TOOLSET
        /* $toolset = get_option('wpcf-fields', false);
          if ($toolset) {
          $toolfields = maybe_unserialize($toolset);
          if (!empty($toolfields)) {
          foreach ($toolfields as $atool) {
          $userMetas[$atool['meta_key']] = $atool['name'].' ['.$atool['type'].']';
          $userMetasGrouped['TOOLSET'][$atool['meta_key']] = $userMetas[$atool['meta_key']];
          }
          }
          } */

        // MANUAL
        global $wpdb;
        $query = 'SELECT DISTINCT meta_key FROM ' . $wpdb->prefix . 'usermeta';
        if ($like) {
            $query .= " WHERE meta_key LIKE '%".$like."%'";
        }
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            $metas = array();
            foreach ($results as $key => $auser) {
                $metas[$auser->meta_key] = $auser->meta_key;
            }
            ksort($metas);
            //$manual_metas = array_diff_key($metas, $userMetas);
            $manual_metas = $metas;
            foreach ($manual_metas as $ameta) {
                if (substr($ameta, 0, 1) == '_') {
                    $ameta = $tmp = substr($ameta, 1);
                    if (in_array($tmp, $manual_metas)) {
                        continue;
                    }
                }
                if (!isset($postMetas[$ameta])) {
                    $userMetas[$ameta] = $ameta;
                    $userMetasGrouped['META'][$ameta] = $ameta;
                }
            }
        }

        //$userMetas = get_user_meta();
        //var_dump($userMetas); die();
        //$userMetasGrouped['NATIVE'] = $userMetas;

        if ($grouped) {
            return $userMetasGrouped;
        }

        return $userMetas;
    }
    
    public static function get_term_metas($grouped = false, $like = '') {
        global $wp_meta_keys;

        $termMetas = $termMetasGrouped = array();

        // ACF
        $acf_groups = get_posts(array('post_type' => 'acf-field-group', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
        if (!empty($acf_groups)) {
            foreach ($acf_groups as $aacf_group) {
                $is_term_group = false;
                $aacf_meta = maybe_unserialize($aacf_group->post_content);
                if (!empty($aacf_meta['location'])) {
                    foreach ($aacf_meta['location'] as $gkey => $gvalue) {
                        foreach ($gvalue as $rkey => $rvalue) {
                            if (substr($rvalue['param'], 0, 5) == 'term_') {
                                $is_term_group = true;
                            }
                        }
                    }
                }
                if ($is_term_group) {
                    $acf = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1, 'post_status' => 'publish', 'post_parent' => $aacf_group->ID, 'suppress_filters' => false));
                    if (!empty($acf)) {
                        foreach ($acf as $aacf) {
                            $aacf_meta = maybe_unserialize($aacf->post_content);
                            if ($like) {
                                $pos_key = stripos($aacf->post_excerpt, $like);
                                $pos_name = stripos($aacf->post_title, $like);
                                if ($pos_key === false && $pos_name === false) {
                                    continue;
                                }
                            }
                            $termMetas[$aacf->post_excerpt] = $aacf->post_title . ' [' . $aacf_meta['type'] . ']';
                            $termMetasGrouped['ACF'][$aacf->post_excerpt] = $userMetas[$aacf->post_excerpt];
                        }
                    }
                    //echo '<pre>';var_dump($aacf_meta);echo '</pre>';
                }
            }
        }

        // PODS
        /* $pods = get_posts(array('post_type' => '_pods_field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
          if (!empty($pods)) {
          foreach ($pods as $apod) {
          $type = get_post_meta($apod->ID, 'type', true);
          $userMetas[$apod->post_name] = $apod->post_title.' ['.$type.']';
          $userMetasGrouped['PODS'][$apod->post_name] = $userMetas[$apod->post_name];
          }
          } */

        // TOOLSET
        /* $toolset = get_option('wpcf-fields', false);
          if ($toolset) {
          $toolfields = maybe_unserialize($toolset);
          if (!empty($toolfields)) {
          foreach ($toolfields as $atool) {
          $userMetas[$atool['meta_key']] = $atool['name'].' ['.$atool['type'].']';
          $userMetasGrouped['TOOLSET'][$atool['meta_key']] = $userMetas[$atool['meta_key']];
          }
          }
          } */

        // MANUAL
        global $wpdb;
        $query = 'SELECT DISTINCT meta_key FROM ' . $wpdb->prefix . 'termmeta';
        if ($like) {
            $query .= " WHERE meta_key LIKE '%".$like."%'";
        }
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            $metas = array();
            foreach ($results as $key => $aterm) {
                $metas[$aterm->meta_key] = $aterm->meta_key;
            }
            ksort($metas);
            //$manual_metas = array_diff_key($metas, $userMetas);
            $manual_metas = $metas;
            foreach ($manual_metas as $ameta) {
                if (substr($ameta, 0, 1) == '_') {
                    $ameta = $tmp = substr($ameta, 1);
                    if (in_array($tmp, $manual_metas)) {
                        continue;
                    }
                }
                if (!isset($postMetas[$ameta])) {
                    $termMetas[$ameta] = $ameta;
                    $termMetasGrouped['META'][$ameta] = $ameta;
                }
            }
        }

        if ($grouped) {
            return $termMetasGrouped;
        }

        return $termMetas;
    }

    public static function get_acf_types() {
        return array("text", "textarea", "number", "range", "email", "url", "password", "image", "file", "wysiwyg", "oembed", "gallery", "select", "checkbox", "radio", "button_group", "true_false", "link", "post_object", "page_link", "relationship", "taxonomy", "user", "google_map", "date_picker", "date_time_picker", "time_picker", "color_picker", "message", "accordion", "tab", "group", "repeater", "flexible_content", "clone");
    }

    public static function get_pods_types() {
        return array("text", "website", "phone", "email", "password", "paragraph", "wysiwyg", "code", "datetime", "date", "time", "number", "currency", "file", "oembed", "pick", "boolean", "color");
    }

    public static function get_toolset_types() {
        return array("audio", "colorpicker", "email", "embed", "file", "image", "numeric", "phone", "textarea", "textfield", "url", "video", "checkboxes", "checkbox", "date", "radio", "select", "skype", "wysiwyg", "multiple");
    }

    public static function get_post_meta($post_id, $meta_key, $fallback = true, $single = false) {
        $meta_value = false;

        if ($fallback) {
            $meta_value = get_post_meta($post_id, $meta_key, $single);
        }

        // https://docs.elementor.com/article/381-elementor-integration-with-acf
        if (DCE_Helper::is_plugin_active('acf')) { // && in_array($original_type, $acf_types)) {
            //var_dump(array_keys(self::get_acf_fields(false, array('repeater', 'group'))));
            $acf_fields = self::get_acf_fields();
            //echo $meta_key; var_dump($acf_fields);
            if (!empty($acf_fields) && array_key_exists($meta_key, $acf_fields)) {
                // https://www.advancedcustomfields.com/resources/
                $meta_value = get_field($meta_key, $post_id);
                //var_dump($meta_value);
                //$render_type = 'plugin';
            }
        }

        // https://docs.elementor.com/article/385-elementor-integration-with-pods
        if (DCE_Helper::is_plugin_active('pods')) { // && in_array($original_type, $pods_types)) {
            $pods_fields = array_keys(self::get_pods_fields());
            //var_dump($pods_fields);
            //var_dump(in_array($meta_key, $pods_fields));
            if (!empty($pods_fields) && in_array($meta_key, $pods_fields, true)) {
                $meta_value = pods_field_display($meta_key, $post_id);
                //$render_type = 'plugin';
            }
        }

        // ToolSet
        if (DCE_Helper::is_plugin_active('wpcf')) { // && in_array($original_type, $toolset_types)) {
            $toolset_fields = array_keys(self::get_toolset_fields());
            if (!empty($toolset_fields) && in_array($meta_key, $toolset_fields, true)) {
                //$meta_value = types_render_field($meta_key, array('post_id' => $post_id));
                //$render_type = 'plugin';
            }
        }

        //var_dump($meta_value);
        return $meta_value;
    }

    public static function get_post_meta_name($meta_key) {

      // ACF
      if (self::is_plugin_active('acf')) {
          $acf = get_field_object($meta_key);
          if ($acf) {
              return $acf['label'];
          }
      }


      // PODS
      if (self::is_plugin_active('pods')) {
          //$pods = PODS::label($meta_key);
          $pods = get_page_by_path($meta_key, OBJECT, '_pods_field');
          if ($pods) {
              return $pods->post_title;
          }
      }

      /*
      // TOOLSET
      $toolset = get_option('wpcf-fields', false);
      if ($toolset) {
          $toolfields = maybe_unserialize($toolset);
          if (!empty($toolfields)) {
              foreach ($toolfields as $atool) {
                  $postMetas[$atool['meta_key']] = $atool['name'].' ['.$atool['type'].']';
                  $postMetasGrouped['TOOLSET'][$atool['meta_key']] = $postMetas[$atool['meta_key']];
              }
          }
      }*/

      return $meta_key;
    }

    public static function get_meta_type($meta_key, $meta_value = null) {

        $meta_type = 'text';

        // ACF
        if (self::is_plugin_active('acf')) {
            global $wpdb;
            $sql = "SELECT post_content FROM " . $wpdb->prefix . "posts WHERE post_excerpt = '" . $meta_key . "' AND post_type = 'acf-field';";
            $acf_result = $wpdb->get_col($sql);
            if (!empty($acf_result)) {
                $acf_content = reset($acf_result);
                $acf_field_object = maybe_unserialize($acf_content);
                //$acf = get_field_object($meta_key);
                if ($acf_field_object && is_array($acf_field_object) && isset($acf_field_object['type'])) {
                    $meta_type = $acf_field_object['type'];
                    //return $meta_type;
                }
            }
        }

        // PODS
        if (self::is_plugin_active('pods')) {
            //$pods = PODS::label($meta_key);
            $pods = get_page_by_path($meta_key, OBJECT, '_pods_field');
            if ($pods) {
                $meta_type = get_post_meta($apod->ID, 'type', true);
                //return $meta_type;
            }
        }

        /*
          // TOOLSET
          $toolset = get_option('wpcf-fields', false);
          if ($toolset) {
          $toolfields = maybe_unserialize($toolset);
          if (!empty($toolfields)) {
          foreach ($toolfields as $atool) {
          $postMetas[$atool['meta_key']] = $atool['name'].' ['.$atool['type'].']';
          $postMetasGrouped['TOOLSET'][$atool['meta_key']] = $postMetas[$atool['meta_key']];
          }
          }
          } */

        if ($meta_value) {
            if ($meta_type != 'text') {
                switch ($meta_type) {
                    case 'gallery':
                        return 'image';

                    case 'embed':
                        if (strpos($meta_value, 'https://www.youtube.com/') !== false || strpos($meta_value, 'https://youtu.be/') !== false) {
                            return 'youtube';
                        }

                    default:
                        return $meta_type;
                }
            } else {
                if ($meta_key == 'avatar') {
                    return 'image';
                }

                if (is_numeric($meta_value)) {
                    return 'number';
                }
                // Validate e-mail
                if (filter_var($meta_value, FILTER_VALIDATE_EMAIL) !== false) {
                    return 'email';
                }

                // Youtube url
                if (is_string($meta_value)) {
                    if (strpos($meta_value, 'https://www.youtube.com/') !== false || strpos($meta_value, 'https://youtu.be/') !== false) {
                        return 'youtube';
                    }
                    $ext = pathinfo($meta_value, PATHINFO_EXTENSION);
                    if (in_array($ext, array('mp3', 'm4a', 'ogg', 'wav', 'wma')) === true) {
                        return 'audio';
                    }
                    if (in_array($ext, array('mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv')) === true) {
                        return 'video';
                    }

                    // Validate url
                    if (filter_var($meta_value, FILTER_SANITIZE_URL) !== false) {
                        //return 'url';
                    }
                    if (substr($meta_value, 0, 7) == 'http://' || substr($meta_value, 0, 8) == 'https://') {
                        return 'url';
                    }
                }
            }
        }

        return $meta_type;
    }

    public static function get_user_meta($user_id, $meta_key, $fallback = true, $single = false) {
        $meta_value = false;

        if ($fallback) {
            $meta_value = get_post_meta($user_id, $meta_key, $single);
        }

        // https://docs.elementor.com/article/381-elementor-integration-with-acf
        if (DCE_Helper::is_plugin_active('acf')) { // && in_array($original_type, $acf_types)) {
            //var_dump(array_keys(self::get_acf_fields(array('repeater', 'group'))));
            $acf_fields = array_keys(self::get_acf_fields());
            //var_dump($acf_fields);
            if (!empty($acf_fields) && in_array($meta_key, $acf_fields, true)) {
                // https://www.advancedcustomfields.com/resources/
                $meta_value = get_field($meta_key, 'user_' . $user_id);
                //var_dump($meta_value);
                //$render_type = 'plugin';
            }
        }

        // https://docs.elementor.com/article/385-elementor-integration-with-pods
        if (DCE_Helper::is_plugin_active('pods')) { // && in_array($original_type, $pods_types)) {
            $pods_fields = array_keys(self::get_pods_fields());
            //var_dump($pods_fields);
            //var_dump(in_array($meta_key, $pods_fields));
            if (!empty($pods_fields) && in_array($meta_key, $pods_fields, true)) {
                $meta_value = pods_field_display($meta_key, $user_id);
                //$render_type = 'plugin';
            }
        }

        // ToolSet
        if (DCE_Helper::is_plugin_active('wpcf')) { // && in_array($original_type, $toolset_types)) {
            $toolset_fields = array_keys(self::get_toolset_fields());
            if (!empty($toolset_fields) && in_array($meta_key, $toolset_fields, true)) {
                //$meta_value = types_render_field($meta_key, array('user_id' => $user_id));
                //$render_type = 'plugin';
            }
        }

        //var_dump($meta_value);
        return $meta_value;
    }

    public static function get_post_metas($grouped = false, $like = '') {
        global $wp_meta_keys;

        $postMetas = $postMetasGrouped = array();

        // REGISTERED in FUNCTION
        $cpts = self::get_post_types();
        foreach ($cpts as $ckey => $cvalue) {
            $cpt_metas = get_registered_meta_keys($ckey);
            if (!empty($cpt_metas)) {
                foreach ($cpt_metas as $fkey => $actpmeta) {
                    if ($like) {
                        $pos_key = stripos($fkey, $like);
                        if ($pos_key === false) {
                            continue;
                        }
                    }
                    $postMetas[$fkey] = $fkey . ' [' . $actpmeta['type'] . ']';
                    $postMetasGrouped['CPT_' . $ckey][$fkey] = $fkey . ' [' . $actpmeta['type'] . ']';
                    //$postMetasGrouped['CPT_'.$ckey]['label'] = strtoupper($ckey);
                    //$postMetasGrouped['CPT_'.$ckey]['options'][$fkey] = $fkey.' ['.$actpmeta['type'].']';
                }
            }
        }

        // ACF
        if (self::is_plugin_active('acf')) {
            $acf = self::get_acf_fields(); //$grouped);
            // $acf = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
            if (!empty($acf)) {
                foreach ($acf as $acfkey => $aacf) {
                    //$aacf_meta = maybe_unserialize($aacf->post_content);
                    //$postMetas[$aacf->post_excerpt] = $aacf->post_title.' ['.$aacf_meta['type'].']';
                    //$postMetasGrouped['ACF'][$aacf->post_excerpt] = $postMetas[$aacf->post_excerpt];
                    if ($acfkey) {
                        if ($like) {
                            $pos_key = stripos($acfkey, $like);
                            $pos_name = stripos($aacf, $like);
                            if ($pos_key === false && $pos_name === false) {
                                continue;
                            }
                        }
                        $postMetas[$acfkey] = $aacf;
                        $postMetasGrouped['ACF'][$acfkey] = $aacf;
                    }
                }
            }
        }

        // PODS
        if (self::is_plugin_active('pods')) {
            $pods = get_posts(array('post_type' => '_pods_field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
            if (!empty($pods)) {
                foreach ($pods as $apod) {
                    $type = get_post_meta($apod->ID, 'type', true);
                    $postMetas[$apod->post_name] = $apod->post_title . ' [' . $type . ']';
                    $postMetasGrouped['PODS'][$apod->post_name] = $postMetas[$apod->post_name];
                }
            }
        }

        // TOOLSET
        if (self::is_plugin_active('wpcf')) {
            $toolset = get_option('wpcf-fields', false);
            if ($toolset) {
                $toolfields = maybe_unserialize($toolset);
                if (!empty($toolfields)) {
                    foreach ($toolfields as $atool) {
                        $postMetas[$atool['meta_key']] = $atool['name'] . ' [' . $atool['type'] . ']';
                        $postMetasGrouped['TOOLSET'][$atool['meta_key']] = $postMetas[$atool['meta_key']];
                    }
                }
            }
        }

        // MANUAL
        global $wpdb;
        $query = 'SELECT DISTINCT meta_key FROM ' . $wpdb->prefix . 'postmeta';
        if ($like) {
            $query .= " WHERE meta_key LIKE '%".$like."%'";
        }
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            $metas = array();
            foreach ($results as $key => $apost) {
                $metas[$apost->meta_key] = $apost->meta_key;
            }
            ksort($metas);
            $manual_metas = array_diff_key($metas, $postMetas);
            foreach ($manual_metas as $ameta) {
                if (substr($ameta, 0, 8) == '_oembed_') {
                    continue;
                }
                /* if (substr($ameta, 0, 1) == '_') {
                  $ameta = $tmp = substr($ameta, 1);
                  if (in_array($tmp, $manual_metas)) {
                  continue;
                  }
                  } */
                if (!isset($postMetas[$ameta])) {
                    $postMetas[$ameta] = $ameta;
                    $postMetasGrouped['NATIVE'][$ameta] = $ameta;
                }
            }
        }

        if ($grouped) {
            return $postMetasGrouped;
        }

        return $postMetas;
    }

    public static function get_post_fields($meta = false, $group = false) {
        $postFieldsKey = array();
        $postTmp = get_post();
        if ($postTmp) {
            $postProp = array();
            $postPropAll = get_object_vars($postTmp);
            if (!empty($meta) && is_string($meta)) {
                foreach ($postPropAll as $key => $value) {
                    $pos_key = stripos($value, $meta);
                    $pos_name = stripos($key, $meta);
                    if ($pos_key === false && $pos_name === false) {
                        continue;
                    }
                    $postProp[$key] = $value;
                }
            } else {
                $postProp = $postPropAll;
            }
            //$postMeta = get_registered_meta_keys('post');
            //$postFields = array_merge(array_keys($postProp), array_keys($postMeta));

            if ($meta) {
                $metas = self::get_post_metas($group, (is_string($meta)) ? $meta : null);
                $postFieldsKey = $metas;
            }

            $postFields = array_keys($postProp);
            if (!empty($postFields)) {
                foreach ($postFields as $value) {
                    $name = str_replace('post_', '', $value);
                    $name = str_replace('_', ' ', $name);
                    $name = ucwords($name);
                    if ($group) {
                        $postFieldsKey['POST'][$value] = $name;
                    } else {
                        $postFieldsKey[$value] = $name;
                    }
                }
                if ($group) {
                    $postFieldsKey = array_merge(['POST' => $postFieldsKey['POST']], $postFieldsKey); // in first position
                }
            }
        }
        //var_dump($postFieldsKey); die();
        return $postFieldsKey;
    }

    public static function is_post_meta($meta_name = null) {
        $post_fields = array(
            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content',
            'post_title',
            'post_excerpt',
            'post_status',
            'comment_status',
            'ping_status',
            'post_password',
            'post_name',
            'to_ping',
            'pinged',
            'post_modified',
            'post_modified_gmt',
            'post_content_filtered',
            'post_parent',
            'guid',
            'menu_order',
            'post_type',
            'post_mime_type',
            'comment_count',
        );

        if ($meta_name) {
            //$post_fields = self::get_post_fields();
            //var_dump($post_fields);
            if (in_array($meta_name, $post_fields)) { // || isset($post_fields[$meta_name])) {
                return false;
            }
        }
        return true;
    }

    public static function get_post_data($args) {
        $defaults = array(
            'posts_per_page' => 5,
            'offset' => 0,
            'category' => '',
            'category_name' => '',
            'orderby' => 'date',
            'order' => 'DESC',
            'include' => '',
            'exclude' => '',
            'meta_key' => '',
            'meta_value' => '',
            'post_type' => 'post',
            'post_mime_type' => '',
            'post_parent' => '',
            'author' => '',
            'author_name' => '',
            'post_status' => 'publish',
            'suppress_filters' => true
        );

        $atts = wp_parse_args($args, $defaults);

        $posts = get_posts($atts);

        return $posts;
    }

    public static function get_post_types($exclude = true) {
        $args = array(
            'public' => true
        );

        $skip_post_types = ['attachment', 'elementor_library', 'oceanwp_library'];

        $post_types = get_post_types($args);
        if ($exclude) {
            $post_types = array_diff($post_types, $skip_post_types);
        }
        foreach ($post_types as $akey => $acpt) {
            $cpt = get_post_type_object($acpt);
            //var_dump($cpt); die();
            $post_types[$akey] = $cpt->label;
        }
        return $post_types;
    }

    public static function get_pages() {
        $args = array(
            'sort_order' => 'desc',
            'sort_column' => 'menu_order',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        $listPage = [];
        foreach ($pages as $page) {
            //$option = '<option value="' . get_page_link( $page->ID ) . '">';
            //$option .= $page->post_title;
            //$option .= '</option>';
            //echo $option;
            $listPage[$page->ID] = $page->post_title;
        }

        return $listPage;
    }
    
    public static function get_post_terms( $post_id = 0, $taxonomy = null, $args = array() ) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        if ($taxonomy) {
            return wp_get_post_terms($post_id, $taxonomy, $args);
        }
        $post_terms = array();
        $post_taxonomies = get_taxonomies(array('public' => true));
        if (!empty($post_taxonomies)) {
            foreach ($post_taxonomies as $key => $atax) {
                $post_terms = array_merge($post_terms, wp_get_post_terms($post_id, $atax, $args));
            }
        }
        return $post_terms;
    }


    public static function get_taxonomies($dynamic = false, $cpt = '', $search = '') {
        $args = array(
                // 'public' => true,
                // '_builtin' => false
        );
        $output = 'objects'; // or objects
        $operator = 'and'; // 'and' or 'or'
        $taxonomies = get_taxonomies($args, $output, $operator);
        $listTax = [];
        $listTax[''] = 'None';
        if ($dynamic)
            $listTax['dynamic'] = 'Dynamic';
        if (!$cpt || $cpt == 'post') {
            $listTax['category'] = 'Categories posts (category)';
            $listTax['post_tag'] = 'Tags posts (post_tag)';
        }
        if ($taxonomies) {
            foreach ($taxonomies as $taxonomy) {
                if (!$cpt || in_array($cpt, $taxonomy->object_type)) {
                    //echo '<p>' . $taxonomy . '</p>';
                    $listTax[$taxonomy->name] = $taxonomy->label . ' (' . $taxonomy->name . ')';
                    //$listPage[$page->ID] = $page->post_title.$isparent;
                }
            }
        }
        
        if (!empty($search)) {
            $tmp = array();
            foreach ($listTax as $tkey => $atax) {
                $pos_key = stripos($tkey, $search);
                $pos_name = stripos($atax, $search);
                if ($pos_key !== false || $pos_name !== false) {
                    $tmp[$tkey] = $atax;
                }
            }
            $listTax = $tmp;
        }

        return $listTax;
    }

    public static function get_taxonomy_terms($taxonomy = null, $flat = false, $search = '') {
        $listTerms = [];
        $flatTerms = [];
        $listTerms[''] = 'None';
        $args = array('taxonomy' => $taxonomy,'hide_empty' => false);
        if ($search) {
            $args['name__like'] = $search;
        }
        if ($taxonomy) {            
            $terms = get_terms($args);
            if (!empty($terms)) {
                foreach ($terms as $aterm) {
                    $listTerms[$aterm->term_id] = $aterm->name . ' (' . $aterm->slug . ')';
                }
                $flatTerms = $listTerms;
            }
        } else {
            $taxonomies = self::get_taxonomies();
            foreach ($taxonomies as $tkey => $atax) {
                if ($tkey) {
                    $args['taxonomy'] = $tkey;
                    $terms = get_terms($args);
                    if (!empty($terms)) {//var_dump($terms); die();
                        $tmp = [];
                        $tmp['label'] = $atax;
                        //$listTerms[$tkey]['label'] = $atax;
                        foreach ($terms as $aterm) {
                            //$listTerms[$tkey]['options'][$aterm->term_id] = $aterm->name.' ('.$aterm->slug.')';
                            $tmp['options'][$aterm->term_id] = $aterm->name . ' (' . $aterm->slug . ')';
                            $flatTerms[$aterm->term_id] = $atax . ' > ' . $aterm->name . ' (' . $aterm->slug . ')';
                        }
                        $listTerms[] = $tmp;
                    }
                }
            }
        }
        if ($flat) {
            return $flatTerms;
        }
        //print_r($listTerms); die();
        return $listTerms;
    }

    public static function get_the_terms_ordered($post_id, $taxonomy) {
        //var_dump($post_id); var_dump($taxonomy);
        $terms = get_the_terms($post_id, $taxonomy);
        //var_dump($terms);
        $ret = array();
        if (!empty($terms)) {
            foreach ($terms as $term) {
                //$ret[$term->term_order] = (object)array(
                //var_dump($term);
                $ret[($term->term_order) ? $term->term_order : $term->slug] = (object) array(
                            "term_id" => $term->term_id,
                            "name" => $term->name,
                            "slug" => $term->slug,
                            "term_group" => $term->term_group,
                            "term_order" => $term->term_order,
                            "term_taxonomy_id" => $term->term_taxonomy_id,
                            "taxonomy" => $term->taxonomy,
                            "description" => $term->description,
                            "parent" => $term->parent,
                            "count" => $term->count,
                            "object_id" => $term->object_id
                );
            }
            ksort($ret);
            //$ret = (object) $ret;
            //var_dump($ret);
        } else {
            $ret = $terms;
        }
        return $ret;
    }
    public static function get_parentterms($tax) {
        $parentTerms = get_terms( $tax );
        $listTerm = [];
        $listTerm[0] = 'None';
        
        foreach ($parentTerms as $term_item) {
            $termChildren = get_term_children( $term_item->term_id, $tax );
            
            if (count($termChildren) > 0) $listTerm[$term_item->term_id] = $term_item->name;
        }
        return $listTerm;
    }
    public static function get_parentpages() {
        //
        $args = array(
            'sort_order' => 'DESC',
            'sort_column' => 'menu_order',
            'numberposts' => -1,
            // 'hierarchical' => 1,
            // 'exclude' => '',
            // 'include' => '',
            // 'meta_key' => '',
            // 'meta_value' => '',
            // 'authors' => '',
            // 'child_of' => 0,
            // 'parent' => -1,
            // 'exclude_tree' => '',
            // 'number' => '',
            // 'offset' => 0,
            'post_type' => self::get_types_registered(),
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        $listPage = [];

        foreach ($pages as $page) {

            $children = get_children('post_parent=' . $page->ID);
            $parents = get_post_ancestors($page->ID);
            $isparent = '';
            // !$parents &&
            if (count($children) > 0) {
                $isparent = ' (Parent)';
            }
            $listPage[$page->ID] = $page->post_title . $isparent;
        }

        return $listPage;
    }

    public static function get_post_settings($settings) {
        $post_args['post_type'] = $settings['post_type'];

        if ($settings['post_type'] == 'post') {
            $post_args['category'] = $settings['category'];
        }

        $post_args['posts_per_page'] = $settings['num_posts'];
        $post_args['offset'] = $settings['post_offset'];
        $post_args['orderby'] = $settings['orderby'];
        $post_args['order'] = $settings['order'];

        return $post_args;
    }

    public static function get_excerpt_by_id($post_id, $excerpt_length) {
        $the_post = get_post($post_id); //Gets post ID

        $the_excerpt = null;
        if ($the_post) {
            $the_excerpt = $the_post->post_excerpt ? $the_post->post_excerpt : $the_post->post_content;
        }

        // $the_excerpt = ($the_post ? $the_post->post_content : null);//Gets post_content to be used as a basis for the excerpt
        //echo $the_excerpt;
        $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
        $words = explode(' ', $the_excerpt, $excerpt_length + 1);

        if (count($words) > $excerpt_length) :
            array_pop($words);
            //array_push($words, '…');
            $the_excerpt = implode(' ', $words);
            $the_excerpt .= '...';  // Don't put a space before
        endif;

        return $the_excerpt;
    }

// ************************************** ALL POST SINGLE IN ALL REGISTER TYPE ***************************/
    public static function get_all_posts($myself = null, $group = false, $orderBy = 'title') {
        $args = array(
            'public' => true,
                //'_builtin' => false,
        );

        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'
        $posttype_all = get_post_types($args, $output, $operator);

        $type_excluded = array('elementor_library', 'oceanwp_library', 'ae_global_templates');
        $typesRegistered = array_diff($posttype_all, $type_excluded);
        // Return elementor templates array

        $templates[0] = 'None';

        $exclude_io = array();
        if (isset($myself) && $myself) {
            //echo 'ei: '.$settings['exclude_io'].' '.count($exclude_io);
            $exclude_io = array($myself);
        }

        $get_templates = get_posts(array('post_type' => $typesRegistered, 'numberposts' => -1, 'post__not_in' => $exclude_io, 'post_status' => 'publish', 'orderby' => $orderBy, 'order' => 'DESC'));

        if (!empty($get_templates)) {
            foreach ($get_templates as $template) {

                if ($group) {
                    $templates[$template->post_type]['options'][$template->ID] = $template->post_title;
                    $templates[$template->post_type]['label'] = $template->post_type;
                } else {
                    $templates[$template->ID] = $template->post_title;
                }
            }
        }

        return $templates;
    }

    public static function get_posts_by_type($typeId, $myself = null, $group = false) {


        $exclude_io = array();
        if (isset($myself) && $myself) {
            //echo 'ei: '.$settings['exclude_io'].' '.count($exclude_io);
            $exclude_io = array($myself);
        }
        $templates = array();
        $get_templates = get_posts(array('post_type' => $typeId, 'numberposts' => -1, 'post__not_in' => $exclude_io, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'DESC', 'suppress_filters' => false));

        if (!empty($get_templates)) {
            foreach ($get_templates as $template) {

                $templates[$template->ID] = $template->post_title;
            }
        }

        return $templates;
    }

    /**
     * Get Post object by post_meta query
     *
     * @use         $post = get_post_by_meta( array( meta_key = 'page_name', 'meta_value = 'contact' ) )
     * @since       1.0.4
     * @return      Object      WP post object
     */
    public static function get_post_by_meta($args = array()) {

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = (object) wp_parse_args($args);

        // grab page - polylang will take take or language selection ##
        $args = array(
            'meta_query' => array(
                array(
                    'key' => $args->meta_key,
                    'value' => $args->meta_value
                )
            ),
            'post_type' => $args->post_type, //'page',
            'posts_per_page' => '1'
        );
        //var_dump($args);
        // run query ##
        $posts = get_posts($args);

        // check results ##

        if (is_wp_error($posts)) {
            if (WP_DEBUG) {
                $error_string = $result->get_error_message();
                echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
            }
        }

        if (!$posts) {
            if (WP_DEBUG) {
                $error_string = _('No result founded');
                echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
            }
            return false;
        }

        // test it ##
        #pr( $posts[0] );
        // kick back results ##
        return reset($posts);
    }

    public static function get_types_registered() {
        $typesRegistered = get_post_types(array('public' => true), 'names', 'and');
        $type_esclusi = DCE_TemplateSystem::$supported_types;
        return array_diff($typesRegistered, $type_esclusi);
    }

// ************************************** ELEMENTOR ***************************/
    public static function get_all_template($def = null) {

        $type_template = array('elementor_library', 'oceanwp_library');

        // Return elementor templates array

        if ($def) {
            $templates[0] = 'Default';
            $templates[1] = 'NO';
        } else {
            $templates[0] = 'NO';
        }

        $get_templates = self::get_templates(); //get_posts(array('post_type' => $type_template, 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'DESC', 'suppress_filters' => false ));
        //print_r($get_templates);
        if (!empty($get_templates)) {
            foreach ($get_templates as $template) {
                $templates[$template['template_id']] = $template['title'] . ' (' . $template['type'] . ')';
                //$options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
                //$types[ $template['template_id'] ] = $template['type'];
            }
        }

        return $templates;
    }

    public static function get_thumbnail_sizes() {
        $sizes = get_intermediate_image_sizes();
        foreach ($sizes as $s) {
            $ret[$s] = $s;
        }

        return $ret;
    }
    
    public static function is_resized_image($imagePath) {
        $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
        $pezzi = explode('-', substr($imagePath, 0, -(strlen($ext) + 1)));
        //var_dump($pezzi);
        if (count($pezzi) > 1) {
            $misures = array_pop($pezzi);
            $fullsize = implode('-', $pezzi) . '.' . $ext;
            //echo $fullsize;
            $pezzi = explode('x', $misures);
            if (count($pezzi) == 2) {
                //var_dump($pezzi);
                if (is_numeric($pezzi[0]) && is_numeric($pezzi[1])) {
                    return $fullsize; // return original value
                }
            }
        }
        return false;
    }

    public static function get_post_orderby_options() {
        $orderby = array(
            'ID' => 'Post Id',
            'author' => 'Post Author',
            'title' => 'Title',
            'date' => 'Date',
            'modified' => 'Last Modified Date',
            'parent' => 'Parent Id',
            'rand' => 'Random',
            'comment_count' => 'Comment Count',
            'menu_order' => 'Menu Order',
            'meta_value_num' => 'Meta Value NUM',
            'meta_value_date' => 'Meta Value DATE',
        );

        return $orderby;
    }

    public static function get_placeholder_image_src($size = null) {
        $placeholder_image = DCE_URL . 'assets/img/placeholder.jpg';
        return $placeholder_image;
    }

    public static function get_anim_timingFunctions() {
        $tf_p = [
            'linear' => __('Linear', 'dynamic-content-for-elementor'),
            'ease' => __('Ease', 'dynamic-content-for-elementor'),
            'ease-in' => __('Ease In', 'dynamic-content-for-elementor'),
            'ease-out' => __('Ease Out', 'dynamic-content-for-elementor'),
            'ease-in-out' => __('Ease In Out', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.755, 0.05, 0.855, 0.06)' => __('easeInQuint', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.23, 1, 0.32, 1)' => __('easeOutQuint', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.86, 0, 0.07, 1)' => __('easeInOutQuint', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.6, 0.04, 0.98, 0.335)' => __('easeInCirc', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.075, 0.82, 0.165, 1)' => __('easeOutCirc', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.785, 0.135, 0.15, 0.86)' => __('easeInOutCirc', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.95, 0.05, 0.795, 0.035)' => __('easeInExpo', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.19, 1, 0.22, 1)' => __('easeOutExpo', 'dynamic-content-for-elementor'),
            'cubic-bezier(1, 0, 0, 1)' => __('easeInOutExpo', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.6, -0.28, 0.735, 0.045)' => __('easeInBack', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.175, 0.885, 0.32, 1.275)' => __('easeOutBack', 'dynamic-content-for-elementor'),
            'cubic-bezier(0.68, -0.55, 0.265, 1.55)' => __('easeInOutBack', 'dynamic-content-for-elementor'),
        ];
        return $tf_p;
    }

    /*
      easingSinusoidalInOut,
      easingQuadraticInOut,
      easingCubicInOut,
      easingQuarticInOut,
      easingQuinticInOut,
      easingCircularInOut,
      easingExponentialInOut.

      easingBackInOut

      easingElasticInOut

      easingBounceInOut
     */

    public static function get_kute_timingFunctions() {
        $tf_p = [
            'linear' => __('Linear', 'dynamic-content-for-elementor'),
            'easingSinusoidalIn' => 'easingSinusoidalIn',
            'easingSinusoidalOut' => 'easingSinusoidalOut',
            'easingSinusoidalInOut' => 'easingSinusoidalInOut',
            'easingQuadraticInOut' => 'easingQuadraticInOut',
            'easingCubicInOut' => 'easingCubicInOut',
            'easingQuarticInOut' => 'easingQuarticInOut',
            'easingQuinticInOut' => 'easingQuinticInOut',
            'easingCircularInOut' => 'easingCircularInOut',
            'easingExponentialInOut' => 'easingExponentialInOut',
            'easingSinusoidalInOut' => 'easingSinusoidalInOut',
            'easingBackInOut' => 'easingBackInOut',
            'easingElasticInOut' => 'easingElasticInOut',
            'easingBounceInOut' => 'easingBounceInOut',
        ];
        return $tf_p;
    }

    public static function get_gsap_ease() {
        $tf_p = [
            'easeNone' => __('None', 'dynamic-content-for-elementor'),
            'easeIn' => __('In', 'dynamic-content-for-elementor'),
            'easeOut' => __('Out', 'dynamic-content-for-elementor'),
            'easeInOut' => __('InOut', 'dynamic-content-for-elementor'),
        ];
        return $tf_p;
    }

    public static function get_gsap_timingFunctions() {
        $tf_p = [
            'Power0' => __('Linear', 'dynamic-content-for-elementor'),
            'Power1' => __('Power1', 'dynamic-content-for-elementor'),
            'Power2' => __('Power2', 'dynamic-content-for-elementor'),
            'Power3' => __('Power3', 'dynamic-content-for-elementor'),
            'Power4' => __('Power4', 'dynamic-content-for-elementor'),
            'SlowMo' => __(' SlowMo', 'dynamic-content-for-elementor'),
            'Back' => __('Back', 'dynamic-content-for-elementor'),
            'Elastic' => __('Elastic', 'dynamic-content-for-elementor'),
            'Bounce' => __('Bounce', 'dynamic-content-for-elementor'),
            'Circ' => __('Circ', 'dynamic-content-for-elementor'),
            'Expo' => __('Expo', 'dynamic-content-for-elementor'),
            'Sine' => __('Sine', 'dynamic-content-for-elementor'),
        ];
        return $tf_p;
    }

    public static function get_ease_timingFunctions() {
        $tf_p = [
            'linear' => __('Linear', 'dynamic-content-for-elementor'),
            'easeInQuad' => 'easeInQuad',
            'easeInCubic' => 'easeInCubic',
            'easeInQuart' => 'easeInQuart',
            'easeInQuint' => 'easeInQuint',
            'easeInSine' => 'easeInSine',
            'easeInExpo' => 'easeInExpo',
            'easeInCirc' => 'easeInCirc',
            'easeInBack' => 'easeInBack',
            'easeInElastic' => 'easeInElastic',
            'easeOutQuad' => 'easeOutQuad',
            'easeOutCubic' => 'easeOutCubic',
            'easeOutQuart' => 'easeOutQuart',
            'easeOutQuint' => 'easeOutQuint',
            'easeOutSine' => 'easeOutSine',
            'easeOutExpo' => 'easeOutExpo',
            'easeOutCirc' => 'easeOutCirc',
            'easeOutBack' => 'easeOutBack',
            'easeOutElastic' => 'easeOutElastic',
            'easeInOutQuad' => 'easeInOutQuad',
            'easeInOutCubic' => 'easeInOutCubic',
            'easeInOutQuart' => 'easeInOutQuart',
            'easeInOutQuint' => 'easeInOutQuint',
            'easeInOutSine' => 'easeInOutSine',
            'easeInOutExpo' => 'easeInOutExpo',
            'easeInOutCirc' => 'easeInOutCirc',
            'easeInOutBack' => 'easeInOutBack',
            'easeInOutElastic' => 'easeInOutElastic',
        ];
        return $tf_p;
    }

    public static function get_anim_in() {
        $anim = [
            [
                'label' => 'Fading',
                'options' => [
                    'fadeIn' => 'Fade In',
                    'fadeInDown' => 'Fade In Down',
                    'fadeInLeft' => 'Fade In Left',
                    'fadeInRight' => 'Fade In Right',
                    'fadeInUp' => 'Fade In Up',
                ],
            ],
            [
                'label' => 'Zooming',
                'options' => [
                    'zoomIn' => 'Zoom In',
                    'zoomInDown' => 'Zoom In Down',
                    'zoomInLeft' => 'Zoom In Left',
                    'zoomInRight' => 'Zoom In Right',
                    'zoomInUp' => 'Zoom In Up',
                ],
            ],
            [
                'label' => 'Bouncing',
                'options' => [
                    'bounceIn' => 'Bounce In',
                    'bounceInDown' => 'Bounce In Down',
                    'bounceInLeft' => 'Bounce In Left',
                    'bounceInRight' => 'Bounce In Right',
                    'bounceInUp' => 'Bounce In Up',
                ],
            ],
            [
                'label' => 'Sliding',
                'options' => [
                    'slideInDown' => 'Slide In Down',
                    'slideInLeft' => 'Slide In Left',
                    'slideInRight' => 'Slide In Right',
                    'slideInUp' => 'Slide In Up',
                ],
            ],
            [
                'label' => 'Rotating',
                'options' => [
                    'rotateIn' => 'Rotate In',
                    'rotateInDownLeft' => 'Rotate In Down Left',
                    'rotateInDownRight' => 'Rotate In Down Right',
                    'rotateInUpLeft' => 'Rotate In Up Left',
                    'rotateInUpRight' => 'Rotate In Up Right',
                ],
            ],
            [
                'label' => 'Attention Seekers',
                'options' => [
                    'bounce' => 'Bounce',
                    'flash' => 'Flash',
                    'pulse' => 'Pulse',
                    'rubberBand' => 'Rubber Band',
                    'shake' => 'Shake',
                    'headShake' => 'Head Shake',
                    'swing' => 'Swing',
                    'tada' => 'Tada',
                    'wobble' => 'Wobble',
                    'jello' => 'Jello',
                ],
            ],
            [
                'label' => 'Light Speed',
                'options' => [
                    'lightSpeedIn' => 'Light Speed In',
                ],
            ],
            [
                'label' => 'Specials',
                'options' => [
                    'rollIn' => 'Roll In',
                ],
            ]
        ];
        return $anim;
    }

    public static function get_anim_out() {
        $anim = [
            [
                'label' => 'Fading',
                'options' => [
                    'fadeOut' => 'Fade Out',
                    'fadeOutDown' => 'Fade Out Down',
                    'fadeOutLeft' => 'Fade Out Left',
                    'fadeOutRight' => 'Fade Out Right',
                    'fadeOutUp' => 'Fade Out Up',
                ],
            ],
            [
                'label' => 'Zooming',
                'options' => [
                    'zoomOut' => 'Zoom Out',
                    'zoomOutDown' => 'Zoom Out Down',
                    'zoomOutLeft' => 'Zoom Out Left',
                    'zoomOutRight' => 'Zoom Out Right',
                    'zoomOutUp' => 'Zoom Out Up',
                ],
            ],
            [
                'label' => 'Bouncing',
                'options' => [
                    'bounceOut' => 'Bounce Out',
                    'bounceOutDown' => 'Bounce Out Down',
                    'bounceOutLeft' => 'Bounce Out Left',
                    'bounceOutRight' => 'Bounce Out Right',
                    'bounceOutUp' => 'Bounce Out Up',
                ],
            ],
            [
                'label' => 'Sliding',
                'options' => [
                    'slideOutDown' => 'Slide Out Down',
                    'slideOutLeft' => 'Slide Out Left',
                    'slideOutRight' => 'Slide Out Right',
                    'slideOutUp' => 'Slide Out Up',
                ],
            ],
            [
                'label' => 'Rotating',
                'options' => [
                    'rotateOut' => 'Rotate Out',
                    'rotateOutDownLeft' => 'Rotate Out Down Left',
                    'rotateOutDownRight' => 'Rotate Out Down Right',
                    'rotateOutUpLeft' => 'Rotate Out Up Left',
                    'rotateOutUpRight' => 'Rotate Out Up Right',
                ],
            ],
            [
                'label' => 'Attention Seekers',
                'options' => [
                    'bounce' => 'Bounce',
                    'flash' => 'Flash',
                    'pulse' => 'Pulse',
                    'rubberBand' => 'Rubber Band',
                    'shake' => 'Shake',
                    'headShake' => 'Head Shake',
                    'swing' => 'Swing',
                    'tada' => 'Tada',
                    'wobble' => 'Wobble',
                    'jello' => 'Jello',
                ],
            ],
            [
                'label' => 'Light Speed',
                'options' => [
                    'lightSpeedOut' => 'Light Speed Out',
                ],
            ],
            [
                'label' => 'Specials',
                'options' => [
                    'rollOut' => 'Roll Out',
                ],
            ]
        ];
        return $anim;
    }

    public static function get_anim_open() {
        $anim_p = [
            'noneIn' => _x('None', 'Ajax Page', 'dynamic-content-for-elementor'),
            'enterFromFade' => _x('Fade', 'Ajax Page', 'dynamic-content-for-elementor'),
            'enterFromLeft' => _x('Left', 'Ajax Page', 'dynamic-content-for-elementor'),
            'enterFromRight' => _x('Right', 'Ajax Page', 'dynamic-content-for-elementor'),
            'enterFromTop' => _x('Top', 'Ajax Page', 'dynamic-content-for-elementor'),
            'enterFromBottom' => _x('Bottom', 'Ajax Page', 'dynamic-content-for-elementor'),
            'enterFormScaleBack' => _x('Zoom Back', 'Ajax Page', 'dynamic-content-for-elementor'),
            'enterFormScaleFront' => _x('Zoom Front', 'Ajax Page', 'dynamic-content-for-elementor'),
            'flipInLeft' => _x('Flip Left', 'Ajax Page', 'dynamic-content-for-elementor'),
            'flipInRight' => _x('Flip Right', 'Ajax Page', 'dynamic-content-for-elementor'),
            'flipInTop' => _x('Flip Top', 'Ajax Page', 'dynamic-content-for-elementor'),
            'flipInBottom' => _x('Flip Bottom', 'Ajax Page', 'dynamic-content-for-elementor'),
                //'flip' => _x( 'Flip', 'Ajax Page', 'dynamic-content-for-elementor' ),
                //'pushSlide' => _x( 'Push Slide', 'Ajax Page', 'dynamic-content-for-elementor' ),
        ];

        return $anim_p;
    }

    public static function get_anim_close() {
        $anim_p = [
            'noneOut' => _x('None', 'Ajax Page', 'dynamic-content-for-elementor'),
            'exitToFade' => _x('Fade', 'Ajax Page', 'dynamic-content-for-elementor'),
            'exitToLeft' => _x('Left', 'Ajax Page', 'dynamic-content-for-elementor'),
            'exitToRight' => _x('Right', 'Ajax Page', 'dynamic-content-for-elementor'),
            'exitToTop' => _x('Top', 'Ajax Page', 'dynamic-content-for-elementor'),
            'exitToBottom' => _x('Bottom', 'Ajax Page', 'dynamic-content-for-elementor'),
            'exitToScaleBack' => _x('Zoom Back', 'Ajax Page', 'dynamic-content-for-elementor'),
            'exitToScaleFront' => _x('Zoom Front', 'Ajax Page', 'dynamic-content-for-elementor'),
            'flipOutLeft' => _x('Flip Left', 'Ajax Page', 'dynamic-content-for-elementor'),
            'flipOutRight' => _x('Flip Right', 'Ajax Page', 'dynamic-content-for-elementor'),
            'flipOutTop' => _x('Flip Top', 'Ajax Page', 'dynamic-content-for-elementor'),
            'flipOutBottom' => _x('Flip Bottom', 'Ajax Page', 'dynamic-content-for-elementor'),
                //'flip' => _x( 'Flip', 'Ajax Page', 'dynamic-content-for-elementor' ),
                //'pushSlide' => _x( 'Push Slide', 'Ajax Page', 'dynamic-content-for-elementor' ),
        ];

        return $anim_p;
    }

    public static function get_roles($everyone = true) {
        $all_roles = wp_roles()->roles;
        //var_dump($all_roles); die();
        $ret = array();
        if ($everyone) {
            $ret['everyone'] = 'Everyone';
        }
        foreach ($all_roles as $key => $value) {
            $ret[$key] = $value['name'];
        }
        return $ret;
    }

    public static function get_current_user_role() {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $role = (array) $user->roles;
            return $role[0];
        } else {
            return false;
        }
    }
    
    public static function get_term_posts($term_id, $cpt = 'any') {
        $posts = array();
        $term = self::get_term_by('id', $term_id);
        //var_dump($term);
        if ($term) {
            /*
            $query = new \WP_Query( array(
                'post_type' => $cpt,  // Or your custom post type's slug
                'posts_per_page' => -1, // Do not paginate posts
                'tax_query' => array(
                    array(
                        'taxonomy' => $term->taxonomy,
                        'field' => 'term_id',
                        'value' => $term->term_id
                    )
                )
            ) );
            return $query->get_posts();             
            */
            
            $term_medias = get_posts(array(
                'post_type' => $cpt,
                'numberposts' => -1,
                'tax_query' => array(
                  array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'id',
                    'terms' => $term_id,
                    'include_children' => false
                  )
                )
            ));
            
            return $term_medias;
        }
        return $posts;        
    }

    public static function get_term_fields($meta = false, $group = false) {
        $termTmp = self::get_term_by('id', 1, 'category');
        if ($termTmp) {
            $termPropAll = get_object_vars($termTmp);
            if (!empty($meta) && is_string($meta)) {
                $termProp = array();
                foreach ($termPropAll as $key => $value) {
                    $pos_key = stripos($value, $meta);
                    $pos_name = stripos($key, $meta);
                    if ($pos_key === false && $pos_name === false) {
                        continue;
                    }
                    $termProp[$key] = $value;
                }
            } else {
                $termProp = $termPropAll;
            }

            if ($meta) {
                $metas = self::get_term_metas($group, (is_string($meta)) ? $meta : null);
                $termFieldsKey = $metas;
            }

            $termFields = array_keys($termProp);
            if (!empty($termFields)) {
                foreach ($termFields as $value) {
                    $name = str_replace('term_', '', $value);
                    $name = str_replace('_', ' ', $name);
                    $name = ucwords($name);
                    if ($group) {
                        $termFieldsKey['TERM'][$value] = $name;
                    } else {
                        $termFieldsKey[$value] = $name;
                    }
                }
            }

            if ($group) {
                $termFieldsKey = array_merge(['TERM' => $termFieldsKey['TERM']], $termFieldsKey); // in first position
            }
        }
        //var_dump($userFieldsKey); die();
        return $termFieldsKey;
    }
    
    public static function get_term_by($field = 'id', $value = 1, $taxonomy = '') {
        if ($field == 'id' || $field == 'term_id') {
            $term = get_term($value);
        } else {
            $term = get_term_by($field, $value, $taxonomy);
        }
        return $term;
    }

    public static function get_user_fields($meta = false, $group = false) {
        $userFieldsKey = array();
        $userTmp = wp_get_current_user();
        if ($userTmp) {
            $userProp = get_object_vars($userTmp);
            if (!empty($userProp['data'])) {
                $userPropAll = (array) $userProp['data'];
                $userProp = array();
                if (!empty($meta) && is_string($meta)) {
                    foreach ($userPropAll as $key => $value) {
                        $pos_key = stripos($value, $meta);
                        $pos_name = stripos($key, $meta);
                        if ($pos_key === false && $pos_name === false) {
                            continue;
                        }
                        $userProp[$key] = $value;
                    }
                } else {
                    $userProp = $userPropAll;
                }
            }
            //echo '<pre>';var_dump($userProp);echo '</pre>'; die();
            //$userMeta = get_registered_meta_keys('post');
            //$userFields = array_merge(array_keys($userProp), array_keys($userMeta));

            if ($meta) {
                $metas = self::get_user_metas($group, (is_string($meta)) ? $meta : null);
                $userFieldsKey = $metas;
            }

            $userFields = array_keys($userProp);
            if (!empty($userFields)) {
                foreach ($userFields as $value) {
                    $name = str_replace('user_', '', $value);
                    $name = str_replace('_', ' ', $name);
                    $name = ucwords($name);
                    if ($group) {
                        $userFieldsKey['USER'][$value] = $name;
                    } else {
                        $userFieldsKey[$value] = $name;
                    }
                }
            }

            $pos_key = is_string($meta) ? stripos('avatar', $meta) : false;
            if (empty($meta) || !is_string($meta) || $pos_key !== false) {
                if ($group) {
                    $userFieldsKey['USER']['avatar'] = 'Avatar';
                } else {
                    $userFieldsKey['avatar'] = 'Avatar';
                }
            }

            if ($group) {
                $userFieldsKey = array_merge(['USER' => $userFieldsKey['USER']], $userFieldsKey); // in first position
            }
        }
        //var_dump($userFieldsKey); die();
        return $userFieldsKey;
    }

    /* public static function get_user_fields($idUser = 1) {
      $userTmp = wp_get_current_user();
      //var_dump($userTmp);
      $userProp = get_object_vars($userTmp);
      $userMeta = get_registered_meta_keys('user');
      //var_dump($userMeta);
      $userFields = array_merge(array_keys($userProp), array_keys($userMeta));
      return $userFields;
      } */

    public static function is_user_meta($meta_name = null) {
        $user_fields = array(
            'ID',
            'user_login',
            'user_pass',
            'user_nicename',
            'user_email',
            'user_url',
            'user_registered',
            'user_activation_key',
            'user_status',
            'display_name',
            'locale',
            'syntax_highlighting',
            'avatar',
        );
        if ($meta_name) {
            //$post_fields = self::get_post_fields();
            //var_dump($post_fields);
            if (in_array($meta_name, $user_fields)) { // || isset($post_fields[$meta_name])) {
                return false;
            }
        }
        return true;
    }
    
    public static function is_term_meta($meta_name = null) {
        $term_fields = array(
            'term_id',
            'name',
            'slug',
            'term_group',
            'term_order',
        );
        if ($meta_name) {
            //$post_fields = self::get_post_fields();
            //var_dump($post_fields);
            if (in_array($meta_name, $term_fields)) { // || isset($post_fields[$meta_name])) {
                return false;
            }
        }
        return true;
    }

    /* public static function get_user_meta($idUser = 1) {
      $all_userMeta = get_user_meta($idUser);
      //$all_userMeta = get_metadata('user',1);
      //var_dump($all_userMeta); die();
      $ret['none'] = 'None';
      foreach ($all_userMeta as $key => $value) {
      $ret[$key] = $key; //$value;
      }
      return $ret;
      } */

    public static function get_acf_fields($types = array(), $group = false, $select = true) {

        $acfList = [];

        if (is_string($types)) {
            if ($types == 'dyncontel-acf') {
                $types = array(
                    'text',
                    'textarea',
                    'select',
                    'number',
                    'date_time_picker',
                    'date_picker',
                    'oembed',
                    'file',
                    'url',
                    'image',
                    'wysiwyg',
                    'true_false',
                );
            } else {
                $types = array($types);
            }
        }
        if ($select) {
            $acfList[0] = 'Select the Field';
        }

        $tipo = 'acf-field';
        $get_templates = get_posts(array('post_type' => $tipo, 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'suppress_filters' => false));

        if (!empty($get_templates)) {
            foreach ($get_templates as $template) {
                $gruppoAppartenenza = false;
                if ($template->post_parent) {
                    if ($gruppoAppartenenza = get_post($template->post_parent)) {
                        $gruppoAppartenenzaField = maybe_unserialize($gruppoAppartenenza->post_content);
                    }
                }
                $arrayField = maybe_unserialize($template->post_content);
                if (isset($arrayField['type']) && (empty($types) || in_array($arrayField['type'], $types))) {

                    if ($group && $gruppoAppartenenza) {

                        if (isset($gruppoAppartenenzaField['type']) && $gruppoAppartenenzaField['type'] == 'group') {
                            $acfList[$gruppoAppartenenza->post_excerpt]['options'][$gruppoAppartenenza->post_excerpt . '_' . $template->post_excerpt] = $template->post_title . ' [' . $template->post_excerpt . '] (' . $arrayField['type'] . ')';
                        } else {
                            $acfList[$gruppoAppartenenza->post_excerpt]['options'][$template->post_excerpt] = $template->post_title . ' [' . $template->post_excerpt . '] (' . $arrayField['type'] . ')';
                        }
                        $acfList[$gruppoAppartenenza->post_excerpt]['label'] = $gruppoAppartenenza->post_title;
                    } else {
                        if ($gruppoAppartenenza && isset($gruppoAppartenenzaField['type']) && $gruppoAppartenenzaField['type'] == 'group') {
                            $acfList[$gruppoAppartenenza->post_excerpt . '_' . $template->post_excerpt] = $template->post_title . ' [' . $template->post_excerpt . '] (' . $arrayField['type'] . ')'; //.$template->post_content; //post_name,
                        } else {
                            $acfList[$template->post_excerpt] = $template->post_title . ' [' . $template->post_excerpt . '] (' . $arrayField['type'] . ')'; //.$template->post_content; //post_name,
                        }
                    }
                }
            }
        }
        return $acfList;
    }

    public static function get_acf_field_urlfile($group = false) {
        return self::get_acf_fields(array('file', 'url'), $group);
    }

    public static function get_acf_field_relations() {
        return self::get_acf_fields('relationship', $group);
    }

    /* public static function get_acf_field_relational_post() {
      $acfList = [];
      $relational = array("post_object", "relationship"); //,"taxonomy","user");
      $acfList[0] = __('Select the Field', 'dynamic-content-for-elementor');
      $get_templates = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1));
      if (!empty($get_templates)) {
      foreach ($get_templates as $template) {
      $gruppoAppartenenza = $template->post_parent;
      $arrayField = maybe_unserialize($template->post_content);
      if (in_array($arrayField['type'], $relational)) {
      $acfList[$template->post_excerpt] = $template->post_title . ' (' . $arrayField['type'] . ')'; //.$template->post_content; //post_name,
      }
      }
      }
      return $acfList;
      } */

    public static function get_acf_field_relational_post() {
        return self::get_acf_fields(array("post_object", "relationship"));
    }

    public static function get_acf_repeater_fields($repeater_name) {
        $sub_fields = array();
        if (self::is_plugin_active('acf')) {
            $repeater_id = self::get_acf_field_id($repeater_name);
            $fields = get_posts(array('post_type' => "acf-field", 'numberposts' => -1, 'post_status' => 'publish', 'post_parent' => $repeater_id));
            if (!empty($fields)) {
                foreach ($fields as $key => $afield) {
                    $settings = maybe_unserialize($afield->post_content);
                    $settings['title'] = $afield->post_title;
                    $sub_fields[$afield->post_excerpt] = $settings;
                }
                
            }
        }
        return $sub_fields;
    }
    
    public static function get_acf_field_id($key) {
        global $wpdb;
        $query = 'SELECT ID FROM '. $wpdb->posts .' WHERE post_type LIKE "acf-field" AND post_excerpt LIKE "'.$key.'"';
        $result = $wpdb->get_var($query);
        if ($result) {
            return $result;
        }
        return false;
    }
    public static function get_acf_field_settings($key) {
        $field = self::get_acf_field_post($key);
        if ($field) {
            //var_dump($field);
            $settings = maybe_unserialize($field->post_content);
            return $settings;
        }
        return false;
    }
    public static function get_acf_field_post($key) {
        if(is_int($key)) return get_post($key);
        $field_id = self::get_acf_field_id($key);
        if ($field_id) {
            $field = get_post($field_id);
            //var_dump($field);
            return $field;
        }
        return false;
    }
    public static function get_pods_fields($t = null) {
        $podsList = [];
        $podsList[0] = __('Select the Field', 'dynamic-content-for-elementor');
        $pods = get_posts(array('post_type' => '_pods_field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
        if (!empty($pods)) {
            foreach ($pods as $apod) {
                $type = get_post_meta($apod->ID, 'type', true);
                if (!$t || $type == $t) {
                    $title = $apod->post_title;
                    if (!$t) {
                        $title .= ' [' . $type . ']';
                    }
                    $podsList[$apod->post_name] = $title;
                }
            }
        }
        return $podsList;
    }

    public static function get_toolset_fields($t = null) {
        $toolsetList = [];
        $toolsetList[0] = __('Select the Field', 'dynamic-content-for-elementor');
        $toolset = get_option('wpcf-fields', false);
        if ($toolset) {
            $toolfields = maybe_unserialize($toolset);
            if (!empty($toolfields)) {
                foreach ($toolfields as $atool) {
                    $type = $atool['type'];
                    if (!$t || $type == $t) {
                        $title = $atool['name'];
                        if (!$t) {
                            $title .= ' [' . $type . ']';
                        }
                        $toolsetList[$atool['meta_key']] = $title;
                    }
                }
            }
        }
        return $toolsetList;
    }

    public static function recursive_array_search($needle, $haystack, $currentKey = '') {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $nextKey = self::recursive_array_search($needle, $value, is_numeric($key) ? $currentKey . '[' . $key . ']' : $currentKey . '["' . $key . '"]');
                if ($nextKey) {
                    return $nextKey;
                }
            } else if ($value == $needle) {
                return is_numeric($key) ? $currentKey . '[' . $key . ']' : $currentKey . '["' . $key . '"]';
            }
        }
        return false;
    }

    public static function array_find_deep($array, $search, $keys = array()) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sub = self::array_find_deep($value, $search, array_merge($keys, array($key)));
                if (count($sub)) {
                    return $sub;
                }
            } elseif ($value === $search) {
                return array_merge($keys, array($key));
            }
        }
        return array();
    }
    public static function array_find_deep_value($array, $value, $key) {
        if (is_array($array)) {
            foreach ($array as $akey => $avalue) {
                if (is_array($avalue)) {
                    if (isset($avalue[$key]) && $value == $avalue[$key]) {
                        return $avalue;
                    }
                    $sub = self::array_find_deep_value($avalue, $value, $key);
                    if (!empty($sub)) {
                        return $sub;
                    }
                }
            }
        }
        return false;
    }

    public static function get_adjacent_post_by_id($in_same_term = false, $excluded_terms = '', $previous = true, $taxonomy = 'category', $post_id = null) {
        global $wpdb;

        if ((!$post = get_post($post_id)))
            return null;
        //var_dump($post);

        $current_post_date = $post->post_date;

        $adjacent = $previous ? 'previous' : 'next';
        $op = $previous ? '<' : '>';
        $join = '';
        $order = $previous ? 'DESC' : 'ASC';

        $where = $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish'", $current_post_date, $post->post_type);
        $sort = "ORDER BY p.post_date $order LIMIT 1";

        $query = "SELECT p.ID FROM $wpdb->posts AS p $join $where $sort";

        //echo $query;

        $result = $wpdb->get_var($query);
        if (null === $result)
            $result = '';

        if ($result)
            $result = get_post($result);

        return $result;
    }

    public static function path_to_url($dir) {
        $dirs = wp_upload_dir();
        $url = str_replace($dirs["basedir"], $dirs["baseurl"], $dir);
        $url = str_replace(ABSPATH, get_home_url(null, '/'), $url);
        //$url = urlencode($url);
        return $url;
    }

    public static function bootstrap_button_sizes() {
        return [
            'xs' => __('Extra Small', 'dynamic-content-for-elementor'),
            'sm' => __('Small', 'dynamic-content-for-elementor'),
            'md' => __('Medium', 'dynamic-content-for-elementor'),
            'lg' => __('Large', 'dynamic-content-for-elementor'),
            'xl' => __('Extra Large', 'dynamic-content-for-elementor'),
        ];
    }

    public static function bootstrap_styles() {
        return [
            '' => __('Default', 'dynamic-content-for-elementor'),
            'info' => __('Info', 'dynamic-content-for-elementor'),
            'success' => __('Success', 'dynamic-content-for-elementor'),
            'warning' => __('Warning', 'dynamic-content-for-elementor'),
            'danger' => __('Danger', 'dynamic-content-for-elementor'),
        ];
    }

    // @P mod
    public static function dce_dynamic_data($datasource = false, $fromparent = false) {

        global $global_ID;
        global $global_TYPE;
        global $in_the_loop;
        global $global_is;
        //
        global $product;
        global $post;
        //
        global $paged;

        $demoPage = get_post_meta(get_the_ID(), 'demo_id', true);
        //
        $id_page = ''; //get_the_ID();
        $type_page = '';
        //
        $original_global_ID = $global_ID; // <-----------------------------
        $original_post = $post; // <-----------------------------
        $original_product = $product;
        $original_paged = $paged;

        //
        // 1) ME-STESSO (naturale) - - - - - - - - - - - - - - - - - - - -
        $type_page = get_post_type();
        $id_page = self::get_rev_ID(get_the_ID(), $type_page);

        // ************************************
        $product = self::wooc_data(); //wc_get_product();
        //echo 'natural ...';

        if ($demoPage) {

            // 2) LA-DEMO  - - - - - - - - - - - - - - - - - - - -

            $type_page = get_post_type($demoPage);
            $id_page = $demoPage;
            // ************************************
            $product = self::wooc_data($id_page); //wc_get_product( $id_page );

            $post = get_post($id_page);
            //echo 'DEMO ...'.$id_page.' - '.$type_page;
        }
        if ($global_ID) {

            // 3) ME-STESSO (se in un template) - - - - - - - - - - - - - - - - - - - -

            $type_page = get_post_type($global_ID); //$global_TYPE;
            $id_page = self::get_rev_ID($global_ID, $type_page);
            // ************************************
            // if product noot exist $product

            $product = self::wooc_data($id_page); //wc_get_product( $id_page );
            $post = get_post($id_page);
            //echo 'global ... '.$id_page.' - '.$type_page;
        }
        if ($datasource) {

            // 4) UN'ALTRO-POST (other) - - - - - - - - - - - - - - - - - - -
            //$original_global_ID = $global_ID;

            $type_page = get_post_type($datasource);
            $id_page = self::get_rev_ID($datasource, $type_page);
            //
            $product = self::wooc_data($id_page); //wc_get_product( $id_page );
            $post = get_post($id_page);
            //
            //echo 'data source.. '.$id_page;
        }
        if ($fromparent) {
            // 5) PARENT (of current)  - - - - - - - - - - - - - - - - - - - -
            $type_page = $global_TYPE;
            $id_page = self::get_rev_ID($global_ID, $type_page);

            $the_parent = wp_get_post_parent_id($id_page);
            if ($the_parent != 0) {
                $type_page = get_post_type($the_parent);
                $id_page = self::get_rev_ID($the_parent, $type_page);
            } /* else {
              // the parent not exist
              $id_page = 0;
              $type_page = get_post_type($id_page);
              } */

            $product = self::wooc_data($id_page); //wc_get_product( $id_page );
            $post = get_post($id_page);
            //echo 'parent.. ('.$id_page.') ';
        }
        //echo $type_page;
        //
        //$global_ID = $id_page; // <-----------------------------


        $data = [
            'id' => $id_page, //number
            'global_id' => $original_global_ID,
            'type' => $type_page, //string
            'is' => $global_is, //string
            'block' => $in_the_loop   //boolean
        ];

        $global_ID = $original_global_ID; // <-----------------------------
        //if ($datasource) {
        $post = $original_post;
        if ($type_page != 'product')
            $product = $original_product;
        $paged = $original_paged;
        //}
        //
        return $data;
    }

    public static function wooc_data($idprod = null) {
        global $product;

        if (function_exists('is_product')) {

            if (isset($idprod)) {
                $product = wc_get_product($idprod);
            } else {
                $product = wc_get_product();
            }
        }
        if (empty($product))
            return;

        return $product;
    }

    public static function get_rev_ID($revid, $revtype) {
        $rev_id = apply_filters('wpml_object_id', $revid, $revtype, true);
        if (!$rev_id)
            return $revid;
        return $rev_id;
    }

    /* public static function memo_globalid() {
      global $global_ID;
      global $original_global_ID;
      $original_global_ID = $global_ID;
      } */
    /* public static function reset_globalid() {
      global $global_ID;
      global $original_global_ID;
      $global_ID = $original_global_ID;
      } */

    public static function get_acffield_filtred( $idField, $id_page = null, $format = true) {
        
        //
        $dataACFieldPost = self::get_acf_field_post($idField);
        //$field_type = $field_settings['type'];
        //var_dump(self::get_acf_field_post($idField));
        //var_dump($idField);
        //var_dump($dataACField);

        // VOGLIO CAPIRE IL TIPO DEL GENITORE

        if($dataACFieldPost){ 
            $parentID = $dataACFieldPost->post_parent;
            $field_settings = self::get_acf_field_settings($parentID);

            if( isset($field_settings['type']) && $field_settings['type'] == 'repeater' ){
                //echo $idField;
                //var_dump(acf_get_loop('previous'));
                return get_sub_field($idField); 
            }
        }
        //var_dump($field_settings);
        //post_parent
        
       
        //var_dump($is_sub_f);

        $theField = get_field( $idField, $id_page, $format);
        
        if (is_post_type_archive() || is_tax() || is_category() || is_tag() ) {
                
            $term = get_queried_object();
            $theField = get_field($idField, $term, $format);

        }else if( is_author() ){

            $author_id = get_the_author_meta('ID');
            $theField = get_field($idField, 'user_'. $author_id, $format);

        }

        if(DCE_Helper::in_the_loop()) $theField = get_field($idField , $id_page, $format);
        return $theField;
    }
    public static function get_templates() {
        return \Elementor\Plugin::instance()->templates_manager->get_source('local')->get_items([
                    'type' => ['section', 'archive', 'page', 'single'],
        ]);
    }

    public static function dce_numeric_posts_nav() {

        if (is_singular())
            return;

        global $wp_query;
        //var_dump($wp_query->max_num_pages);
        /** Stop execution if there's only 1 page */
        if ($wp_query->max_num_pages <= 1)
            return;

        $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
        $max = intval($wp_query->max_num_pages);

        $prev_arrow = is_rtl() ? 'fa fa-angle-right' : 'fa fa-angle-left';
        $next_arrow = is_rtl() ? 'fa fa-angle-left' : 'fa fa-angle-right';

        /** Add current page to the array */
        if ($paged >= 1)
            $links[] = $paged;

        /** Add the pages around the current page to the array */
        if ($paged >= 3) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }

        if (( $paged + 2 ) <= $max) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }

        echo '<div class="navigation posts-navigation"><ul class="page-numbers">' . "\n";

        /** Previous Post Link */
        if (get_previous_posts_link())
            printf('<li>%s</li>' . "\n", get_previous_posts_link());

        /** Link to first page, plus ellipses if necessary */
        if (!in_array(1, $links)) {
            $class = 1 == $paged ? ' class="current"' : '';

            printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');

            if (!in_array(2, $links))
                echo '<li>…</li>';
        }

        /** Link to current page, plus 2 pages in either direction if necessary */
        sort($links);
        foreach ((array) $links as $link) {
            $class = $paged == $link ? ' class="current"' : '';
            printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
        }

        /** Link to last page, plus ellipses if necessary */
        if (!in_array($max, $links)) {
            if (!in_array($max - 1, $links))
                echo '<li>…</li>' . "\n";

            $class = $paged == $max ? ' class="current"' : '';
            printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
        }

        /** Next Post Link */
        if (get_next_posts_link())
            printf('<li>%s</li>' . "\n", get_next_posts_link());

        echo '</ul></div>' . "\n";
    }

    /* -------------------- */

    public static function get_wp_link_page($i) {
        if (!is_singular() || is_front_page()) {
            return get_pagenum_link($i);
        }

        // Based on wp-includes/post-template.php:957 `_wp_link_page`.
        global $wp_rewrite;
        $ggg = self::dce_dynamic_data();
        $post = get_post();
        $query_args = [];
        $url = get_permalink($ggg['id']);

        if ($i > 1) {
            if ('' === get_option('permalink_structure') || in_array($post->post_status, ['draft', 'pending'])) {
                $url = add_query_arg('page', $i, $url);
            } elseif (get_option('show_on_front') === 'page' && (int) get_option('page_on_front') === $post->ID) {
                $url = trailingslashit($url) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
            } else {
                $url = trailingslashit($url) . user_trailingslashit($i, 'single_paged');
            }
        }

        if (is_preview()) {
            if (( 'draft' !== $post->post_status ) && isset($_GET['preview_id'], $_GET['preview_nonce'])) {
                $query_args['preview_id'] = wp_unslash($_GET['preview_id']);
                $query_args['preview_nonce'] = wp_unslash($_GET['preview_nonce']);
            }

            $url = get_preview_post_link($post, $query_args, $url);
        }

        return $url;
    }

    /* --------------------- */

    public static function get_next_pagination() {
        //global $paged;
        $paged = max(1, get_query_var('paged'), get_query_var('page'));

        if (empty($paged))
            $paged = 1;

        $link_next = self::get_wp_link_page($paged + 1);

        return $link_next;
    }

    public static function numeric_query_pagination($pages, $settings) {

        $icon_prevnext = str_replace('right', '', $settings['pagination_icon_prevnext']);
        $icon_firstlast = str_replace('right', '', $settings['pagination_icon_firstlast']);

        $range = (int) $settings['pagination_range'] - 1; //la quantità di numeri visualizzati alla volta
        $showitems = ($range * 2) + 1;

        $paged = max(1, get_query_var('paged'), get_query_var('page'));

        if (empty($paged))
            $paged = 1;

        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;

            if (!$pages) {
                $pages = 1;
            }
        }

        if (1 != $pages) {
            echo '<div class="dce-pagination">';

            //Progression
            if ($settings['pagination_show_progression'])
                echo '<span class="progression">' . $paged . ' / ' . $pages . '</span>';

            /* echo "<span>paged: ".$paged."</span>";
              echo "<span>range: ".$range."</span>";
              echo "<span>showitems: ".$showitems."</span>";
              echo "<span>pages: ".$pages."</span>"; */

            //First
            if ($settings['pagination_show_firstlast'])
                if ($paged > 2 && $paged > $range + 1 && $showitems < $pages)
                    echo '<a href="' . self::get_wp_link_page(1) . '" class="pagefirst"><i class="' . $icon_firstlast . 'left"></i> ' . __($settings['pagination_first_label'], 'dynamic-content-for-elementor' . '_texts') . '</a>';

            //Prev
            if ($settings['pagination_show_prevnext'])
                if ($paged > 1 && $showitems < $pages)
                    echo '<a href="' . self::get_wp_link_page($paged - 1) . '" class="pageprev"><i class="' . $icon_prevnext . 'left"></i> ' . __($settings['pagination_prev_label'], 'dynamic-content-for-elementor' . '_texts') . '</a>';

            //Numbers
            if ($settings['pagination_show_numbers'])
                for ($i = 1; $i <= $pages; $i++) {
                    if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                        echo ($paged == $i) ? "<span class=\"current\">" . $i . "</span>" : "<a href='" . self::get_wp_link_page($i) . "' class=\"inactive\">" . $i . "</a>";
                    }
                }

            //Next
            if ($settings['pagination_show_prevnext'])
                if ($paged < $pages && $showitems < $pages)
                    echo '<a href="' . self::get_wp_link_page($paged + 1) . '" class="pagenext">' . __($settings['pagination_next_label'], 'dynamic-content-for-elementor' . '_texts') . ' <i class="' . $icon_prevnext . 'right"></i></a>';
            //Last
            if ($settings['pagination_show_firstlast'])
                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages)
                    echo '<a href="' . self::get_wp_link_page($pages) . '" class="pagelast">' . __($settings['pagination_last_label'], 'dynamic-content-for-elementor' . '_texts') . ' <i class="' . $icon_firstlast . 'right"></i></a>';

            echo '</div>';
        }
    }

    public static function dir_to_array($dir, $hidden = false, $files = true) {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = self::dir_to_array($dir . DIRECTORY_SEPARATOR . $value, $hidden, $files);
                } else {
                    if ($files) {
                        if (substr($value, 0, 1) != '.') { // hidden file
                            $result[] = $value;
                        }
                    }
                }
            }
        }
        return $result;
    }

    public static function is_empty_dir($dirname) {
        if (!is_dir($dirname))
            return false;
        foreach (scandir($dirname) as $file) {
            if (!in_array($file, array('.', '..', '.svn', '.git')))
                return false;
        }
        return true;
    }

    /**
     * Function for including files
     *
     * @since 0.5.0
     */
    public static function file_include($file) {
        $path = DCE_PATH . $file;
        //echo $path;
        if (file_exists($path)) {
            include_once( $path );
        }
    }

    public static function get_settings_by_id($element_id = null, $post_id = null) {
        $settings = array();
        if (!$post_id) {
            $post_id = get_the_ID();
            if (!$post_id) {
                $post_id = $_GET['post'];
            }
        }
        $post_meta = json_decode(get_post_meta($post_id, '_elementor_data', true), true);
        if (!$element_id) {
            return $post_meta;
        }
        $keys_array = self::array_find_deep_value($post_meta, $element_id, 'id');
        if (isset($keys_array['settings'])) {
            return $keys_array['settings'];
        }
        return false;
        /*var_dump($keys_array);
        $keys = '["' . implode('"]["', $keys_array) . '"]';
        $keys = str_replace('["id"]', '["settings"]', $keys);
        eval("\$settings = \$post_meta" . $keys . ";");
        return $settings;*/
    }

    public static function set_all_settings_by_id($element_id = null, $settings = array(), $post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
            if (!$post_id) {
                $post_id = $_GET['post'];
            }
        }
        $post_meta = self::get_settings_by_id(null, $post_id);
        if ($element_id) {
            $keys_array = self::array_find_deep($post_meta, $element_id);
            $keys = '["' . implode('"]["', $keys_array) . '"]';
            $keys = str_replace('["id"]', '["settings"]', $keys);
            eval("\$post_meta" . $keys . " = \$settings;");
            array_walk_recursive($post_meta, function($v, $k) {
                $v = self::escape_json_string($v);
            });
        }
        $post_meta_prepared = json_encode($post_meta);
        $post_meta_prepared = wp_slash($post_meta_prepared);
        update_metadata('post', $post_id, '_elementor_data', $post_meta_prepared);
    }

    public static function set_settings_by_id($element_id, $key, $value = null, $post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
            if (!$post_id) {
                $post_id = $_GET['post'];
            }
        }
        $post_meta = self::get_settings_by_id(null, $post_id);
        $keys_array = self::array_find_deep($post_meta, $element_id);
        $keys = '["' . implode('"]["', $keys_array) . '"]';
        $keys = str_replace('["id"]', '["settings"]', $keys);
        if (is_null($value)) {
            eval("unset(\$post_meta" . $keys . "[\$key]);");
        } else {
            eval("\$post_meta" . $keys . "[\$key] = \$value;");
        }
        array_walk_recursive($post_meta, function($v, $k) {
            $v = self::escape_json_string($v);
        });
        $post_meta_prepared = json_encode($post_meta);
        $post_meta_prepared = wp_slash($post_meta_prepared);
        update_metadata('post', $post_id, '_elementor_data', $post_meta_prepared);
        return $post_id;
    }

    public static function set_dynamic_tag($editor_data) {
        if (is_array($editor_data)) {
            foreach ($editor_data as $key => $avalue) {
                $editor_data[$key] = self::set_dynamic_tag($avalue);
            }
            if (isset($editor_data['elType'])) {
                foreach ($editor_data['settings'] as $skey => $avalue) {
                    //if ($editor_data['type'] == 'text' || $editor_data['type'] == 'textarea') {
                    $editor_data['settings'][\Elementor\Core\DynamicTags\Manager::DYNAMIC_SETTING_KEY][$skey] = 'token';
                }
            }
        }
        return $editor_data;
    }

    public static function escape_json_string($value) {
        // # list from www.json.org: (\b backspace, \f formfeed)
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

    public static function get_sql_operators() {
        $compare = self::get_wp_meta_compare();
        //$compare["LIKE WILD"] = "LIKE %...%";
        $compare["IS NULL"] = "IS NULL";
        $compare["IS NOT NULL"] = "IS NOT NULL";
        return $compare;
    }

    public static function get_wp_meta_compare() {
        // meta_compare (string) - Operator to test the 'meta_value'. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP' or 'RLIKE'. Default value is '='.
        return array(
            "=" => "=",
            ">" => "&gt;",
            ">=" => "&gt;=",
            "<" => "&lt;",
            "<=" => "&lt;=",
            "!=" => "!=",
            "LIKE" => "LIKE",
            "RLIKE" => "RLIKE",
            /*
              "E" => "=",
              "GT" => "&gt;",
              "GTE" => "&gt;=",
              "LT" => "&lt;",
              "LTE" => "&lt;=",
              "NE" => "!=",
              "LIKE_WILD" => "LIKE %...%",
             */
            "NOT LIKE" => "NOT LIKE",
            "IN" => "IN (...)",
            "NOT IN" => "NOT IN (...)",
            "BETWEEN" => "BETWEEN",
            "NOT BETWEEN" => "NOT BETWEEN",
            "NOT EXISTS" => "NOT EXISTS",
            "REGEXP" => "REGEXP",
            "NOT REGEXP" => "NOT REGEXP",
        );
    }

    public static function get_post_stati() {
        return array(
            'published' => __('Published'),
            'future' => __('Future'),
            'draft' => __('Draft'),
            'pending' => __('Pending'),
            'private' => __('Private'),
            'trash' => __('Trash'),
            'auto-draft' => __('Auto-Draft'),
            'inherit' => __('Inherit'),
        );
    }

    public static function get_post_value($post_id = null, $field = 'ID') {
        $postValue = null;

        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if ($field == 'permalink' || $field == 'get_permalink') {
            $postValue = get_permalink($post_id);
        }

        if ($field == 'post_excerpt' || $field == 'excerpt') {
            //$postValue = get_the_excerpt($post_id);
            $post = get_post($post_id);
            $postValue = $post->post_excerpt;
        }

        if ($field == 'the_author' || $field == 'post_author' || $field == 'author') {
            $postValue = get_the_author();
        }

        if (in_array($field, array('thumbnail', 'post_thumbnail', 'thumb'))) {
            $postValue = get_the_post_thumbnail();
        }

        if (!$postValue) {
            if (property_exists('WP_Post', $field)) {
                $postTmp = get_post($post_id);
                $postValue = $postTmp->{$field};
            }
        }
        if (!$postValue) {
            if (property_exists('WP_Post', 'post_' . $field)) {
                $postTmp = get_post($post_id);
                if ($postTmp) {
                    $postValue = $postTmp->{'post_' . $field};
                }
            }
        }
        if (!$postValue) {
            if (metadata_exists('post', $post_id, $field)) {
                $postValue = get_post_meta($post_id, $field, true);
            }
        }
        if (!$postValue) { // fot meta created with Toolset plugin
            if (metadata_exists('post', $post_id, 'wpcf-' . $field)) {
                $postValue = get_post_meta($post_id, 'wpcf-' . $field, true);
            }
        }

        return $postValue;
    }

    public static function get_user_value($user_id = null, $field = 'display_name') {
        $metaValue = '';
        if ($user_id) {
            $userTmp = get_user_by('ID', $user_id);
            if ($userTmp) {
                //if (property_exists('WP_User', $metaKey[0])) {
                // campo nativo
                if (@$userTmp->data->{$field}) {
                    //$userTmp = get_user_by('ID', $user_id);
                    $metaValue = $userTmp->data->{$field};
                }
                if (!$metaValue) {
                    if (@$userTmp->data->{'user_' . $field}) {
                        //if (property_exists('WP_User', 'user_'.$metaKey[0])) {
                        //$userTmp = get_user_by('ID', $user_id);
                        $metaValue = $userTmp->data->{'user_' . $field};
                    }
                }
                // altri campi nativi
                if (!$metaValue) {
                    $userInfo = get_userdata($user_id);
                    if (@$userInfo->{$field}) {
                        $metaValue = $userInfo->{$field};
                    }
                    if (!$metaValue) {
                        if (@$userInfo->{'user_' . $field}) {
                            $metaValue = $userInfo->{'user_' . $field};
                        }
                    }
                }
                // campo meta
                if (!$metaValue) {
                    if (metadata_exists('user', $user_id, $field)) {
                        $metaValue = get_user_meta($user_id, $field, true);
                    }
                    if (!$metaValue) {
                        // meta from module user_registration
                        if (metadata_exists('user', $user_id, 'user_registration_' . $field)) {
                            $metaValue = get_user_meta($user_id, 'user_registration_' . $field, true);
                        }
                    }
                }
            }
        }
        return $metaValue;
    }

    public static function get_term_value($term = null, $field = 'name') {
        $termValue = null;

        if (!is_object($term)) {
            $term = self::get_term_by('id', $term);
        }

        if ($field == 'permalink' || $field == 'get_permalink' || $field == 'get_term_link' || $field == 'term_link') {
            $termValue = get_term_link($term);
        }

        if (!$termValue) {
            if (property_exists('WP_Term', $field)) {
                $termValue = $term->{$field};
            }
        }
        if (!$termValue) {
            if (property_exists('WP_Term', 'term_' . $field)) {
                $termValue = $term->{'term_' . $field};
            }
        }
        if (!$termValue) {
            if (metadata_exists('term', $term->term_id, $field)) {
                $termValue = get_term_meta($term->term_id, $field, true);
            }
        }
        if (!$termValue) { // fot meta created with Toolset plugin
            if (metadata_exists('term', $term->term_id, 'wpcf-' . $field)) {
                $termValue = get_term_meta($term->term_id, 'wpcf-' . $field, true);
            }
        }

        return $termValue;
    }

    public static function to_string($avalue) {
        if (!is_array($avalue) && !is_object($avalue)) {
            return $avalue;
        }
        if (is_object($avalue) && get_class($avalue) == 'WP_Term') {
            return $avalue->name;
        }
        if (is_object($avalue) && get_class($avalue) == 'WP_Post') {
            return $avalue->post_title;
        }
        if (is_object($avalue) && get_class($avalue) == 'WP_User') {
            return $avalue->display_name;
        }
        if (is_array($avalue)) {

            if (isset($avalue['post_title'])) {
                return $avalue['post_title'];
            }
            if (isset($avalue['display_name'])) {
                return $avalue['display_name'];
            }
            if (isset($avalue['name'])) {
                return $avalue['name'];
            }
            if (count($avalue) == 1) {
                return reset($avalue);
            }
            return print_r($avalue, true);
        }
        return '';
    }
    
    public static function get_post_link($post_id = null) {
        return get_permalink($post_id);
    }
    public static function get_user_link($user_id = null) {
        if (!$user_id) {
            $user_id = get_the_author_meta('ID');
        }
        return get_author_posts_url($user_id);
    }
    public static function get_term_link($term_id = null) {
        return get_term_link($term_id);
    }

    public static function str_to_array($delimiter, $string, $format = null) {
        $pieces = explode($delimiter, $string);
        $pieces = array_map('trim', $pieces);
        //$pieces = array_filter($pieces);
        $tmp = array();
        foreach ($pieces as $value) {
            if ($value != '') {
                $tmp[] = $value;
            }
        }
        $pieces = $tmp;
        if ($format) {
            $pieces = array_map($format, $pieces);
        }
        return $pieces;
    }

    public static function get_image_id($image_url) {
        global $wpdb;
        $sql = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE guid LIKE '%" . esc_sql($image_url) . "';";
        $attachment = $wpdb->get_col($sql);
        return reset($attachment);
    }

    /**
     * Get size information for all currently-registered image sizes.
     *
     * @global $_wp_additional_image_sizes
     * @uses   get_intermediate_image_sizes()
     * @return array $sizes Data for all currently-registered image sizes.
     */
    public static function get_image_sizes() {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach (get_intermediate_image_sizes() as $_size) {
            if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                $sizes[$_size]['width'] = get_option("{$_size}_size_w");
                $sizes[$_size]['height'] = get_option("{$_size}_size_h");
                $sizes[$_size]['crop'] = (bool) get_option("{$_size}_crop");
            } elseif (isset($_wp_additional_image_sizes[$_size])) {
                $sizes[$_size] = array(
                    'width' => $_wp_additional_image_sizes[$_size]['width'],
                    'height' => $_wp_additional_image_sizes[$_size]['height'],
                    'crop' => $_wp_additional_image_sizes[$_size]['crop'],
                );
            }
        }

        return $sizes;
    }

    /**
     * Get size information for a specific image size.
     *
     * @uses   get_image_sizes()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|array $size Size data about an image size or false if the size doesn't exist.
     */
    public static function get_image_size($size) {
        $sizes = self::get_image_sizes();

        if (isset($sizes[$size])) {
            return $sizes[$size];
        }

        return false;
    }

    /**
     * Get the width of a specific image size.
     *
     * @uses   get_image_size()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|string $size Width of an image size or false if the size doesn't exist.
     */
    public static function get_image_width($size) {
        if (!$size = self::get_image_size($size)) {
            return false;
        }

        if (isset($size['width'])) {
            return $size['width'];
        }

        return false;
    }

    /**
     * Get the height of a specific image size.
     *
     * @uses   get_image_size()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|string $size Height of an image size or false if the size doesn't exist.
     */
    public static function get_image_height($size) {
        if (!$size = get_image_size($size)) {
            return false;
        }

        if (isset($size['height'])) {
            return $size['height'];
        }

        return false;
    }

    public static function get_gravatar_styles() {
        $gravatar_images = array(
            '404' => '404 (empty with fallback)',
            'retro' => '8bit',
            'monsterid' => 'Monster (Default)',
            'wavatar' => 'Cartoon face',
            'indenticon' => 'The Quilt',
            'mp' => 'Mystery',
            'mm' => 'Mystery Man',
            'robohash' => 'RoboHash',
            'blank' => 'transparent GIF',
            'gravatar_default' => 'The Gravatar logo'
        );
        return $gravatar_images;
    }

    public static function get_post_formats() {
        return array('standard' => 'Standard', 'aside' => 'Aside', 'chat' => 'Chat', 'gallery' => 'Gallery', 'link' => 'Link', 'image' => 'Image', 'quote' => 'Quote', 'status' => 'Status', 'video' => 'Video', 'audio' => 'Audio');
    }

    public static function vc_strip_shortcodes($content) {
        //return $content;
        $tmp = $content;
        $tags = array('[/vc_', '[vc_', '[dt_', '[interactive_banner_2');
        foreach ($tags as $atag) {
            $pezzi = explode($atag, $tmp);
            if (count($pezzi) > 1) {
                $content_mod = '';
                foreach ($pezzi as $key => $value) {
                    $altro = explode(']', $value, 2);
                    $content_mod .= end($altro);
                }
                $tmp = $content_mod;
            }
        }
        return $tmp;
    }

    public static function array_to_groups($myarray) {
        $ret = array();
        if (!empty($myarray)) {
            foreach ($myarray as $mkey => $avalue) {
                ksort($avalue);
                $ret[$mkey]['label'] = $mkey;
                $ret[$mkey]['options'] = $avalue;
            }
        }
        return $ret;
    }

    public static function text_reduce($text, $length, $length_type, $finish) {
        $tokens = array();
        $out = '';
        $w = 0;

        // Divide the string into tokens; HTML tags, or words, followed by any whitespace
        // (<[^>]+>|[^<>\s]+\s*)
        preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $text, $tokens);
        foreach ($tokens[0] as $t) { // Parse each token
            if ($w >= $length && 'sentence' != $finish) { // Limit reached
                break;
            }
            if ($t[0] != '<') { // Token is not a tag
                if ($w >= $length && 'sentence' == $finish && preg_match('/[\?\.\!]\s*$/uS', $t) == 1) { // Limit reached, continue until ? . or ! occur at the end
                    $out .= trim($t);
                    break;
                }
                if ('words' == $length_type) { // Count words
                    $w++;
                } else { // Count/trim characters
                    if ($finish == 'exact_w_spaces') {
                        $chars = $t;
                    } else {
                        $chars = trim($t);
                    }
                    $c = mb_strlen($chars);
                    if ($c + $w > $length && 'sentence' != $finish) { // Token is too long
                        $c = ( 'word' == $finish ) ? $c : $length - $w; // Keep token to finish word
                        $t = substr($t, 0, $c);
                    }
                    $w += $c;
                }
            }
            // Append what's left of the token
            $out .= $t;
        }

        return trim(force_balance_tags($out));
    }

    public static function get_button_sizes() {
        return [
            'xs' => __('Extra Small', 'elementor'),
            'sm' => __('Small', 'elementor'),
            'md' => __('Medium', 'elementor'),
            'lg' => __('Large', 'elementor'),
            'xl' => __('Extra Large', 'elementor'),
        ];
    }

    public static function get_jquery_display_mode() {
        return [
            '' => __('None', 'dynamic-content-for-elementor'),
            'slide' => __('Slide', 'dynamic-content-for-elementor'),
            'fade' => __('Fade', 'dynamic-content-for-elementor'),
        ];
    }

    public static function in_the_loop() {
        global $in_the_loop;
        return in_the_loop() || $in_the_loop;
    }
    
    public static function get_form_data($record) {
        // Get sumitetd Form data
        $raw_fields = $record->get('fields');
        // Normalize the Form Data
        $fields = [];
        foreach ($raw_fields as $id => $field) {
            $fields[$id] = $field['value'];
        }

        $extra_fields = self::get_form_extra_data($record, $fields);
        foreach ($extra_fields as $key => $value) {
            $fields[$key] = $value;
        }
        
        global $dce_form;
        if (!empty($dce_form) && is_array($dce_form)) {
            foreach ($fields as $key => $value) {
                $dce_form[$key] = $value;
            }
        } else {
            $dce_form = $fields; // for form tokens
        }
        
        if (!empty($fields['submitted_on_id'])) {
            global $post, $user;
            if (empty($post)) {
                $post = get_post($fields['submitted_on_id']);
            }
            /* if (empty($user)) {
              $user = get_user_by('id', $fields['submitted_by_id']);
              } */
        }
        
        if (!empty($fields['post_id'])) {
            global $post;
            $post = get_post($fields['post_id']);
        }
        
        
        return $fields;
    }
    
    public static function get_form_extra_data($record, $fields = null, $settings = null) {

        $referrer = isset($_POST['referrer']) ? $_POST['referrer'] : '';
        
        if (is_object($record)) {
            $form_name = $record->get_form_settings('form_name');
        } else {
            if (!empty($settings['form_name'])) {
                $form_name = $settings['form_name'];
            }
        }

        // get current page
        $this_post = get_queried_object();
        if ($this_post && get_class($this_post) == 'WP_Post') {
            $this_page = $this_post;
        } else if ($referrer) {
            $post_id = url_to_postid($referrer);
            if ($post_id) {
                $this_post = $this_page = get_post($post_id);
            }
        } else {
            $this_post = $this_page = get_post($_POST['post_id']);
        }

        // get current user
        $this_user_id = get_current_user_id();

        // Elementor DB
        $data = array();
        $email = false;
        $this_user = false;
        foreach ($fields as $label => $value) {
            if (stripos($label, 'email') !== false) {
                $email = $value;
            }
            $data[] = array('label' => $label, 'value' => sanitize_text_field($value));
        }
        if ($this_user_id) {
            if ($this_user = get_userdata($this_user_id)) {
                $this_user = $this_user->display_name;
            }
        }
        $extra = array(
            'submitted_on' => $this_page->post_title,
            'submitted_on_id' => $this_page->ID,
            'submitted_by' => $this_user,
            'submitted_by_id' => $this_user_id
        );

        return [
            'submitted_on_id' => $this_page->ID,
            'submitted_by_id' => $this_user_id,
            'ip_address' => \ElementorPro\Classes\Utils::get_client_ip(),
            'referrer' => $referrer,
            'form_name' => $form_name,
                /*
                  // Elementor DB
                  'sb_elem_cfd' => array(
                  'data'     => $data,
                  'extra'    => $extra,
                  'post'     => array_map( 'sanitize_text_field', $_POST ),
                  'server'   => $_SERVER,
                  'fields_original' => $fields, //array( 'form_fields' => $record->get_form_settings( 'form_fields' ) ),
                  'record_original' => $record,
                  ),
                  'sb_elem_cfd_read' => 0,
                  'sb_elem_cfd_email' => $email,
                  'sb_elem_cfd_form_id' => $fields['form_name'],
                 */
        ];
    }

    public static function get_dynamic_value($value, $fields = array()) {
        if (is_array($value)) {
            if (!empty($value)) {
                foreach ($value as $key => $setting) {
                    if (is_string($setting)) {
                        $value[$key] = self::get_dynamic_value($setting, $fields);
                    }
                    // repeater
                    if (is_array($setting)) {
                        foreach ($setting as $akey => $avalue) {
                            if (is_array($avalue)) {
                                foreach ($avalue as $rkey => $rvalue) {
                                    $value[$key][$akey][$rkey] = self::get_dynamic_value($rvalue, $fields);
                                }
                            }
                        }
                    }
                }
            }
        }
        if (is_string($value)) {
            $value = DCE_Tokens::do_tokens($value);
            $value = do_shortcode($value);
            if (!empty($fields)) {
                $value = self::replace_setting_shortcodes($value, $fields);
                $value = DCE_Tokens::replace_var_tokens($value, 'form', $fields);
            }
        }
        return $value;
    }

    public static function replace_setting_shortcodes($setting, $fields = array(), $urlencode = false) {
        // Shortcode can be `[field id="fds21fd"]` or `[field title="Email" id="fds21fd"]`, multiple shortcodes are allowed
        return preg_replace_callback('/(\[field[^]]*id="(\w+)"[^]]*\])/', function( $matches ) use ( $urlencode, $fields ) {
            $value = '';
            if (isset($fields[$matches[2]])) {
                $value = $fields[$matches[2]];
            }
            if ($urlencode) {
                $value = urlencode($value);
            }
            return $value;
        }, $setting);
    }
    
    public static function get_post_css($post_id = null) {
        $upload = wp_upload_dir();
        $elementor_styles = array(
            'elementor-frontend-css' => ELEMENTOR_ASSETS_PATH . 'css/frontend.min.css',
            //'elementor-icons-css' => ELEMENTOR_ASSETS_PATH . 'lib/eicons/css/elementor-icons.min.css',
            'elementor-common-css' => ELEMENTOR_ASSETS_PATH . 'css/common.min.css',
            //'elementor-animations-css' => ELEMENTOR_ASSETS_PATH . 'lib/animations/animations.min.css',
            'dce-frontend-css' => DCE_PATH . 'assets/css/dce-all.min.css',
        );
        if (self::is_plugin_active('elementor-pro')) {
            $elementor_styles['elementor-pro-css'] = ELEMENTOR_PRO_ASSETS_PATH . 'css/frontend.min.css';
        }
        if ($post_id) {
            $elementor_styles['elementor-post-' . $post_id . '-css'] = $upload['basedir'] . '/elementor/css/post-' . $post_id . '.css';
        }
        $css = '';
        foreach ($elementor_styles as $key => $astyle) {
            //echo $astyle;
            $css .= self::get_style_embed($astyle);
        }
        //var_dump($css); die();
        return $css;
    }
    
    public static function get_style_embed($style) {
        $css = '';
        /* global $wp_styles;
          //$css = var_export($wp_styles->registered, true);
          if (!empty($wp_styles->registered[$style])) {
          $src = $wp_styles->registered[$style]->src;
          $css_file = get_stylesheet_directory_uri() . $src;
          $css .= $css_file;
          if (file_exists($css_file)) {
          $css = file_get_contents($css_file);
          }
          } */
        //$css = $style;
        if (file_exists($style)) {
            $css = file_get_contents($style);
        }
        return $css;
    }
    
    public static function tablefy($html = '') {
        $table_replaces = array(
            'table' => '.elementor-container',
            'tr' => '.elementor-row',
            'td' => '.elementor-column',
        );
        $dom = new \PHPHtmlParser\Dom;
        $dom->load($html);
        foreach ($dom->find('.elementor-container') as $tag) {
            $changeTagTable = function() {
                $this->name = 'table';
            };
            $changeTagTable->call($tag->tag);
        }
        foreach ($dom->find('.elementor-row') as $tag) {
            $changeTagTr = function() {
                $this->name = 'tr';
            };
            $changeTagTr->call($tag->tag);
        }
        foreach ($dom->find('.elementor-column') as $tag) {
            $changeTagTd = function() {
                $this->name = 'td';
            };
            $changeTagTd->call($tag->tag);
        }
        $html_table = (string) $dom;
        return $html_table;
    }

}
