# Features
- Custom configuration files
  - JSON-based configuration file
    - Global configuration options
    - Group/service configuration options
  - Custom user includes
    - Custom centered zone in navbar
    - Custom links in navbar
    - Custom footer
    - Custom CSS
- HTTP status checks
- Mobile-friendly
- Icon packs

## Custom configuration files
The startpage works out of the box, but it's assumed the user will mount a Docker volume at `/var/www/html/user_includes` to include custom configuration files. The configuration filenames are below (the filenames must be spelled exactly like below).
```
user_includes
├── config.json
├── footer.php
├── header_center.php
├── header_links.php
└── style.css
```
⚠️ Anything inside `/var/www/html/user_includes` can be served by Apache. Don't put anything containing usernames/passwords/etc... in this directory. ⚠️

### JSON-based configuration file
The configuration file (`config.json`) has two sections:

1. Global configuration options
1. Groups (this a an array which contains another array of services)

#### Global configuration options
Below is the layout of the global options, with some notes.
```
{
    "page_title":           "Dashboard",                                                            # sets the HTML <title> of the page
    "navbar_title_image":   "./vendor/fortawesome/font-awesome/svgs/solid/house.svg",               # sets the image in the navbar (to the left of the main navbar title)
    "navbar_title":         "10236 Charing Cross Road",                                             # sets the main navbar title (to the right of the navbar image)
    "favicon":              "./vendor/fortawesome/font-awesome/svgs/solid/earth-americas.svg",      # sets the HTML "favicon" (it's actually a SVG file, not an ICO file)
    "link_target":          "_self",                                                                # sets the HTML <a> target attribute (e.g., _self or _blank)
    ...
    ...
    ...
}
```

#### Group/service configuration options
Below is the layout of the group/service options, with some notes.
```
    ...
    ...
    ...
    "Groups": {
        "Web": {
            "404test": {                                                                 # The name of the config (this needs to be unique per group)
                "name": "404 Test page",                                                 # The name to display on the page
                "href": "https://httpstat.us/404",                                       # The link of the URL displayed above
                "icon": "./vendor/fortawesome/font-awesome/svgs/solid/globe.svg",        # The path to the SVG icon
                "stat": true,                                                            # True or false to perform a status check
                "misc": ""                                                               # Extra info that can go in the "Misc." column
            }
        },
        "Home": {
            "200test": {
                "name": "200 Test page",
                "href": "https://httpstat.us/200",
                "icon": "./vendor/fortawesome/font-awesome/svgs/solid/globe.svg",
                "stat": true,
                "misc": ""
            }
    ...
    ...
    ...   
```
### Custom user includes

#### Custom centered zone in navbar
The file `header_center.php` contains the HTML used to add extra info to the navbar (e.g., a custom search engine). This content will be centered in the navbar. Populate it as needed.
```
<li class="nav-item">
  <form id="form"> 
    <input type="search" id="query" name="q" placeholder="Search...">
    <button>Search</button>
  </form>
</li>>
```

#### Custom links in navbar
The `header_links.php` file contains the HTML used to add extra links to the navbar. This content will be on on the right-side of the navbar. Populate it as needed.
```
<li class="nav-item">
  <a class="nav-link" href="#" target="_blank"><i class="fas fa-sitemap"></i> Network Map</a>
</li>
```

#### Custom footer
The file `footer.php` contains the HTML used in the footer. Populate it as needed.
```
<div class="container">
  <footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
    <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link1</a></li>
    <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link2</a></li>
    <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link3</a></li>
    <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link4</a></li>
    <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link5</a></li>
    </ul>
    <p class="text-center text-muted">&copy; 2021 Company, Inc</p>
  </footer>
</div><!--end container-->
```

#### Custom CSS
The CSS in `style.css` is loaded after Bootstrap, Font Awesome, and the default CSS. Anything you put in here will overwrite CSS above it. Populate it as needed.
```
body {
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMC8yOS8xMiKqq3kAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzVxteM2AAABHklEQVRIib2Vyw6EIAxFW5idr///Qx9sfG3pLEyJ3tAwi5EmBqRo7vHawiEEERHS6x7MTMxMVv6+z3tPMUYSkfTM/R0fEaG2bbMv+Gc4nZzn+dN4HAcREa3r+hi3bcuu68jLskhVIlW073tWaYlQ9+F9IpqmSfq+fwskhdO/AwmUTJXrOuaRQNeRkOd5lq7rXmS5InmERKoER/QMvUAPlZDHcZRhGN4CSeGY+aHMqgcks5RrHv/eeh455x5KrMq2yHQdibDO6ncG/KZWL7M8xDyS1/MIO0NJqdULLS81X6/X6aR0nqBSJcPeZnlZrzN477NKURn2Nus8sjzmEII0TfMiyxUuxphVWjpJkbx0btUnshRihVv70Bv8ItXq6Asoi/ZiCbU6YgAAAABJRU5ErkJggg==);
}
```
In the `darkmode` folder you will find a `style.css` file. You can easily place this in your `/var/www/html/user_includes` folder. Colors can be changed at the top of the CSS.
![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/desktop_dark.png)

## HTTP status checks

In the `config.json file`, when `"stat": true`, PHP will use `get_headers` to perform a quick check of the HTTP status code of the supplied `href`.
```
    ...
    ...
    ...
        "Home": {
            "200test": {
                "name": "200 Test page",
                "href": "https://httpstat.us/200",
                "icon": "./vendor/fortawesome/font-awesome/svgs/solid/globe.svg",
                "stat": true,
                "misc": ""
            }
    ...
    ...
    ... 
```

Based on the status code returned, it will display the following icons.
```
200 - 300       = Green check
401             = Orange lock
400 - 499       = Orange exclamation
Everything else = Red X
```

When `"stat": false`, the check is skipped and a `Gray X` is displayed.

## Mobile-friendly

[Bootstrap](https://getbootstrap.com/) is delivered inside the container image. This makes the image larger, but means that Bootstrap is not loaded from an external third-party CDN.

I am not a web developer, so using Bootstrap ensures that the page works well on desktop and mobile, as well as having a familiar look and feel (e.g., navbar, hamburger menu on mobile, footer, etc...).

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/mobile.png)

## Icon packs

Icons from [Font Awesome (free)](https://fontawesome.com/) and [Simple Icons](https://simpleicons.org/) are delivered inside the container image. This makes the image larger, but means that icons are not loaded from an external third-party CDN. In addition, this also gives the benefit of being able to swap Font Awesome and Simple Icons in the `config.json` file by changing the path to the SVG files.
