<?php

Class Customer_Controller {

  public function get_all_customers ( $f3 ) {
    $perms = [ 'permission' => 1, 'role' => 'post_payment' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );

    $response = array();
    //  customer
    $st = new DB\SQL\Mapper($f3->get('DB'),'customer');
    $all_customers = $st->find();
    $response['customers'] = array();
    foreach($all_customers as $customer){
      array_push( $response['customers'], $customer->cast() );
    }
    print json_encode($response);
  }
}