<?php
namespace DynamicContentForElementor;

/**
 * DCE Tokens Class
 *
 * @since 0.1.0
 */
class DCE_Tokens {
    
    public function add_shortcode() {
        add_shortcode('dce-token', [$this, 'do_shortcode']);
    }
    
    public function do_shortcode($params = array()) {
        if (empty($params['value'])) {
            return '';
        }
        return self::do_tokens('['.$params['value'].']');
    }

    static public function do_tokens($text = '') {
        return self::replace_all_tokens($text);
    }

    static public function replace_all_tokens($text) {
        $text = self::replace_date_tokens($text);
        $text = self::replace_user_tokens($text);
        $text = self::replace_post_tokens($text);
        //$text = self::replace_var_tokens($text);
        //$text = $this->replace_term__tokens($text); // TODO?!
        $text = self::replace_option_tokens($text);
        return $text;
    }

    static public function replace_user_tokens($text) {
        $current_user = wp_get_current_user();
        $current_user_id = 0;
        if ($current_user) {
            $current_user_id = $current_user->ID;
        }
        // user field
        $pezzi = explode('[user:', $text);
        if (count($pezzi) > 1) {
            foreach ($pezzi as $key => $avalue) {
                if ($key) {
                    $metaTmp = explode(']', $avalue);
                    $metaParams = reset($metaTmp);

                    $morePezzi = explode('?', $metaParams, 2);
                    $fallback = '';
                    if (count($morePezzi) == 2) {
                        $fallback = end($morePezzi);
                    }
                    $pezzoTmp = reset($morePezzi);
                    $altriPezzi = explode('|', $pezzoTmp, 2);
                    if (count($altriPezzi) == 2) {
                        $filtersTmp = explode('|', end($altriPezzi));
                        if (is_numeric(reset($filtersTmp)) && intval(reset($filtersTmp)) > 0) {
                            $user_id = reset($filtersTmp);
                        }
                        //$user_id = end($altriPezzi);
                    } else {
                        $user_id = $current_user_id;
                    }
                    //echo $user_id;
                    $metaName = reset($altriPezzi);
                    $metaKey = explode(':', $metaName);
                    $field = array_shift($metaKey);
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
                    $replaceValue = self::check_array_value($metaValue, $metaKey);
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    
                    if (count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, end($altriPezzi), $user_id, $field);
                    }
                    
                    $text = str_replace('[user:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }

    static public function replace_post_tokens($text) {
        $current_post_id = $post_id = get_the_ID();
        /*$current_post_id = 0;
        $current_post = get_post();
        if ($current_post) {
            $current_post_id = $post_id = $current_post->ID;
        }*/
        // post field
        $pezzi = explode('[post:', $text);
        if (count($pezzi) > 1) {
            foreach ($pezzi as $key => $avalue) {
                $filters = array();
                if ($key) {
                    $metaTmp = explode(']', $avalue);
                    $metaParams = reset($metaTmp);

                    // GET FALLBACK
                    $morePezzi = explode('?', $metaParams, 2);
                    $fallback = '';
                    if (count($morePezzi) == 2) {
                        $fallback = end($morePezzi);
                    }
                    $pezzoTmp = reset($morePezzi);

                    // GET FILTERS or ID
                    $altriPezzi = explode('|', $pezzoTmp, 2);
                    if (count($altriPezzi) == 2) {
                        $filtersTmp = explode('|', end($altriPezzi));
                        if (is_numeric(reset($filtersTmp)) && intval(reset($filtersTmp)) > 0) {
                            $post_id = reset($filtersTmp);
                        }
                    }

                    $metaName = reset($altriPezzi);

                    // GET SUB ARRAY
                    $metaKey = explode(':', $metaName);
                    $field = array_shift($metaKey);
                    $metaValue = '';
                    if ($post_id) {
                        $metaValue = DCE_Helper::get_post_value($post_id, $field);
                    }

                    $replaceValue = self::check_array_value($metaValue, $metaKey);
                    //$checkValue = eval($replaceValue);
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    
                    if (count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, end($altriPezzi), $post_id, $field);
                    }
                    
                    $text = str_replace('[post:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }

    static public function replace_var_tokens($text, $var_name, $var_value) {
        $current_post_id = $post_id = get_the_ID();
        if (is_object($var_value)) {
            $var_value = get_object_vars($var_value);
        }
        //print_r($text);
        //if (trim($text) == '['.$var_name.']') {
            $text = str_replace('['.$var_name.']', DCE_Helper::to_string($var_value), $text); // simple
        //}
        // var field
        $pezzi = explode('['.$var_name.':', $text);
        if (count($pezzi) > 1) {
            foreach ($pezzi as $key => $avalue) {
                $filters = array();
                if ($key) {
                    $metaTmp = explode(']', $avalue);
                    $metaParams = reset($metaTmp);

                    // GET FALLBACK
                    $morePezzi = explode('?', $metaParams, 2);
                    $fallback = '';
                    if (count($morePezzi) == 2) {
                        $fallback = end($morePezzi);
                    }
                    $pezzoTmp = reset($morePezzi);

                    // GET FILTERS or ID
                    $altriPezzi = explode('|', $pezzoTmp, 2);
                    $post_id = get_the_ID();
                    if (count($altriPezzi) == 2) {
                        $filtersTmp = explode('|', end($altriPezzi));
                        if (is_numeric(reset($filtersTmp)) && intval(reset($filtersTmp)) > 0) {
                            $post_id = reset($filtersTmp);
                        }
                    }

                    $metaName = reset($altriPezzi);

                    // GET SUB ARRAY
                    $metaKey = explode(':', $metaName);
                    $field = reset($metaKey);

                    $replaceValue = self::check_array_value($var_value, $metaKey);
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    
                    if (count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, end($altriPezzi), $post_id, $field);
                    }
                    $text = str_replace('['.$var_name.':' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }

    static public function replace_term_tokens($text) {
        return $text;
    }

    static public function replace_option_tokens($text) {
        // /wp-admin/options.php
        $pezzi = explode('[option:', $text);
        if (count($pezzi) > 1) {
            foreach ($pezzi as $key => $avalue) {
                if ($key) {
                    $pezzo = explode(']', $avalue);
                    $metaParams = reset($pezzo);
                    $morePezzi = explode('?', $metaParams, 2);
                    $fallback = '';
                    if (count($morePezzi) == 2) {
                        $fallback = end($morePezzi);
                    }
                    $pezzoTmp = reset($morePezzi);
                    
                    // GET FILTERS or ID
                    $altriPezzi = explode('|', $pezzoTmp, 2);
                    
                    $optionParams = explode(':', reset($altriPezzi));
                    $optionName = array_shift($optionParams);
                    $optionValue = get_option($optionName);
                    $replaceValue = self::check_array_value($optionValue, $optionParams);
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    if (count($altriPezzi) == 2) {
                        $filtersTmp = explode('|', end($altriPezzi));
                        $replaceValue = self::apply_filters($replaceValue, $filtersTmp);
                    }
                    $text = str_replace('[option:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    
    static public function replace_date_tokens($text) {
        $text = str_replace('[date]', '[date:now]', $text);
        $text = str_replace('[date|', '[date:now|', $text);
        // /wp-admin/options.php
        $pezzi = explode('[date:', $text);
        if (count($pezzi) > 1) {
            foreach ($pezzi as $key => $avalue) {
                if ($key) {
                    $pezzo = explode(']', $avalue);
                    $metaParams = reset($pezzo);
                    //if (in_array(substr($metaParams, 0,1), array(':','|'))) {
                        //$metaParams = substr($metaParams, 1);
                        $altriPezzi = explode('|', $metaParams,2);
                        $filtersTmp = array();
                        if (count($altriPezzi) == 2) {
                            $filtersTmp = explode('|', end($altriPezzi));
                        }
                        // GET TIMESTAMP
                        $timestamp = '';

                        // date format
                        $dateFormat = get_option( 'date_format' );
                        if (!empty($filtersTmp)) {
                            foreach ($filtersTmp as $pkey => $pvalue) {
                                if (!$pkey) {
                                    if ($pvalue && !is_callable($pvalue) && $pvalue != 'IT') {
                                        $dateFormat = $pvalue;
                                    }
                                }
                            }
                        }

                        $pezzoTmp = reset($altriPezzi);
                        $dateParams = explode(':', $pezzoTmp);

                        $altTime = reset($dateParams);
                        if ($altTime == 'post') { // from post field
                            $altTime = self::do_tokens('['.$pezzoTmp.']');
                        }
                        if (is_numeric($altTime)) {
                            $timestamp = $altTime;
                        } else {
                            $timestamp = strtotime($pezzoTmp);
                        }
                        //$dateFormat = $pezzoTmp; //$dateParams[0];
                        $replaceValue = date($dateFormat, $timestamp);

                        // translate
                        if (!empty($filtersTmp)) {
                            foreach ($filtersTmp as $pkey => $pvalue) {
                                $replaceValue = self::str_translate($replaceValue, $pvalue);
                            }
                            $replaceValue = self::apply_filters($replaceValue, $filtersTmp);
                        }

                        //$replaceValue = self::check_array_value($dateValue, $dateFormat); //$dateParams);
                        //$text = str_replace('[date|' . $metaParams . ']', $replaceValue, $text); // now
                        $text = str_replace('[date:' . $metaParams . ']', $replaceValue, $text); // custom date
                    //}
                }
            }
        }
        return $text;
    }
    
    static public function value_or_fallback($replaceValue, $fallback) {
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if ($replaceValue == '' 
                    || substr($replaceValue,0,12) == '<pre>array ('
                    || substr($replaceValue,0,12) == '<pre>object(') {
                // FALLBACK
                $replaceValue = $fallback;
            }
        }
        return $replaceValue;
    }

    static public function check_array_value($optionValue = array(), $optionParams = array()) {
        if (!is_array($optionParams)) {
            $optionParams = array($optionParams);
        }
        
        $val = $optionValue;
        foreach ($optionParams as $key => $value) {
            
            if (is_array($val)) {
                
                /*if (count($val) == 1) {
                    $tmpValue = reset($val);
                    if (!is_array($tmpValue)) {
                        $val = $tmpValue;
                    }
                }*/
                
                if (array_key_exists($value, $val)) {
                    $val = $val[$value];
                } else {
                    return '<pre>'.var_export($val, true).'</pre>';
                } 
                
            } else if (is_object($val)) {
                if (property_exists(get_class($val), $value)) {
                    $val = $val->{$value};
                } else {
                    return '<pre>'.var_export($val, true).'</pre>';
                }
            }
            
        }
        
        if (is_array($val)) {
          if (count($val) == 1) {
              $tmpValue = reset($val);
              if (!is_array($tmpValue)) {
                  $val = $tmpValue;
              }
          }
        }
        
        if (is_array($val) || is_object($val)) {
            return '<pre>'.var_export($val, true).'</pre>';
        }
        
        return $val;
    }
    
    public static function apply_filters($replaceValue = false, $altriPezzi = '', $post_id = 0, $field = '') {
        if (is_string($altriPezzi)) {
            $filtersTmp = explode('|', $altriPezzi);
        }
        if (is_array($altriPezzi)) {
            $filtersTmp = $altriPezzi;
        }
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        $filters = array();
        foreach ($filtersTmp as $afilter) {
            if (!is_numeric($afilter) && !intval($afilter) > 0) {
                $afilterTmp = explode('(', $afilter,2);
                if (count($afilterTmp) == 2) {
                    $parameters = explode(',', substr(end($afilterTmp),0,-1));
                    $kfilter = reset($afilterTmp);
                    $filters[$kfilter] = $parameters;
                } else {
                    $filters[$afilter] = array(); // no params
                }
            }
        }
        // APPLY FILTERS
        if (!empty($filters)) {
            // https://www.w3schools.com/Php/php_ref_string.asp
            // https://www.php.net/manual/en/ref.strings.php
            foreach ($filters as $afilter => $parameters) {

                // THUMB Custom Size
                if (in_array($field, array('thumbnail','post_thumbnail','thumb','guid'))) {
                    if (strpos($afilter, 'x') !== false) {
                        list($h,$w) = explode('x', $afilter, 2);
                        if (is_numeric($h) && is_numeric($w)) {
                            //$h = intval($h);
                            //$w = intval($w);
                            $post_thumbnail_id = get_post_thumbnail_id( $post_id );
                            $replaceValueThumb = wp_get_attachment_image_src( $post_thumbnail_id, array($w,$h));
                            if ($replaceValueThumb) {
                                $replaceValue = reset($replaceValueThumb);
                            } else {
                                $replaceValue = '';
                            }
                            continue;
                        }
                    }
                }

                if ($afilter && is_callable($afilter)) {
                    if (empty($parameters)) {
                        $replaceValue = $afilter($replaceValue);
                    } else {
                        if (in_array($afilter, array('substr'))) {
                            array_unshift($parameters, $replaceValue);
                        }
                        $replaceValue = call_user_func_array($afilter, $parameters);
                    }
                }
            }
        }
        return $replaceValue;
    }

        public static function str_translate($value, $lang) {
        if ($lang == "IT") {
            $value = str_replace("January", "Gennaio", $value);
            $value = str_replace("February", "Febbraio", $value);
            $value = str_replace("March", "Marzo", $value);
            $value = str_replace("April", "Aprile", $value);
            $value = str_replace("May", "Maggio", $value);
            $value = str_replace("June", "Giugno", $value);
            $value = str_replace("July", "Luglio", $value);
            $value = str_replace("August", "Agosto", $value);
            $value = str_replace("September", "Settembre", $value);
            $value = str_replace("October", "Ottobre", $value);
            $value = str_replace("November", "Novembre", $value);
            $value = str_replace("December", "Dicembre", $value);
            
            $value = str_replace("Jan", "Gen", $value);
            $value = str_replace("May", "Mag", $value);
            $value = str_replace("Jun", "Giu", $value);
            $value = str_replace("Jul", "Lug", $value);
            $value = str_replace("Aug", "Ago", $value);
            $value = str_replace("Sep", "Set", $value);
            $value = str_replace("Oct", "Ott", $value);
            $value = str_replace("Dec", "Dic", $value);
            
            $value = str_replace("Sunday", "Domenica", $value);
            $value = str_replace("Monday", "Lunedì", $value);
            $value = str_replace("Tuesday", "Martedì", $value);
            $value = str_replace("Wednesday", "Mercoledì", $value);
            $value = str_replace("Thursday", "Giovedì", $value);
            $value = str_replace("Friday", "Venerdì", $value);
            $value = str_replace("Saturday", "Sabato", $value);
            
            $value = str_replace("Sun", "Dom", $value);
            $value = str_replace("Mon", "Lun", $value);
            $value = str_replace("Tue", "Mar", $value);
            $value = str_replace("Wed", "Mer", $value);
            $value = str_replace("Thu", "Gio", $value);
            $value = str_replace("Fri", "Ven", $value);
            $value = str_replace("Sat", "Sab", $value);
        }
        return $value;
    }

}
