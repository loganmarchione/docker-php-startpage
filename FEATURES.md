# Features
- [Custom configuration files](#custom-configuration-files)
  - [JSON-based configuration file](#json-based-configuration-file)
    - Global configuration options
    - Group/service configuration options
  - [Custom user includes](#custom-user-includes)
    - Custom links in navbar
    - Custom footer
    - Custom CSS
- [Dark mode](#dark-mode)
- [Card search](#card-search)
- [HTTP status checks](#http-status-checks)
- [Mobile-friendly](#mobile-friendly)
- [Icon packs](#icon-packs)

## Custom configuration files
The startpage works out of the box, but it's assumed the user will mount a Docker volume at `/var/www/html/user_includes` to include custom configuration files. The configuration filenames are below (the filenames must be spelled exactly like below). These files are only loaded if they exist inside the `user_includes` directory. If they are not present (or misspelled), they are not loaded.
```
user_includes
├── config.json
├── footer.html
├── header_links.html
└── style.css
```

⚠️ Anything inside `/var/www/html/user_includes` can be served by Apache. Don't put anything containing usernames/passwords/etc... in this directory. ⚠️

### JSON-based configuration file
The configuration file (`config.json`) has two sections:

1. Global configuration options
1. Groups (this a an array which contains another array of services)

#### Global configuration options
Below is the layout of the global options, with some notes. The options shown below happen to be the defaults.
```
{
    "page_title":           "Dashboard",                                                            # sets the HTML <title> of the page
    "navbar_title_image":   "./vendor/fortawesome/font-awesome/svgs/solid/house.svg",               # sets the image in the navbar (to the left of the main navbar title)
    "navbar_title":         "1234 USA Street",                                                      # sets the main navbar title (to the right of the navbar image)
    "favicon":              "./vendor/fortawesome/font-awesome/svgs/solid/house.svg",               # sets the HTML "favicon" (it's actually a SVG file, not an ICO file)
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
        "Test Pages": {
            "200test": {                                                                            # The name of the config (this needs to be unique per group)
                "name": "200 Test page",                                                            # The name to display on the page
                "href": "https://httpstat.us/200",                                                  # The link of the URL displayed above
                "icon": "./vendor/fortawesome/font-awesome/svgs/solid/earth-americas.svg",          # The path to the SVG icon
                "stat": true,                                                                       # True or false to perform a status check
                "misc": ""                                                                          # Extra info that can go in the card (this can be raw HTML)
            },
            "301test": {
                "name": "301 Test page",
                "href": "https://httpstat.us/301",
                "icon": "./vendor/fortawesome/font-awesome/svgs/solid/earth-europe.svg",
                "stat": true,
                "misc": ""
            },
    ...
    ...
    ...   
```
### Custom user includes

These files are only loaded if they exist inside the `user_includes` directory. If they are not present (or misspelled), they are not loaded.

#### Custom links in navbar
The `header_links.html` file contains the HTML used to add extra links to the navbar. Populate it as needed.
```
<li class="nav-item">
    <a class="nav-link" href="#" target="_blank"><i class="fas fa-sitemap"></i> Link1</a>
</li>
<li class="nav-item">
  <a class="nav-link" href="#" target="_blank"><i class="fas fa-globe"></i> Link2</a>
</li>
```

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/navbar_links_on.png)

#### Custom footer
The file `footer.html` contains the HTML used in the footer. Populate it as needed.
```
<footer class="footer mt-auto py-3 bg-body-tertiary">
    <div class="container"></div>
        <ul class="nav justify-content-center border-bottom pb-3 mb-3">
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link1</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link2</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link3</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link4</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Link5</a></li>
        </ul>
        <p class="text-center text-muted">&copy; 2024 Company, Inc</p>
    </div>
</footer>
```

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/footer.png)

#### Custom CSS
The CSS in `style.css` is loaded after Bootstrap, Font Awesome, and the default CSS. Anything you put in here will overwrite CSS above it. Populate it as needed.
```
body {
    color: #212529;
    background-color: #f8f9fa;
    background-size: 10px 10px;
    background-image:
        linear-gradient(to right, #ccc 1px, transparent 1px),
        linear-gradient(to bottom, #ccc 1px, transparent 1px);
    min-height: 100vh;
}
```

## Dark mode

[Bootstrap 5.3.0](https://blog.getbootstrap.com/2023/05/30/bootstrap-5-3-0/) supports dark mode. Currently, the default is set to `dark` (as God intended), but can be set to `light` via a toggle.

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/desktop_dark.png)

## Card search

A simple Javascript-based search is included to search for cards (it only searches the card title). I'm not a Javascript-person, so I'm open to feedback on this.

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/search.png)

## HTTP status checks

In the `config.json file`, when `"stat": true`, PHP will use `get_headers` to perform a quick check of the HTTP status code of the supplied `href`.
```
    ...
    ...
    ...
    "Groups": {
        "Test Pages": {
            "200test": {
                "name": "200 Test page",
                "href": "https://httpstat.us/200",
                "icon": "./vendor/fortawesome/font-awesome/svgs/solid/earth-americas.svg",
                "stat": true,
                "misc": ""
            },
    ...
    ...
    ... 
```

Based on the status code returned, it will display the following icons. When `"stat": false`, the check is skipped and a `Gray X` is displayed.
```
200 - 300       = Green check
401             = Orange lock
400 - 499       = Orange exclamation
500 - 599       = Red X
Everything else = Red X
```

You can hover over the status icon to see a tooltip containing the status code.

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/http_status_code_check.png)

## Mobile-friendly

[Bootstrap](https://getbootstrap.com/) is delivered inside the container image. This makes the image larger, but means that Bootstrap is not loaded from an external third-party CDN.

I am not a web developer (clearly), so using Bootstrap ensures that the page works well on desktop and mobile, as well as having a familiar look and feel (e.g., navbar, hamburger menu on mobile, footer, etc...).

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/mobile_dark.png)
![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/mobile_light.png)

## Icon packs

Icons from the following sources are delivered inside the container image. This makes the image larger, but means that icons are not loaded from an external third-party CDN. You can switch between icon packs by changing the path to each SVG file in the `config.json`.

| Icon pack                                                                  | Example icon path for `user_includes/config.json`              |
|----------------------------------------------------------------------------|----------------------------------------------------------------|
| [Bootstrap Icons](https://icons.getbootstrap.com/)                         | `./vendor/twbs/bootstrap-icons/icons/qr-code.svg`              |
| [Font Awesome (free)](https://fontawesome.com/)                            | `./vendor/fortawesome/font-awesome/svgs/solid/globe.svg`       |
| [Homelab SVG assets](https://github.com/loganmarchione/homelab-svg-assets) | `./vendor/loganmarchione/homelab-svg-assets/assets/linux.svg`  |
| [Simple Icons](https://simpleicons.org/)                                   | `./vendor/simple-icons/simple-icons/icons/homeassistant.svg`   |
