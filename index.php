<?php
// This is probably the worst PHP you'll ever read in your life

// Check if a user config.json file exists, otherwise use the sample file
if (file_exists("user_includes/config.json")) {
  $json = file_get_contents("user_includes/config.json");
} else {
  $json = file_get_contents("sample.json");
}

// Decode the JSON
$json_data = json_decode($json,true);

// Set some variables to use later
$page_title =             $json_data["page_title"];
$navbar_title_image =     $json_data["navbar_title_image"];
$navbar_title =           $json_data["navbar_title"];
$favicon =                $json_data["favicon"];

// Function to check URL HTTP status
function URL_check(string $url)
{
  // Set some defaults
  stream_context_set_default(
    array(
      'http' => array(
        'max_redirects'=>5,
        'timeout' => 5,
        'method' => 'GET'   // Used GET instead of HEAD because some apps don't like HEAD requests
      )
    )
  );

  // Array containing the various status glyphs
  $status = array(
    "<span class=\"auth\"><i class=\"fas fa-lock\"></i></span>",
    "<span class=\"error\"><i class=\"fas fa-circle-exclamation\"></i></span>",
    "<span class=\"online\"><i class=\"fas fa-circle-check\"></i></span>",
    "<span class=\"offline\"><i class=\"fas fa-circle-xmark\"></i></span>"
  );

  // Get the headers and 3-digit status code
  $headers = get_headers($url);
  $status_code = substr($headers[0], 9, 3 );
  
  // If haystack contains needle(s)
  if (strpos($headers[0], "401") !== false) {
    return $status[0];
  } elseif ($status_code >= 400 && $status_code <= 499) {
    return $status[1];
  } elseif ($status_code >= 200 && $status_code <= 399) {
    return $status[2];
  } else {
    return $status[3];
  }
}

?>
<!doctype html>
<html lang="en">
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
    .error {
      height: 1.25em;
      padding: 2px 5px 2px 5px;
      margin: 0;
      display: inline;
      color: orange;
      font-family: inherit;
      border-radius: 4px;
    }
    .online {
      height: 1.25em;
      padding: 2px 5px 2px 5px;
      margin: 0;
      display: inline;
      color: green;
      font-family: inherit;
      border-radius: 4px;
    }
    .offline { 
      height: 1.25em;
      padding: 2px 5px 2px 5px;
      margin: 0;
      display: inline;
      color: red;
      font-family: inherit;
      border-radius: 4px;
    }
    .auth {
        height: 1.25em;
        padding: 2px 5px 2px 5px;
        margin: 0;
        display: inline;
        color: orange;
        font-family: inherit;
        border-radius: 4px;
    }
    .unknown {
        height: 1.25em;
        padding: 2px 5px 2px 5px;
        margin: 0;
        display: inline;
        color: gray;
        font-family: inherit;
        border-radius: 4px;
    }
    .table {
      background-color: white;
    }
    .table caption {
      font-size: 150%;
      border: inherit;
    }
    .table td {
      white-space: nowrap;      /* So table won't wrap (i.e., I want them to scroll horizontally on mobile) */
    }
    </style>

    <?php if (file_exists("user_includes/style.css")) {
      include("user_includes/style.css");
    } ?>

    <link rel="shortcut icon" href="<?php echo $favicon; ?>">
    <title><?php echo $page_title; ?></title>
  </head>
  <body>
  <header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><?php echo $navbar_title; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ms-auto mb-2 mb-md-0">
            <li class="nav-item">
            <a class="nav-link" href="#" onclick="window.location.reload(true);"><i class="fas fa-sync fa-spin"></i> Reload</a>
            </li>
            <?php if (file_exists("user_includes/header_links.php")) { include("user_includes/header_links.php"); } ?>
        </ul>
        </div>
    </div>
    </nav>
    </header>

    <div class="container">
        <div class="row">

<?php
        // Only get JSON under the Groups key
        $array = $json_data["Groups"];

        // For each sub-key under Groups
        foreach($array as $group => $group_array){
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
            <td><img class=\"logotype\" src=\"$service_icon\"> <a href=\"$service_href\" target=\"_blank\">$service_name</a></td>
            <td>";
            // If $service_stat is set to true, perform the check
            if ($service_stat) {
              echo URL_check($service_href);
            }
            else {
              echo "<span class=\"unknown\"><i class=\"fas fa-circle-xmark\"></i></span>";
            }
            echo "</td>
            <td>$service_misc</td>
            </tr>";
        } // close foreach

        echo "
        </tbody>
        </table>
        </div>
        </div>";
        } // close foreach
?>

        </div><!--end row-->
    </div><!--end container-->

    <?php if (file_exists("user_includes/footer.php")) {
      include("user_includes/footer.php");
    } ?>

    <script src="./vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
