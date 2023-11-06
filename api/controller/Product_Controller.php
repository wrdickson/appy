<?php

Class Product_Controller {
  //  this group of methods respond to router requests,
  //  which always pass $f3 as a parameter
  public function search_products_by_code ( $f3 ) {
    //  set the permission level on this action
    $perms = [ 'permission' => 3, 'role' => 'search_products' ];
    //  throws an error and stops execution if auth fails
    $this->validate_account( $f3, $perms );

    //  create response array
    $response = array();

    //  get params
    $params = json_decode($f3->get('BODY'));
    $response['params'] = $params;
    $response['search_term'] = $params->search_term;
    $search_term = $params->search_term;

    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM pos_product WHERE product_code LIKE :search");
    $stmt->execute([
      ':search' => $search_term . '%'
    ]);
    $arr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $iArr = array();
      $iArr['id'] = $obj->id;
      $iArr['product_title'] = $obj->product_title;
      $iArr['product_subgroup'] = $obj->product_subgroup;
      $iArr['product_code'] = $obj->product_code;
      $iArr['price'] = $obj->price;
      $iArr['tax_group'] = $obj->tax_group;
      $iArr['is_active'] = $obj->is_active;
      

      array_push($arr, $iArr);
    }

    $response['products'] = $arr;

    print json_encode($response);

  }

  //  private functions
  private function validate_account ( $f3, $perms ) {
    //  the request should have 'Jwt' ('jwt' works!!) property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
  }
}