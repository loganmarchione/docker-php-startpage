# Run locally

```
git clone blah
cd blah
composer update
php -S localhost:8000
```


Run in Docker
```
git clone blah
cd blah
sudo docker build --no-cache --file Dockerfile --tag loganmarchione/docker-php-startpage:latest .
sudo docker run --name docker-php-startpage \
  -p 80:80 \
  loganmarchione/docker-php-startpage:latest
```

## TODO
- [ ] Learn PHP
- [ ] Add a [healthcheck](https://docs.docker.com/engine/reference/builder/#healthcheck)
- [ ] Make the image smaller (currently ~500MB due to vendor directory)
- [ ] Add check to make sure JSON is valid (if it's not, thing will load)
- [ ] Get `navbar_title_image` config option working