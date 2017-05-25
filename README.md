# grouphealth
Appointment Scheduler Assignment

Functional Demo available/setup at http://grouphealth.anyx.org/

api/
  Uses Zend Expressive micro framework
  Uses REST skeleton framework from:
    https://github.com/ezimuel/zend-expressive-api

ui/
  Uses AngularJS with ngMaterial and ui.bootstrap

conf/
  Included sample apache vhost conf pointing domain at UI and vhost-alias domain/api

test/
  Uses phpunit TODO

INSTALL:
  git clone https://github.com/drazed/grouphealth.git ./grouphealth
  copy conf/grouphealth_vhost.conf to apache vhosts folder, edit and change domain/paths
  make sure api/data/ and api/data/grouphealth.sqlite3 are owned/readable/writable by apache
  run `apache2ctl configtest` (or variant to test vhost sanity)
  run `apache2ctl graceful` (or variant to restart apache)
