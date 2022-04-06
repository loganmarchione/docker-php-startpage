# docker-php-startpage

[![CI/CD](https://github.com/loganmarchione/docker-php-startpage/actions/workflows/main.yml/badge.svg)](https://github.com/loganmarchione/docker-php-startpage/actions/workflows/main.yml)
[![Docker Image Size (latest semver)](https://img.shields.io/docker/image-size/loganmarchione/docker-php-startpage)](https://hub.docker.com/r/loganmarchione/docker-php-startpage)

Runs a PHP-based startpage in Docker
  - Source code: [GitHub](https://github.com/loganmarchione/docker-php-startpage)
  - Docker container: [Docker Hub](https://hub.docker.com/r/loganmarchione/docker-php-startpage)
  - Image base: [PHP](https://hub.docker.com/_/php)
  - Init system: N/A
  - Application: N/A

## Explanation

  - Runs a Nginx WebDav server in Docker.
  - Exposes two WebDav locations
    - `/public` - No authentication
    - `/restricted` - Authentication if specified (see below)

## Requirements

  - Everything is PHP (server-side), so the Docker container running this image will need to be able to reach URLs to do a status check.
## Docker image information

### Docker image tags
  - `latest`: Latest version
  - `X.X.X`: [Semantic version](https://semver.org/) (use if you want to stick on a specific version)

### Environment variables
N/A

### Ports
| Port on host              | Port in container | Comments            |
|---------------------------|-------------------|---------------------|
| Choose at your discretion | 80                | Apache              |

### Volumes
| Volume on host            | Volume in container          | Comments                           |
|---------------------------|------------------------------|------------------------------------|
| Choose at your discretion | /var/www/html/user_includes  | Used to store user config files    |

### Example usage
Below is an example docker-compose.yml file.
```
version: '3'
services:
  webdav:
    container_name: webdav
    restart: unless-stopped
    environment:
      - WEBDAV_USER=user
      - WEBDAV_PASS=password1
    networks:
      - webdav
    ports:
      - '8888:80'
    volumes:
      - 'webdav:/var/www/webdav'
    image: loganmarchione/docker-webdav-nginx:latest

networks:
  webdav:

volumes:
  webdav:
    driver: local
```

Below is an example of running locally (used to edit/test/debug).
```
git clone https://github.com/loganmarchione/docker-php-startpage.git
cd docker-php-startpage
composer update
php -S localhost:8000
```

## TODO
- [ ] Learn PHP
- [x] Add a [healthcheck](https://docs.docker.com/engine/reference/builder/#healthcheck)
- [ ] Make the image smaller (currently ~500MB due to `vendor` directory, maybe load resources from a CDN?)
- [ ] Add check to make sure JSON is valid (currently, if it's not valid, nothing will load)
- [ ] Get `navbar_title_image` config option working
- [ ] Change `config.json` to `config.php`, since PHP allows setting default variables and comments
- [ ] Investigate using `curl` instead of `get_headers` (`curl` might be faster?)
- [ ] Add a try/except to the `get_headers` call