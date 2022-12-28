<?php
// This is probably the worst PHP you'll ever read in your life

// Check if a user config.json file exists, otherwise use the sample file
if (file_exists("user_includes/config.json")) {
  $json = file_get_contents("user_includes/config.json");
} else {
  $json = file_get_contents("sample.json");
}

// Decode the JSON
$json_data = json_decode($json, true);

// Set some variables to use later
$page_title =             $json_data["page_title"] ?? 'Dashboard';
$navbar_title_image =     $json_data["navbar_title_image"] ?? './vendor/fortawesome/font-awesome/svgs/solid/house.svg';
$navbar_title =           $json_data["navbar_title"] ?? '1234 USA Street';
$favicon =                $json_data["favicon"] ?? './vendor/fortawesome/font-awesome/svgs/solid/earth-americas.svg';
$link_target =            $json_data["link_target"] ?? '_blank';
$default_theme =          $json_data["default_theme"] ?? 'dark';

// Function to check URL HTTP status
function URL_check(string $url): string {
  // Set some defaults
  stream_context_set_default(
    array(
      'http' => array(
        'max_redirects'=>5,
        'timeout' => 5,
        'method' => 'GET'   // Used GET instead of HEAD because some apps don't like HEAD requests
      ),
      'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
      )
    )
  );

  // Get the headers and 3-digit status code
  $headers = get_headers($url);
  $status_code = substr($headers[0], 9, 3 );
  
  // Strings to return based on HTTP status code
  // Special case, if $headers contains "401", display lock icon
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
    return "<span class=\"glyph-offline\" data-bs-toggle=\"tooltip\" data-bs-title=\"offline\"><i class=\"fas fa-circle-xmark\"></i></span>";
  }
}

?>
<!doctype html>
<html lang="en" data-bs-theme="<?php echo $default_theme; ?>">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="./vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./vendor/fortawesome/font-awesome/css/all.min.css" rel="stylesheet">
    <style>
    .logotype {
      height: 1.25em;
      vertical-align: text-top;
    }
    .navbar_title_image {
      height: 1.25em;
      padding-right: 0.5em;
    }
    .glyph-error {
      display: inline;
      color: orange;
    }
    .glyph-online {
      display: inline;
      color: green;
    }
    .glyph-offline {
      display: inline;
      color: red;
    }
    .glyph-auth {
      display: inline;
      color: orange;
    }
    .glyph-disabled {
      display: inline;
      color: gray;
    }
    .table caption {
      font-size: 150%;
      border: inherit;
    }
    /* So table won't wrap (i.e., I want them to scroll horizontally on mobile) */
    .table td {
      white-space: nowrap;
    }
    /* user_includes/style.css comes right after this */
    <?php if (file_exists("user_includes/style.css")) { include("user_includes/style.css"); } ?>

    </style>

    <link rel="shortcut icon" href="<?php echo $favicon; ?>">
    <title><?php echo $page_title; ?></title>
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-md bg-body-tertiary mb-4">
        <div class="container-fluid">
          <img class="navbar_title_image" src="<?php echo $navbar_title_image; ?>"><a class="navbar-brand" href="#"><?php echo $navbar_title; ?></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mx-auto mb-2 mb-md-0">
              <!--user_includes/header_center.php comes right after this-->
              <?php if (file_exists("user_includes/header_center.php")) { include("user_includes/header_center.php"); } ?>
            </ul>
            <ul class="navbar-nav ms-0 mb-2 mb-md-0">
              <!--user_includes/header_links.php comes right after this-->
              <?php if (file_exists("user_includes/header_links.php")) { include("user_includes/header_links.php"); } ?>
              <li class="nav-item">
                <a class="nav-link" href="#" onclick="window.location.reload(true);"><i class="fas fa-sync fa-spin"></i> Reload</a>
              </li>
            </ul>
          </div><!--end collapse-->
        </div><!--end container-->
      </nav>
    </header>

    <div class="container">
      <div class="row">
      <?php
        // Only get JSON under the Groups key
        $array = $json_data["Groups"];

        // For each sub-key under Groups
        foreach($array as $group => $group_array){

        // The formatting is ugly here in source so it produces pretty HTML
        echo "
          <div class=\"col\">
          <div class=\"table-responsive-sm\">
            <table class=\"table table-hover table-striped caption-top\">
              <caption>$group</caption>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Misc.</th>
                </tr>
              </thead>
              <tbody>";

        // For each service in the group
        foreach($group_array as $service => $service_array) {
          $service_name = $service_array["name"];
          $service_href = $service_array["href"];
          $service_icon = $service_array["icon"];
          $service_stat = $service_array["stat"];
          $service_misc = $service_array["misc"];

        echo "
                <tr>
                  <td><img class=\"logotype\" src=\"$service_icon\"> <a href=\"$service_href\" target=\"$link_target\">$service_name</a></td>
                  <td>";
        // If $service_stat is set to true, perform the check
        if ($service_stat) {
          echo URL_check($service_href);
        }
        else {
          echo "<span class=\"glyph-disabled\" data-bs-toggle=\"tooltip\" data-bs-title=\"Status check disabled\"><i class=\"fas fa-circle-xmark\"></i></span>";
        }
        echo "</td>
                  <td>$service_misc</td>
                </tr>";
        } // close foreach

        echo "
              </tbody>
            </table>
          </div><!--end table-responsive-->
          </div><!--end col-->
        ";
        } // close foreach
      ?>

      </div><!--end row-->
    </div><!--end container-->
    <!--user_includes/footer.php comes right after this-->
    <?php if (file_exists("user_includes/footer.php")) { include("user_includes/footer.php"); } ?>

    <script src="./vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Initialize tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      })
    </script>
  </body>
</html>
