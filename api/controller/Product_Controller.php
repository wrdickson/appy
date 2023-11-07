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
    $offset = $params->offset;
    $limit = $params->pageSize;

    $pdo = DataConnector::get_connection();
    //$stmt = $pdo->prepare("SELECT * FROM pos_product WHERE product_code LIKE :search ORDER BY product_title ASC LIMIT :limit OFFSET :offset ");
    $stmt = $pdo->prepare("SELECT pos_product.id, pos_product.product_title, pos_product.product_subgroup, pos_product.price, pos_product.tax_group, pos_product.is_active, pos_product.product_code, pos_product_group.group_title, 
    pos_product_subgroup.subgroup_title, pos_tax_group.tax_types FROM pos_product INNER JOIN pos_product_subgroup ON pos_product_subgroup.id = pos_product.product_subgroup INNER JOIN pos_product_group ON pos_product_group.id = pos_product_subgroup.group_id INNER JOIN pos_tax_group ON pos_tax_group.id = pos_product.tax_group WHERE pos_product.product_code LIKE :search ORDER BY pos_product.product_title ASC LIMIT :limit OFFSET :offset ");
    $stmt->execute([
      ':search' => $search_term . '%',
      ':limit' => $limit,
      ':offset' => $offset
    ]);
    $arr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $iArr = array();
      $iArr['id'] = $obj->id;
      $iArr['product_title'] = $obj->product_title;
      $iArr['product_subgroup'] = $obj->product_subgroup;
      $iArr['group_title'] = $obj->group_title;
      $iArr['subgroup_title'] = $obj->subgroup_title;
      $iArr['product_code'] = $obj->product_code;
      $iArr['tax_types'] = json_decode($obj->tax_types);
      $iArr['price'] = $obj->price;
      $iArr['tax_group'] = $obj->tax_group;
      $iArr['is_active'] = $obj->is_active;


      array_push($arr, $iArr);
    }

    $response['products'] = $arr;

    //  get the total row count for pagination
    $stmt = $pdo->prepare("SELECT id FROM pos_product WHERE product_code LIKE :search");
    $stmt->execute([
      ':search' => $search_term . '%'
    ]);
    $count = $stmt->rowCount();
    $response['rowCount'] = $count;

    print json_encode($response);

  }

  //  private functions
  private function validate_account ( $f3, $perms ) {
    //  the request should have 'Jwt' ('jwt' works!!) property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
  }
}