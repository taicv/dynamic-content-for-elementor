<?php
/** Loads the WordPress Environment and Template */
define('WP_USE_THEMES', false);
require ('../../../../wp-blog-header.php');

if (isset($_GET['element_id'])) {
    $element_id = intval($_GET['element_id']);
} else {
    $element_id = 0;
}

if (!empty($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
} else {
    $post_id = 0;
}

if ($element_id && $post_id) {
    $settings = \DynamicContentForElementor\DCE_Helper::get_settings_by_id($element_id, $post_id);
    $post = get_post($post_id);
    //var_dump($settings);
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$post->post_name.'.ics');
?>BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN'
CALSCALE:GREGORIAN
BEGIN:VEVENT
URL:<?php echo get_permalink($post_id); ?> 
DTSTART:<?php echo date('Ymd\\THi00\\Z',strtotime($settings['dce_calendar_datetime_start'])); ?> 
DTEND:<?php echo date('Ymd\\THi00\\Z',strtotime($settings['dce_calendar_datetime_end'])); ?> 
SUMMARY:<?php echo !empty($settings['dce_calendar_title']) ? $settings['dce_calendar_title'] : ''; ?> 
DESCRIPTION:<?php echo !empty($settings['dce_calendar_description']) ? nl2br($settings['dce_calendar_description']) : ''; ?> 
LOCATION:<?php echo !empty($settings['dce_calendar_location']) ? $settings['dce_calendar_location'] : ''; ?> 
UID:<?php echo md5($settings['dce_calendar_title']); ?> 
END:VEVENT
END:VCALENDAR<?php
    die();
}

echo 'ERROR';