<?php

require 'vendor/autoload.php';
require 'config/config.php';
require 'lib/Auth.php';
require 'lib/F3Auth.php';
require 'lib/DataConnector.php';

//  instantiate $f3 
$f3 = \Base::instance();

//  instantiate an f3 connection object
/*
$db = new DB\SQL(
  'mysql:host=' . DB_HOST. ';port=3306;dbname=' . DB_NAME,
  DB_USER,
  DB_PASS
);
*/
$db = new DB\SQL(
  'mysql:host=' . DB_HOST. ';dbname=' . DB_NAME,
  DB_USER,
  DB_PASS
);


// assign the db connection to $f3 object
$f3->set('DB', $db);


// autoload the controllers and models into the $f3 object
$f3->set('AUTOLOAD', 'controller/');

//  Options
$f3->route('GET /autoload-options', 'Options_Controller->get_autoload_options');

//  POS

$f3->route('GET /pos-data', 'Pos_Controller->get_initial_data');
$f3->route('GET /tax-types', 'Pos_Controller->get_tax_types');
$f3->route('GET /payment-types', 'Pos_Controller->get_payment_types');

//  Products
$f3->route('POST /product-search-code', 'Product_Controller->search_products_by_code');

//  Customers

$f3->route('GET /customers', 'Customer_Controller->get_all_customers');

$f3->route('GET /test', 'Test_Controller->index');

$f3->route('GET /test/do-something', 'Test_Controller->do_something');

$f3->route('GET /spacetypes', 'Space_Type_Controller->index');

$f3->route('GET /spacetypes/@id', 'Space_Type_Controller->get_space_type');

$f3->route('POST /spacetypes/@id', 'Space_Type_Controller->update_space_type');

$f3->route('POST /sales', 'Sale_Controller->postSale');

/**
 * LOGIN
 * 
 * ftn requires SERVER_NAME, JWT_KEY, DB_HOST, DB_NAME, DB_USER, and DB_PASS as DEFINED vars.
 * @param $f3 OBJECT should have a raw json body with properties 'username' and 'password'
 */
$f3->route('POST /login', function( $f3 ) {
  $iAuth = new Auth( SERVER_NAME, JWT_KEY, DB_HOST, DB_NAME, DB_USER, DB_PASS);
  $params = json_decode($f3->get('BODY'));
  print json_encode( $iAuth->check_login( $params->username, $params->password ) );
});

/**
 *  AUTHORIZE TOKEN
 * 
 * $perms is not a param, but it:
 * is ALWAYS necessary and
 * is ALWAYS an array with a 'permission' INT property of the account's perm
 * and a 'role' STRING property that might match a string in account's 'roles' JSON array
 * 
 */
$f3->route('POST /authorize-token', function( $f3 ) {
  $perms = ['permission'=> 1, 'role'=>'void'];
  $r = F3Auth::authorize_token( $f3, $perms );
  print json_encode($r);
});

//  start the router
$f3->run();