<?php

use Brick\Money\Money;
use Brick\Math\RoundingMode;

Class Sale_Controller {

  public function postSale ( $f3 ) {
    //  set the permission level on this action
    $perms = [ 'permission' => 3, 'role' => 'post_sale' ];
    //  the request should have 'Jwt' ('jwt' works!!) property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );

    $params = json_decode($f3->get('BODY'), true);

    //  instantiate the response array that will be returned to front end
    $response = array();

    //  debug . . . return params and auth (ie the user)
    $response['params'] = $params;
    $response['auth'] = $f3auth;

    //  get the relevant data from the $args object
    $payments = $params['payments'];
    $sale_items = $params['sale_items'];
    $user_id = $f3auth['decoded']->account->id;

    //  calculate totals from sale_items
    $sale_subtotal = 0;
    $sale_tax = 0;
    $sale_total = 0;

    $payment_total = 0;

    $folio_id = 1;

    foreach( $sale_items as $sale_item ) {
      $sale_subtotal += $sale_item['subtotal'];
      $sale_tax += $sale_item['tax'];
      $sale_total += $sale_item['total'];
    }

    foreach( $payments as $payment ) {
      $payment_total += $payment['amountMajorUnit'];
    }


    //  recalculate sales to make sure client math is correct

    //  validate data

    //  as a transaction:
    //    post sale items
    //    post sale
    //    post payment
    
    //  transaction works within a try/catch statement
    try {
        //  get the connection pdo object
        $pdo = DataConnector::get_connection();
        //  start the transaction
        $pdo->beginTransaction();


        //  sale
        $stmt = $pdo->prepare("INSERT INTO pos_sale ( folio, sold_by, date_posted, sale_subtotal, sale_tax, sale_total ) VALUES ( :folio, :sb, NOW(), :sub, :tax, :total )");
        $stmt->execute([
          ':folio' => $folio_id,
          ':sb' => $user_id,
          ':sub' => $sale_subtotal,
          ':tax' => $sale_tax,
          ':total' => $sale_total
        ]);
        $sale_id = $pdo->lastInsertId();

        //  payments
        foreach( $payments as $payment ) {
          $stmt= $pdo->prepare("INSERT INTO pos_payment ( sale_id, payment_type, payment_amount, payment_date ) VALUES ( :si, :pt, :pa, NOW() )");
          $stmt->execute([
            ':si' => $sale_id,
            ':pt' => $payment['paymentType'],
            ':pa' => $payment['amountMajorUnit']
          ]);
        }

        //  sale_items
        foreach( $sale_items as $sale_item ) {
          $stmt = $pdo->prepare("INSERT INTO pos_sale_item ( sale_id, product_id, quantity_sold, price_per_unit, item_subtotal, item_tax_amount, item_tax_spread ) VALUES ( :si, :pi, :qs, :ppu, :sub, :it, :its )");
          $stmt->execute([
            ':si' => $sale_id,
            ':pi' => $sale_item['product'],
            ':qs' => $sale_item['quantity'],
            ':ppu' => $sale_item['price'],
            ':sub' => $sale_item['subtotal'],
            ':it' => $sale_item['tax'],
            ':its' => json_encode($sale_item['taxSpread'])
          ]);
        }

        //  all good.  we got to here.  commit the transaction.
        $pdo->commit();

    //  something went wrong
    } catch (Exception $ex) {
      //  rollback
      $pdo->rollback();
      //  throw the exception
      throw $ex;
    }

    print json_encode($response);

  }

}
