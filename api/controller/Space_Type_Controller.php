<?php

Class Space_Type_Controller {

  public function get_space_type ($f3 ){
    $perms = [ 'permission' => 3, 'role' => 'post_payment' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
    $id = $f3->get('PARAMS.id');
    $st = new DB\SQL\Mapper($f3->get('DB'),'space_types');
    $st->load(array('id=?', $id));
    print json_encode( $st->cast() );
  }

  public function index ( $f3 ) {
    
    $st = new DB\SQL\Mapper($f3->get('DB'),'space_types');
    $all_space_types = $st->find();

    $response = array();
    foreach( $all_space_types as $i_space_type ) {
      array_push($response, $i_space_type->cast());
    }
    print json_encode($response);
    
  }

  public function update_space_type ( $f3 ) {

    $perms = [ 'permission' => 3, 'role' => 'post_payment' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );

    $id = $f3->get('PARAMS.id');
    $obj = json_decode($f3->get('BODY'));
    
    $st = new DB\SQL\Mapper($f3->get('DB'),'space_types');
    $st->load(array('id=?', $id));
    $st->title = $obj->title;
    $st->display_order = $obj->display_order;
    $st->is_active = $obj->is_active;

    $success = $st->save();
    print json_encode($st->cast());
    //print json_encode($obj);

    //  transaction structure
    try {

    } catch ( PDOException $e ) {
      throw $e;
    }

  }


}
