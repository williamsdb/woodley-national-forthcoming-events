<?php
/*
Plugin Name: Woodley & District u3a - wider network forthcoming events
Plugin URI: https://github.com/williamsdb/woodley-national-forthcoming-events
Description: Scrape events from the national u3a page and display them as a formatted list.
Version: 2.2.0
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
        wp_enqueue_script('waduwn_custom_js', plugins_url('/custom.js', __FILE__), array(), '1.1.20');
    }
}

function woodley_national_forthcoming_events($atts = [], $content = null, $tag = '')
{

    // Default parameters
    $defaults = array(
        'title' => TRUE,
        'desc' => TRUE,
        'calendar' => FALSE,
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

        // Create a DateTime object from the input string
        $dateTime = parseEventDate($date);

        // Check if the date was successfully parsed
        if ($dateTime) {
            // Convert the DateTime object to a more usable format and a timestamp
            $timestamp = $dateTime->format("U");
            $usableDate = $dateTime->format("jS F Y ga");
        } else {
            $usableDate = '';
            $timestamp = 0;
        }

        if (!empty($usableDate) && $timestamp > time() && $timestamp < strtotime('+10 months')) {
            $events[] = [
                'title' => $title,
                'url' => $url,
                'description' => $description,
                'date' => $usableDate,
                'timestamp' => $timestamp
            ];
        }
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
            $output .= '<tr>';

            // Show the date and time and add to calendar if selected
            $output .= '<td>' . $event['timestamp'] . '</td>';
            $output .= '<td width="20%" valign="top"><strong>' . $event['date'] . '</strong>&nbsp;';

            // Add the calendar button if the timestamp is valid and the calendar option is enabled
            if ($event['timestamp'] > 0 && $args['calendar']) {
                // Add to Calendar button and popout menu
                $cleandesc = strip_tags($event['description']);
                $cleandesc = str_replace(array("\r", "\n"), ' ', $cleandesc);
                $output .= '<div class="add-to-calendar-container" style="display:inline-block; position:relative; margin-top:5px;">
                    <a href="#" class="add-to-calendar-link" onclick="event.preventDefault(); this.nextElementSibling.style.display = (this.nextElementSibling.style.display === \'block\' ? \'none\' : \'block\');"><small>Add to Calendar &#x25BC;</small></a>
                    <div class="add-to-calendar-menu" style="display:none; position:absolute; z-index:999; background:#fff; border:1px solid #ccc; padding:8px 0; min-width:140px; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                        <a href="#" download="event.ics" target="_blank" style="display:block; padding:4px 16px; text-decoration:none; color:#222;" onclick="event.preventDefault(); addToCalendar(\'' . $event['title'] . '\',\'' . $cleandesc . '\',\'' . $event['date'] . '\');">Apple</a>
                        <a href="' . htmlspecialchars(generate_google_calendar_url($event)) . '" target="_blank" style="display:block; padding:4px 16px; text-decoration:none; color:#222;">Google</a>
                        <a href="#" download="event.ics" target="_blank" style="display:block; padding:4px 16px; text-decoration:none; color:#222;" onclick="event.preventDefault(); addToCalendar(\'' . $event['title'] . '\',\'' . $cleandesc . '\',\'' . $event['date'] . '\');">Outlook</a>
                        <a href="' . htmlspecialchars(generate_yahoo_calendar_url($event)) . '" target="_blank" style="display:block; padding:4px 16px; text-decoration:none; color:#222;">Yahoo</a>
                    </div>
                </div>';
            }
            $output .= '</td><td><a href="' . $event['url'] . '" target="_blank"><span class="u3aeventtitle">' . $event['title'] . '</span></a><br>' . $event['description'];

            $output .= '</td></tr>' . PHP_EOL;
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

function generate_google_calendar_url($event)
{
    $title = urlencode($event['title']);
    $url = urlencode($event['url']);
    $sdate = format_date($event['date']);
    $edate = '';
    if ($sdate) {
        $start = DateTime::createFromFormat('Ymd\THis', $sdate, new DateTimeZone('Europe/London'));
        if ($start) {
            $end = clone $start;
            $end->modify('+1 hour');
            $edate = $end->format('Ymd\THis');
        }
    }
    $description = urlencode(strip_tags($event['description']));
    return "https://www.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$sdate}/{$edate}&details={$description}&location={$url}";
}

function generate_yahoo_calendar_url($event)
{
    $title = urlencode($event['title']);
    $date = urlencode($event['date']);
    $url = urlencode($event['url']);
    $sdate = format_date($event['date']);
    $edate = '';
    if ($sdate) {
        $start = DateTime::createFromFormat('Ymd\THis', $sdate, new DateTimeZone('Europe/London'));
        if ($start) {
            $end = clone $start;
            $end->modify('+1 hour');
            $edate = $end->format('Ymd\THis');
        }
    }
    $description = urlencode(strip_tags($event['description']));
    return "https://calendar.yahoo.com/?v=60&view=d&type=20&title={$title}&st={$sdate}&et={$edate}&url={$url}&desc={$description}";
}

function format_date($date)
{
    // Convert date like "1st July 2025 10am" to "20250701T100000Z"
    $dateTime = DateTime::createFromFormat('jS F Y ga', $date, new DateTimeZone('Europe/London'));
    if (!$dateTime) {
        // Try fallback without "st/nd/rd/th"
        $date = preg_replace('/(\d+)(st|nd|rd|th)/', '$1', $date);
        $dateTime = DateTime::createFromFormat('j F Y ga', $date, new DateTimeZone('Europe/London'));
    }
    if ($dateTime) {
        $dateTime->setTimezone(new DateTimeZone('Europe/London'));
        return $dateTime->format('Ymd\THis');
    }
    return '';
}

/**
 * Parse strings like "Monday 12 January at 10am" or "Wednesday 10 December at 2pm"
 * and return a DateTime with the correct year (rolls into next year when appropriate).
 *
 * @param string $text  Input string
 * @param DateTimeZone|null $tz Optional timezone (default server timezone)
 * @return DateTime|false
 */
function parseEventDate(string $text, DateTimeZone $tz = null)
{
    $tz = $tz ?: new DateTimeZone(date_default_timezone_get());

    // Normalise spacing and lowercase am/pm
    $s = trim(preg_replace('/\s+/', ' ', $text));
    $s = preg_replace('/\s+([ap]m)$/i', '$1', $s); // "10 am" -> "10am" (optional)

    // Regex: optional weekday, day number, month name, "at", hour, optional :minutes, am/pm
    $re = '/^(?:[A-Za-z]+?\s+)?' .                  // optional weekday e.g. "Monday "
        '(?P<day>\d{1,2})\s+' .
        '(?P<month>[A-Za-z]+)\s+' .
        '(?:at\s+)?' .
        '(?P<hour>\d{1,2})(?::(?P<min>\d{2}))?\s*(?P<ampm>am|pm)$/i';

    if (!preg_match($re, $s, $m)) {
        return false;
    }

    $day = (int)$m['day'];
    $monthName = $m['month'];
    $hour = (int)$m['hour'];
    $min = isset($m['min']) && $m['min'] !== '' ? (int)$m['min'] : 0;
    $ampm = strtolower($m['ampm']);

    // Convert month name to month number reliably using DateTime
    $tmp = DateTime::createFromFormat('!F', ucfirst(strtolower($monthName)));
    if (! $tmp) {
        return false;
    }
    $monthNum = (int)$tmp->format('n');

    // Decide year: if the date (this year) is >= now -> this year, else next year.
    $now = new DateTime('now', $tz);
    $year = (int)$now->format('Y');

    // Build candidate datetime for this year
    // use 12-hour format with am/pm
    $timeString = sprintf('%d-%02d-%02d %d:%02d %s', $year, $monthNum, $day, $hour, $min, $ampm);
    $candidate = DateTime::createFromFormat('Y-m-d g:i a', $timeString, $tz);

    // If createFromFormat failed for some reason, return false
    if (! $candidate) {
        return false;
    }

    // If candidate is in the past (strictly less than now), bump year by 1
    if ($candidate < $now) {
        $year++;
        $timeString = sprintf('%d-%02d-%02d %d:%02d %s', $year, $monthNum, $day, $hour, $min, $ampm);
        $candidate = DateTime::createFromFormat('Y-m-d g:i a', $timeString, $tz);
        if (! $candidate) return false;
    }

    return $candidate;
}
