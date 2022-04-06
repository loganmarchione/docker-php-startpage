# Features
- JSON-based configuration file
- HTTP status checks
- Custom links in navbar
- Custom footer
- Custom CSS
- Mobile-friendly:
  - Uses [Bootstrap](https://getbootstrap.com/) CSS/JS
- Icon packs:
  - [Font Awesome Free](https://fontawesome.com/) (better for glyphs and symbols)
  - [Simple Icons](https://simpleicons.org/) (better for brands)

## JSON-based configuration file
The configuration file (`config.json`) has two sections:

1. Global configuration options
1. Groups (this a an array which contains services, which itself is another array)

### Global configuration options
Below is the layout of the global options, with some notes.
```
{
    "page_title":           "Dashboard",                                                            sets the HTML <title> of the page
    "navbar_title_image":   "#TODO",                                                                sets the image in the navbar (to the left of the main navbar title)
    "navbar_title":         "10236 Charing Cross Road",                                             sets the main navbar title (to the right of the navbar image)
    "favicon":              "./vendor/fortawesome/font-awesome/svgs/solid/earth-americas.svg",       sets the HTML "favicon" (it's actually a SVG file, not an ICO file)
    ...
    ...
    ...
}
```

### Group/service config
Below is the layout of the group/service options, with some notes.
```
    ...
    ...
    ...
    "Groups": {
        "Web": {
            "404test": {                                                                 The name of the config (this needs to be unique)
                "name": "404 Test page",                                                 The name is display on the page
                "href": "https://httpstat.us/404",                                       The link of the URL displayed above
                "icon": "./vendor/fortawesome/font-awesome/svgs/solid/globe.svg",        The path to the SVG icon
                "stat": true,                                                            True or false to perform a status check
                "misc": ""                                                               Extra info that can go in the "Misc." column
            }
        },
        ...
        ...
        ...
```


## HTTP status checks

When `"stat": true`, PHP will use `get_headers` to perform a quick check of the HTTP status code of the supplied `href`.
```
200 - 300       = Green check
401             = Orange lock
400 - 499       = Orange exclamation
Everything else = Red X
```

When `"stat": false`, the check is skipped.
```
Gray X
```

## Custom links in navbar


## Custom footer


## Custom CSS


## Mobile-friendly

Bootstrap is delivered inside the container image. This makes the image larger, but means that Bootstrap is not loaded from an external third-party CDN (this means it loads faster).

Using Bootstrap ensures that the page works well on desktop and mobile, as well as having a familiar look and feel (e.g., navbar, hamburger menu on mobile, footer, etc...).

## Icon packs

Icons are delivered inside the container image. This makes the image larger, but means that icons are not loaded from an external third-party CDN (this means they load faster). In addition, this also gives the benefit of being able to swap Font Awesome and Simple Icons in the `config.json` file by changing the path to the SVG files.

On that note, the icons are delivered as SVG files, not web fonts. This means you are bound by the limitations of SVGs (e.g., can't change colors, can't have Bootstrap-specific properties applied to them, can't scale with fonts, etc...). Again, this was done for convenience sake.