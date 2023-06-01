<?php
// This is probably the worst PHP you'll ever read in your life

// Check if a user's config.json file exists, otherwise use the sample file
$jsonFile = "user_includes/config.json";
if (file_exists($jsonFile)) {
    $json = file_get_contents($jsonFile);
} else {
    $json = file_get_contents("sample.json");
}

// Check if the file read was successful
if ($json === false) {
    throw new \RuntimeException("ERROR: Failed to read JSON file: $jsonFile");
}

// Decode the JSON
$json_data = json_decode($json, true);

// Check if the JSON decoding was successful
if ($json_data === null) {
    throw new \RuntimeException("ERROR: Failed to decode JSON: " . json_last_error_msg());
}

// Set some variables from the JSON file to use later
$page_title =             $json_data["page_title"] ?? 'Dashboard';
$navbar_title_image =     $json_data["navbar_title_image"] ?? './vendor/fortawesome/font-awesome/svgs/solid/house.svg';
$navbar_title =           $json_data["navbar_title"] ?? '1234 USA Street';
$favicon =                $json_data["favicon"] ?? './vendor/fortawesome/font-awesome/svgs/solid/earth-americas.svg';
$link_target =            $json_data["link_target"] ?? '_blank';
$default_theme =          $json_data["default_theme"] ?? 'dark';

/**
 * Check the HTTP status of a given URL and return HTML based on the result.
 *
 * @param string $url The URL to check.
 * @return string The HTML representing the status based on the HTTP response.
 */
function URL_check(string $url): string {
    // Set some defaults
    stream_context_set_default(
        array(
            'http' => array(
                'max_redirects'=>5,
                'timeout' => 5,
                // Used GET instead of HEAD because some apps don't like HEAD requests
                'method' => 'GET'
            ),
            'ssl' => array(
                // Don't validate certs because some certs in a homelab might be self-signed
                'verify_peer' => false,
                'verify_peer_name' => false,
            )
        )
    );

    // Get the headers and 3-digit status code
    $headers = get_headers($url);

    // If headers are false or the array is empty
    if ($headers === false || !isset($headers[0])) {
        return '<span class="glyph-offline" data-bs-toggle="tooltip" data-bs-title="php-error"><i class="fas fa-circle-xmark"></i></span>';
    }

    $status_code = substr($headers[0], 9, 3 );

    // If $headers contains "401", display lock icon
    if (strpos($headers[0], "401") !== false) {
        return "<span class=\"glyph-auth\" data-bs-toggle=\"tooltip\" data-bs-title=\"$status_code\"><i class=\"fas fa-lock\"></i></span>";
    // If $headers contains 4xx
    } elseif ($status_code >= 400 && $status_code <= 499) {
        return "<span class=\"glyph-error\" data-bs-toggle=\"tooltip\" data-bs-title=\"$status_code\"><i class=\"fas fa-circle-exclamation\"></i></span>";
    // If $headers contains 5xx
    } elseif ($status_code >= 500 && $status_code <= 599) {
        return "<span class=\"glyph-offline\" data-bs-toggle=\"tooltip\" data-bs-title=\"$status_code\"><i class=\"fas fa-circle-xmark\"></i></span>";
    // If $headers contains 2xx-3xx
    } elseif ($status_code >= 200 && $status_code <= 399) {
        return "<span class=\"glyph-online\" data-bs-toggle=\"tooltip\" data-bs-title=\"$status_code\"><i class=\"fas fa-circle-check\"></i></span>";
    // If $headers contains anything else
    } else {
        return '<span class="glyph-offline" data-bs-toggle="tooltip" data-bs-title="offline"><i class="fas fa-circle-xmark"></i></span>';
    }
}

// Iterate through each group and item in that group
foreach ($json_data['Groups'] as $groupName => &$items) {
    foreach ($items as $itemKey => &$item) {
        if ($item['stat'] === true) {
            $item['statusHtml'] = URL_check($item['href']);
        } else {
            $item['statusHtml'] = '<span class="glyph-disabled" data-bs-toggle="tooltip" data-bs-title="Status check disabled"><i class="fas fa-circle-xmark"></i></span>';
        }
    }
}

// Including the Composer autoload file to load required dependencies
require_once './vendor/autoload.php';

// Set the initial path for the templates directory
$loaderPaths = [
  './templates',
];

// Check if the second template directory exists
$secondTemplateDir = './user_includes';
if (is_dir($secondTemplateDir)) {
  $loaderPaths[] = $secondTemplateDir;
}

// Creating a Twig loader with the specified paths
$loader = new \Twig\Loader\FilesystemLoader($loaderPaths);

// Creating a Twig environment with the loader
$twig = new \Twig\Environment($loader);

// Loading the 'index.html.twig' template
$template = $twig->load('index.html.twig');

// Render the template with the provided data
echo $template->render([
    'page_title' => $page_title,
    'navbar_title_image' => $navbar_title_image,
    'navbar_title' => $navbar_title,
    'favicon' => $favicon,
    'link_target' => $link_target,
    'default_theme' => $default_theme,
    'groups' => $json_data['Groups']
]);

?>