<?php

//Read the config.php file
include 'config/config.php';

// Read the JSON file 
$json = file_get_contents("config.json");
$json_data = json_decode($json,true);

// Set some variables to use later
$page_title = $json_data["page_title"];
$navbar_title_image = $json_data["navbar_title_image"];
$navbar_title = $json_data["navbar_title"];
$favicon = $json_data["favicon"];

// Function to check if a URL is OK
function URL_check(string $url)
{
  // Make HEAD requests instead of GET requets, since HEAD is smaller
  stream_context_set_default(
    array(
        'http' => array(
            'method' => 'HEAD'
        )
    )
  );

  // Array containing the online/offline font glyphs
  $status = array("<span class=\"online\"><i class=\"fas fa-check-circle\"></i></span>", "<span class=\"offline\"><i class=\"fas fa-times-circle\"></i></span>");
  
  $headers = get_headers($url);
  
  // If haystack contains needle(s)
  if (strpos($headers[0], "401") || strpos($headers[0], "404") !== false) {
    // Return offline
    return $status[1];
  } else {
    // Return online
    return $status[0];
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
    <link rel="shortcut icon" href="<?php echo $favicon; ?>">

    <!--extra CSS-->
    <style>
    body {
      background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMC8yOS8xMiKqq3kAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzVxteM2AAABHklEQVRIib2Vyw6EIAxFW5idr///Qx9sfG3pLEyJ3tAwi5EmBqRo7vHawiEEERHS6x7MTMxMVv6+z3tPMUYSkfTM/R0fEaG2bbMv+Gc4nZzn+dN4HAcREa3r+hi3bcuu68jLskhVIlW073tWaYlQ9+F9IpqmSfq+fwskhdO/AwmUTJXrOuaRQNeRkOd5lq7rXmS5InmERKoER/QMvUAPlZDHcZRhGN4CSeGY+aHMqgcks5RrHv/eeh455x5KrMq2yHQdibDO6ncG/KZWL7M8xDyS1/MIO0NJqdULLS81X6/X6aR0nqBSJcPeZnlZrzN477NKURn2Nus8sjzmEII0TfMiyxUuxphVWjpJkbx0btUnshRihVv70Bv8ItXq6Asoi/ZiCbU6YgAAAABJRU5ErkJggg==);
    }
    .logotype {
      height: 1.25em;
      vertical-align: text-top;
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
    .table {
      background-color: white;
    }
    .table caption {
      font-size: 150%;
      border: inherit;
    }
    .table td {
      white-space: nowrap;      /* So table wont' wrap (i.e., I want them to scroll on mobile) */
    }
    </style>

    <title><?php echo $page_title; ?></title>
  </head>
  <body>

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
        </ul>
        </div>
    </div>
    </nav>

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

        </div>
    </div>
    <script src="./vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>