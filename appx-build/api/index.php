<?php

require 'vendor/autoload.php';

//  instantiate $f3 before loading fragments
//  this makes $f3 pretty global but wtf . . .
$f3 = \Base::instance();

$db = new DB\SQL(
  'mysql:host=localhost;port=3306;dbname=appx-db',
  'mto_admin',
  'quetzal123'
);

// assign the db connection to $f3 object
$f3->set('DB', $db);
// autoload the controllers and models into the $f3 object
$f3->set('AUTOLOAD', 'controller/');


$f3->route('GET /test', 'Test_Controller->index');

//  start the router
$f3->run();