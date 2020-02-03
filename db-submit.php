<?php

//household targetChangeBtn
if (!empty($_POST['nowTarget'])) {
  if($_POST['method'] == 'Y') {
    $boolean = $_POST['nowTarget'];
    $id = $_POST['id'];
    $source = $_POST['source'];

    if($source == "支出") {
      $db_type = "wp1_purchases";
    } else if ($source == "収入") {
      $db_type = "wp1_incomes";
    }
    $wpdb->update($db_type,
    array(
      'target' => $boolean
    ),
    array(
      'id' => $id
    ),
    array(
      '%d'
    ));
  }
}
//targetChangeIpt
if (!empty($_POST['changeDataType'])) {
  if($_POST['method'] == 'Y') {
    $change_data_type = $_POST['changeDataType'];
    $p_i_id = $_POST['id'];
    $source = $_POST['source'];
    $value = $_POST['value'];

    if($source == "支出") {
      $db_type = "wp1_purchases";
    } else if ($source == "収入") {
      $db_type = "wp1_incomes";
    }

    if($change_data_type == "price") {
      $data_type = "%d";
    } else {
      $data_type = "%s";
    }

    $wpdb->update($db_type,
    array(
      $change_data_type => $value,
      'modified' => current_time('mysql') //編集年月日
    ),
    array(
      'id' => $p_i_id
    ),
    array(
      $data_type,
      '%s'
    ));
  }
}
//show-manual
if(isset($_POST['Balance'])){
  if($_POST['post_method'] == 'Y') {
    $wallet_id = $_POST['wallet_id'];
    $wallet_source = $_POST['wallet_source'];
    $wallet_price = $_POST['wallet_price'];
    $changed_price = $_POST['changed_price'];
    $used_day = $_POST['created'];
    //編集の処理
    $wpdb->update('wp1_wallet',
    array(
      'price' => $changed_price,
      'modified' => current_time('mysql') //編集年月日
    ),
    array(
      'id' => $wallet_id
    ),
    array(
      '%d',
      '%s'
    ));
    //「不明金として記帳」にチェックが入っていた時の処理
    if($_POST['checkbox'] == 'on') {
      $unknown_money = $wallet_price - $changed_price;
      if($unknown_money > 0) {
      //登録の処理（購入）
      $wpdb->insert('wp1_purchases',
        array(
          'type' => "支出",
          'source' => $wallet_source,
          'category_main' => "現金・カード",
          'category_sub' => "使途不明金",
          'price' => $unknown_money,
          'name' => "支出（不明）",
          'created' => $used_day,
          'target' => 1
          ),
        array(
          '%s',
          '%s',
          '%s',
          '%s',
          '%d',
          '%s',
          '%s',
          '%d'
        ));
      } elseif($unknown_money < 0) {
      //登録の処理（収入）
      $wpdb->insert('wp1_incomes',
        array(
          'type' => "収入",
          'source' => $wallet_source,
          'category_main' => "収入",
          'category_sub' => "不明な入金",
          'price' => -($unknown_money),
          'name' => "収入（不明）",
          'created' => $used_day,
          'target' => 1
          ),
        array(
          '%s',
          '%s',
          '%s',
          '%s',
          '%d',
          '%s',
          '%s',
          '%d'
        ));
      }
    }
  }
}

if(isset($_POST['Delete'])){
  if($_POST['post_method'] == 'Y') {
    $wallet_id = $_POST['wallet_id'];
    $wpdb->update('wp1_wallet',
    array(
      'price' => 50000
    ),
    array(
      'id' => $wallet_id
    ),
    array(
      '%s'
    ));
  }
}
//accounts
//削除の処理（変更でテスト中）
if (!empty($_POST['ajaxid'])) {
  if($_POST['method'] == 'Y') {
    $id = $_POST['ajaxid'];
    $type = $_POST['ajaxtype'];

    if($type == "支出") {
      $db_type = "wp1_purchases";
    } else if ($type == "収入") {
      $db_type = "wp1_incomes";
    } else if ($type == "財布") {
      $db_type = "wp1_wallet";
    }

    // $wpdb->delete('wp1_wallet',
    // array(
    //   'id' => $wallet_id
    //    ),
    //  array(
    //    '%s'
    //  )
    // );
    $wpdb->update($db_type,
    array(
      'price' => 75000
    ),
    array(
      'id' => $id
    ),
    array(
      '%d'
    ));
  }
}
?>
