# -*- mode: ruby -*-
# vi: set ft=ruby :

########################################
# Variables
########################################
v_base = "debian/bullseye64"
v_name = "docker-php-startpage"
v_cpu = 2
v_mem = 1024

########################################
# Configuration
########################################

Vagrant.configure("2") do |config|
  config.vm.box = v_base
  config.vm.define v_name
  config.vm.hostname = v_name

  config.vm.network "private_network", type: "dhcp"
  config.vm.network "forwarded_port", guest: 8000, host: 8000, protocol: "tcp", auto_correct: true
  config.ssh.extra_args = ["-t", "cd /vagrant; bash --login"]

  config.vm.provider "virtualbox" do |vb|
    vb.gui = false
    vb.name = v_name
    vb.cpus = v_cpu
    vb.memory = v_mem
  end

  config.vm.provision "basics", type: "shell", inline: <<-SHELL
    export DEBIAN_FRONTEND=noninteractive
    apt-get update
    apt-get install -y --no-install-recommends apt-transport-https bash build-essential ca-certificates curl git jq rsync software-properties-common unzip vim wget zip
  SHELL

  config.vm.provision "app-development", type: "shell", inline: <<-SHELL
    export DEBIAN_FRONTEND=noninteractive
    apt-get update
    apt-get install -y --no-install-recommends php php-common php-bcmath php-cli php-curl php-fpm php-gd php-json php-mbstring php-mysql php-pdo php-pear php-xml php-zip
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php
    mv composer.phar /usr/local/bin/composer
    php -r "unlink('composer-setup.php');"
  SHELL

  config.vm.provision "versions", type: "shell", inline: <<-SHELL
    export DEBIAN_FRONTEND=noninteractive
    php --version
    composer --version
  SHELL

end
