# docker-php-startpage

[![CI/CD](https://github.com/loganmarchione/docker-php-startpage/actions/workflows/main.yml/badge.svg)](https://github.com/loganmarchione/docker-php-startpage/actions/workflows/main.yml)
[![Docker Image Size (latest semver)](https://img.shields.io/docker/image-size/loganmarchione/docker-php-startpage)](https://hub.docker.com/r/loganmarchione/docker-php-startpage)

Runs a PHP-based startpage in Docker
  - Source code: [GitHub](https://github.com/loganmarchione/docker-php-startpage)
  - Docker container: [Docker Hub](https://hub.docker.com/r/loganmarchione/docker-php-startpage)
  - Image base: [PHP](https://hub.docker.com/_/php)
  - Init system: N/A
  - Application: N/A
  - Architecture: `linux/amd64,linux/arm64,linux/arm/v7`

## Explanation

  - Runs a PHP-based startpage in Docker.
  - See [Features](https://github.com/loganmarchione/docker-php-startpage/blob/master/FEATURES.md) for more detailed usage information.
  - Inspired by https://github.com/hywax/mafl and https://github.com/notclickable-jordan/starbase-80

## Screenshots

### Desktop

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/desktop_dark.png)
![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/desktop_light.png)

### Mobile

![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/mobile_dark.png)
![Screenshot](https://raw.githubusercontent.com/loganmarchione/docker-php-startpage/master/screenshots/mobile_light.png)

## Requirements

  - Everything is PHP (server-side), so the Docker container running this image will need to be able to reach URLs to do a status check.
  - The startpage works out of the box (using a sample `config.json` file), but it's assumed the user will mount a Docker volume at `/var/www/html/user_includes` to include custom configuration files.
  - See [Features](https://github.com/loganmarchione/docker-php-startpage/blob/master/FEATURES.md) for more detailed usage information.

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
  startpage:
    container_name: docker-php-startpage
    restart: unless-stopped
    networks:
      - startpage
    ports:
      - '8888:80'
    volumes:
      - 'user_includes:/var/www/html/user_includes'
    image: loganmarchione/docker-php-startpage:latest

networks:
  startpage:

volumes:
  user_includes:
    driver: local
```

### Debugging only

Below is an example of running locally (used to edit/test/debug).
```
# Build the Dockerfile
docker compose -f docker-compose-dev.yml up -d

# View logs
docker compose -f docker-compose-dev.yml logs -f

# Destroy when done
docker compose -f docker-compose-dev.yml down
```