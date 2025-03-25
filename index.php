<?php
// This is probably the worst PHP you'll ever read in your life

require_once 'vendor/autoload.php';

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
$config = json_decode($json, true);

// Check if the JSON decoding was successful
if ($config === null) {
    throw new \RuntimeException("ERROR: Failed to decode JSON: " . json_last_error_msg());
}

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

    // Verify the URL is valid
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '<span class="glyph-offline" data-bs-toggle="tooltip" data-bs-title="invalid-url"><i class="fas fa-circle-xmark"></i></span>';
    }

    // Get the headers and 3-digit status code
    $headers = get_headers($url);

    // If headers are false or the array is empty
    $headers = @get_headers($url);
    if (!$headers || !isset($headers[0])) {
        return '<span class="glyph-offline" data-bs-toggle="tooltip" data-bs-title="php-error"><i class="fas fa-circle-xmark"></i></span>';
    }

    $status_code = (int) substr($headers[0], 9, 3);

    return match (true) {
        // If $headers contains "401", display lock icon
        str_contains($headers[0], "401") => '<span class="glyph-auth" data-bs-toggle="tooltip" data-bs-title="' . $status_code . '"><i class="fas fa-lock"></i></span>',
        // If $headers contains 4xx
        $status_code >= 400 && $status_code <= 499 => '<span class="glyph-error" data-bs-toggle="tooltip" data-bs-title="' . $status_code . '"><i class="fas fa-circle-exclamation"></i></span>',
        // If $headers contains 5xx
        $status_code >= 500 && $status_code <= 599 => '<span class="glyph-offline" data-bs-toggle="tooltip" data-bs-title="' . $status_code . '"><i class="fas fa-circle-xmark"></i></span>',
        // If $headers contains 2xx-3xx
        $status_code >= 200 && $status_code <= 399 => '<span class="glyph-online" data-bs-toggle="tooltip" data-bs-title="' . $status_code . '"><i class="fas fa-circle-check"></i></span>',
        // If $headers contains anything else
        default => '<span class="glyph-offline" data-bs-toggle="tooltip" data-bs-title="offline"><i class="fas fa-circle-xmark"></i></span>',
    };
}

// Check the status of each link if 'stat' is enabled
foreach ($config['Groups'] as &$group) {
    foreach ($group as &$service) {
        if ($service['stat']) {
            $service['status'] = URL_check($service['href']);
        } else {
            $service['status'] = '<span class="glyph-disabled" data-bs-toggle="tooltip" data-bs-title="Status check disabled"><i class="fas fa-circle-xmark"></i></span>';
        }
    }
}

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
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

// Render the template
echo $twig->render('index.html.twig', [
    'config' => $config,
    'autoescape' => 'html',
]);

?>