<?php

Class Pos_Controller {

  public function get_initial_data ( $f3 ) {
    $perms = [ 'permission' => 1, 'role' => 'post_payment' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );

    $response = array();
    //  payment type
    $st = new DB\SQL\Mapper($f3->get('DB'),'pos_payment_type');
    $all_payment_types = $st->find(array('is_active>?',0));
    $response['payment_types'] = array();
    foreach($all_payment_types as $payment_type){
      array_push( $response['payment_types'], $payment_type->cast() );
    }

    //  product
    $st = new DB\SQL\Mapper($f3->get('DB'),'pos_product');
    $all_products = $st->find(array('is_active>?',0));
    $response['products'] = array();
    foreach($all_products as $product){
      array_push( $response['products'], $product->cast() );
    }

    //  product_group
    $st = new DB\SQL\Mapper($f3->get('DB'),'pos_product_group');
    $all_product_groups = $st->find(array('is_active>?',0));
    $response['product_groups'] = array();
    foreach($all_product_groups as $product_group){
      array_push( $response['product_groups'], $product_group->cast() );
    }

    //  product_subgroup
    $st = new DB\SQL\Mapper($f3->get('DB'),'pos_product_subgroup');
    $all_product_subgroups = $st->find(array('is_active>?',0));
    $response['product_subgroups'] = array();
    foreach($all_product_subgroups as $product_subgroup){
      array_push( $response['product_subgroups'], $product_subgroup->cast() );
    }

    //  tax_group
    $st = new DB\SQL\Mapper($f3->get('DB'),'pos_tax_group');
    $all_tax_groups = $st->find(array('is_active>?',0));
    $response['tax_groups'] = array();
    foreach($all_tax_groups as $tax_group){
      array_push( $response['tax_groups'], $tax_group->cast() );
    }

    //  tax_type
    $st = new DB\SQL\Mapper($f3->get('DB'),'pos_tax_type');
    $all_tax_types = $st->find(array('is_active>?',0));
    $response['tax_types'] = array();
    foreach($all_tax_types as $tax_type){
      array_push( $response['tax_types'], $tax_type->cast() );
    }

    print json_encode($response);
  }

  public function get_payment_types ( $f3 ) {
    $perms = [ 'permission' => 3, 'role' => '' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );

    $response = array();
    //  payment type
    $st = new DB\SQL\Mapper($f3->get('DB'),'pos_payment_type');
    $all_payment_types = $st->find(array('is_active>?',0));
    $response['payment_types'] = array();
    foreach($all_payment_types as $payment_type){
      array_push( $response['payment_types'], $payment_type->cast() );
    }
    print json_encode($response);

  }

  public function get_tax_types ( $f3 ) {
    $perms = [ 'permission' => 3, 'role' => '' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );

    $response = array();
    //  tax_type
    $st = new DB\SQL\Mapper($f3->get('DB'),'pos_tax_type');
    $all_tax_types = $st->find(array('is_active>?',0));
    $response['tax_types'] = array();
    foreach($all_tax_types as $tax_type){
      array_push( $response['tax_types'], $tax_type->cast() );
    }

    print json_encode($response);
  }
}