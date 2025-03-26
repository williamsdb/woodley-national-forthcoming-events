<?php
/*
Plugin Name: Woodley & District u3a - wider network forthcoming events
Plugin URI: https://github.com/williamsdb/woodley-national-forthcoming-events
Description: Scrape events from the national u3a page and display them as a formatted list.
Version: 2.0.2
Author: Neil Thompson
Author URI: http://nei.lt
*/

require __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$MyUpdateChecker = PucFactory::buildUpdateChecker(
    'https://plugins.nei.lt/woodley_national_forthcoming_events.json',
    __FILE__,
    'woodley_national_forthcoming_events'
);

add_shortcode('waduwn', 'woodley_national_forthcoming_events');
add_action('wp_enqueue_scripts', 'waduwn_queue_stylesheet');

function waduwn_queue_stylesheet()
{
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'waduwn')) {
        wp_enqueue_style('waduwn_stylesheet_dt00', '//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css', array());
        wp_enqueue_style('waduwn_stylesheet_dt01', '//cdn.datatables.net/responsive/3.0.4/css/responsive.dataTables.min.css', array());
        wp_enqueue_script('waduwn_custom_js00', '//cdn.datatables.net/2.2.2/js/dataTables.min.js', array('jquery'), '1.1');
        wp_enqueue_script('waduwn_custom_js01',  '//cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.min.js', array(), '1.1');
        wp_enqueue_script('waduwn_custom_js', plugins_url('/custom.js', __FILE__), array(), '1.14');
    }
}

function woodley_national_forthcoming_events($atts = [], $content = null, $tag = '')
{

    // Default parameters
    $defaults = array(
        'title' => TRUE,
        'desc' => TRUE,
    );

    // Merge shortcode attributes with defaults
    $args = shortcode_atts($defaults, $atts, 'waduwn');

    // get the whole of the forthcoming events page html
    list($httpCode, $html) = get_web_page('https://www.u3a.org.uk/events/educational-events#Events');
    if ($httpCode != 200 || empty($html)) {
        $output = '<p>Sorry, there was a problem retrieving the events from the national u3a website. Please try again later. Error code (' . $httpCode . ') ' . $html . '</p>';
        return $output;
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    $events = [];
    $items = $xpath->query('//div[contains(@class, "el-item")]');

    // cycle through each event
    foreach ($items as $item) {
        $titleNode = $xpath->query('.//h3[contains(@class, "el-title")]', $item)->item(0);
        $title = $titleNode ? trim($titleNode->textContent) : '';

        $urlElement = $xpath->query('.//a[contains(@class, "uk-link-toggle")]', $item)->item(0);
        $url = $urlElement ? trim($urlElement->getAttribute('href')) : '';

        $descriptionNode = $xpath->query('.//div[contains(@class, "el-content")]', $item)->item(0);
        $description = $descriptionNode ? trim($dom->saveHTML($descriptionNode)) : '';

        // Extract the date from the description
        $date = '';

        // First format: check within <p> tags inside el-content (e.g., "Monday 17 March at 2pm")
        if ($descriptionNode) {
            $paragraphs = $xpath->query('.//p', $descriptionNode);
            foreach ($paragraphs as $p) {
                $text = trim($p->textContent);
                if (preg_match('/\b(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\s+\d{1,2}\s+\w+\s+at\s+\d{1,2}(am|pm)\b/i', $text, $matches)) {
                    $date = $matches[0];
                    break;
                }
            }
        }

        // Second format: check for date directly in the organiser section
        if (empty($date)) {
            $dateNode = $xpath->query('.//div[contains(@class, "organizer-listing-info-avatar-with-follower")]', $item)->item(0);
            if ($dateNode) {
                $dateText = trim($dateNode->textContent);
                if (preg_match('/\b(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\s+\d{1,2}\s+\w+\s+at\s+\d{1,2}(\.\d{1,2})?(am|pm)\b/i', $dateText, $matches)) {
                    $date = $matches[0];
                }
            }
        }

        // Remove the date from the description
        if ($date) {
            $description = preg_replace('/\b(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\s+\d{1,2}\s+\w+\s+at\s+\d{1,2}(\.\d{1,2})?(am|pm)\b/i', '', $description);
        }

        // Define the format of the input date string
        $inputFormat = "l j F \a\\t ga";

        // Create a DateTime object from the input string
        $dateTime = DateTime::createFromFormat($inputFormat, $date);

        // Check if the date was successfully parsed
        if ($dateTime) {
            // Convert the DateTime object to a more usable format and a timestamp
            $timestamp = $dateTime->format("U");
            $usableDate = $dateTime->format("jS F Y ga");
        } else {
            $usableDate = '';
            $timestamp = 0;
        }

        $events[] = [
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'date' => $usableDate,
            'timestamp' => $timestamp
        ];
    }

    // format the heading for the section
    $output = '';
    if ($args['title']) {
        $output .= '<h3>Forthcoming national events</h3>' . PHP_EOL;
    }
    if ($args['desc']) {
        $output .= '<p>These events are organised by the national u3a and are open to all members. Click on the event title for more information and to book.</p>' . PHP_EOL;
    }

    $output .= '<table id="datatableResdb" class="table table-striped table-bordered"><thead><tr><th>no</th><th>Date</th><th>Description</th></tr></thead><tbody>' . PHP_EOL;

    $i = 0;
    foreach ($events as $event) {
        // only show future events or with no date/time
        if ($event['timestamp'] > time() || $event['date'] == '') {
            $output .= '<tr><td>' . $event['timestamp'] . '</td><td width="20%" valign="top"><strong>' . $event['date'] . '<strong></td><td><a href="' . $event['url'] . '" target="_blank"><span class="u3aeventtitle">' . $event['title'] . '</span></a><br>' . $event['description'] . '</td></tr>' . PHP_EOL;
        }
    }

    $output .= '</tbody></table>' . PHP_EOL;

    return $output;
}

function get_web_page($url)
{
    $options = [
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_ENCODING       => "",     // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,   // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // timeout on connect
        CURLOPT_TIMEOUT        => 120,    // timeout on response
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return array($httpCode, $content);
}
