# grouphealth  
Appointment Scheduler Assignment  
  
Functional Demo available/setup at http://grouphealth.anyx.org/  
  
api/  
  Uses Zend Expressive micro framework  
  Uses REST skeleton framework from:  
    https://github.com/ezimuel/zend-expressive-api  

api/test/  
  A sample PHPUnit test of the Appointment middleware object
  Trigger from /api/ with:
    php tests/phpuit.phar tests/AppointmentTest
  
ui/  
  Uses AngularJS with ngMaterial and ui.bootstrap  
  
conf/  
  Included sample apache vhost conf pointing domain at UI and vhost-alias domain/api  
  
  
INSTALL:  
  git clone https://github.com/drazed/grouphealth.git ./grouphealth  
  copy conf/grouphealth_vhost.conf to apache vhosts folder, edit and change domain/paths  
  make sure api/data/ and api/data/grouphealth.sqlite3 are owned/readable/writable by apache  
  run `apache2ctl configtest` (or variant to test vhost sanity)  
  run `apache2ctl graceful` (or variant to restart apache)  
