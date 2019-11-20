<?php
/** Loads the WordPress Environment and Template */
define('WP_USE_THEMES', false);
require ('../../../../wp-blog-header.php');

if (isset($_GET['element_id'])) {
    $element_id = $_GET['element_id'];
} else {
    $element_id = 0;
}

if (!empty($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
} else {
    $post_id = 0;
}

if ($element_id && $post_id) {
    
    // static settings
    $settings = \DynamicContentForElementor\DCE_Helper::get_settings_by_id($element_id, $post_id);
    
    // dynamic settings
    // populate post for dynamic data
    global $post;
    $post = get_post($post_id);
    // create an instance of widget to get his dynamic data
    include_once('../includes/widgets/DCE_Widget_Prototype.php');
    include_once('../includes/widgets/CONTENT/DCE_Widget_Calendar.php');
    $data = array('settings' => $settings, 'id' => $element_id);
    $widget = new \DynamicContentForElementor\Widgets\DCE_Widget_Calendar($data, array());
    $settings = $widget->get_settings_for_display();
    
    //
    $start = ($settings['dce_calendar_datetime_format'] != 'string') ? $settings['dce_calendar_datetime_start'] : $settings['dce_calendar_datetime_start_string'];
    $end = ($settings['dce_calendar_datetime_format'] != 'string') ? $settings['dce_calendar_datetime_end'] : $settings['dce_calendar_datetime_end_string'];
    
    header('Content-type: text/calendar; charset=utf-8');
    header("Content-Transfer-Encoding: Binary");
    header('Content-Description: File Transfer');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    //header('Content-Disposition: inline; filename="'.$post->post_name.'.ics"');
    header('Content-Disposition: attachment; filename="'.$post->post_name.'.ics"');
?>BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Dynamic.ooo//NONSGML DCE Calendar//EN
CALSCALE:GREGORIAN
METHOD:PUBLISH
BEGIN:VEVENT
URL:<?php echo get_permalink($post_id); ?> 
DTSTART:<?php echo date('Ymd\\THi00\\Z',strtotime($start)); ?> 
DTEND:<?php echo date('Ymd\\THi00\\Z',strtotime($end)); ?> 
SUMMARY:<?php echo !empty($settings['dce_calendar_title']) ? $settings['dce_calendar_title'] : ''; ?> 
DESCRIPTION:<?php echo !empty($settings['dce_calendar_description']) ? strip_tags(nl2br($settings['dce_calendar_description'])) : ''; ?> 
LOCATION:<?php echo !empty($settings['dce_calendar_location']) ? $settings['dce_calendar_location'] : ''; ?> 
UID:<?php echo md5($settings['dce_calendar_title']); ?> 
END:VEVENT
END:VCALENDAR<?php
    die();
}

echo 'ERROR';
