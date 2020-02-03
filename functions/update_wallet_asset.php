<?php

function update_wallet_asset($type, $source, $price, $created) {
  if($source != "なし") {
    global $wpdb;
    //財布残高の更新
    $update_wallet = $wpdb->get_var( $wpdb->prepare(
      "SELECT price FROM $wpdb->wallet WHERE name = %s",
      $source
    ));

    if($type == "支出") {
      $wallet_price = $update_wallet - $price;
    } else if($type == "収入") {
      $wallet_price = $update_wallet + $price;
    }

    if($source != "なし") {
      $wpdb->update('wp1_wallet',
      array(
        'price' => $wallet_price,
        'modified' => current_time('mysql')
        ),
      array(
        'name' => $source
        ),
      array(
        '%d',
        '%s'
      ));
    }

    $check_data_befor = $wpdb->get_results( $wpdb->prepare(
      "SELECT * FROM $wpdb->asset WHERE wallet_name = %s AND created <= %s",
      [$source, $created]
    ));

    $check_data_after = $wpdb->get_results( $wpdb->prepare(
      "SELECT * FROM $wpdb->asset WHERE wallet_name = %s AND created >= %s",
      [$source, $created]
    ));

    if(empty($check_data_after) || empty($check_data_befor))
    {
    // ない時はデータベースに登録
    $wpdb->insert('wp1_asset',
      array(
        'wallet_name' => $source,
        'price' => $wallet_price,
        'created' => $created
        ),
      array(
        '%s',
        '%d',
        '%s'
      ));
    }

    if(!empty($check_data_after)) {
      //ある時はデータベースを更新
      for($i=0; $i<count($check_data_after); $i++) {

        if($type == "支出") {
          $update_price = $check_data_after[$i]->price - $price;
        } else if($type == "収入") {
          $update_price = $check_data_after[$i]->price + $price;
        }

        $wpdb->update('wp1_asset',
        array(
          'price' => $update_price,
          'modified' => current_time('mysql')
          ),
        array(
          'wallet_name' => $source,
          'created' => $check_data_after[$i]->created
          ),
        array(
          '%d',
          '%s'
        ));
      }
    }

    //資産の更新
    // date_default_timezone_set('Asia/Tokyo');
    // $today = new DateTime();
    // $today = $today->format('Y-m-d');
    // $check_data = $wpdb->get_row( $wpdb->prepare(
    //   "SELECT * FROM $wpdb->asset WHERE wallet_name = %s AND created = %s",
    //   [$source, $today]
    // ));
    // if(empty($check_data))
    // {
    // // ない時はデータベースに登録
    // $wpdb->insert('wp1_asset',
    //   array(
    //     'wallet_name' => $source,
    //     'price' => $wallet_price,
    //     'created' => current_time('mysql')
    //     ),
    //   array(
    //     '%s',
    //     '%d',
    //     '%s'
    //   ));
    // } else {
    //   //ある時はデータベースを更新
    //   $wpdb->update('wp1_asset',
    //   array(
    //     'price' => $wallet_price,
    //     'modified' => current_time('mysql')
    //     ),
    //   array(
    //     'wallet_name' => $source,
    //     'created' => $today
    //     ),
    //   array(
    //     '%d',
    //     '%s'
    //   ));
    // }
  }
}

 ?>
