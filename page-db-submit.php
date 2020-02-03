<?php
//show-manual
if(isset($_POST['Balance'])){
  if($_POST['post_method'] == 'Y') {
    $wallet_id = $_POST['wallet_id'];
    $wallet_name = $_POST['wallet_name'];
    $wallet_price = $_POST['wallet_price']; //変更前の金額
    $changed_price = $_POST['changed_price']; //変更後の金額
    $used_day = $_POST['created'];
    if($wallet_price != $changed_price) {
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
      //資産に登録処理
      date_default_timezone_set('Asia/Tokyo');
      $today = new DateTime();
      $today = $today->format('Y-m-d');
      $check_data = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $wpdb->asset WHERE wallet_name = %s AND created = %s",
        [$wallet_name, $today]
      ));
      if(empty($check_data))
      {
      //ない時はデータベースに登録
      $wpdb->insert('wp1_asset',
        array(
          'wallet_name' => $wallet_name,
          'price' => $changed_price,
          'created' => current_time('mysql')
          ),
        array(
          '%s',
          '%d',
          '%s'
        ));
      } else {
        //ある時はデータベースを更新
        $wpdb->update('wp1_asset',
        array(
          'price' => $changed_price,
          'modified' => current_time('mysql') //編集年月日
          ),
        array(
          'wallet_name' => $wallet_name,
          'created' => $today
          ),
        array(
          '%d',
          '%s'
        ));
      }
      //「不明金として記帳」にチェックが入っていた時の処理
      if($_POST['checkbox'] == 'on') {
        $unknown_money = $wallet_price - $changed_price;
        if($unknown_money > 0) {
        //登録の処理（購入）
        $wpdb->insert('wp1_purchases',
          array(
            'type' => "支出",
            'source' => $wallet_name,
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
            'source' => $wallet_name,
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
}

//show-manual
if(isset($_POST['Asset'])){
  if($_POST['post_method'] == 'Y') {
    $act_type = $_POST['act_type'];
    $wallet_id = $_POST['wallet_id'];
    $wallet_name = $_POST['asset_name'];
    $old_wallet_name = $_POST['old_wallet_name'];
    $wallet_price = $_POST['wallet_price']; //変更前の金額
    $changed_price = $_POST['changed_price']; //変更後の金額
    $created = $_POST['created'];

    if($act_type == "new") {
      $check_data = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $wpdb->wallet WHERE id = %s",
        $wallet_id
      ));
      if($check_data->name == NULL)
      {
        //ない時は編集の処理
        $wpdb->update('wp1_wallet',
        array(
          'name' => $wallet_name,
          'price' => $changed_price,
          'created' => $created
        ),
        array(
          'id' => $wallet_id
        ),
        array(
          '%s',
          '%d',
          '%s'
        ));
      } else {
      // 登録の処理
        $wpdb->insert('wp1_wallet',
          array(
            'name' => $wallet_name,
            'price' => $changed_price,
            'type' => $check_data->type,
            'type_sub' => $check_data->type_sub,
            'possession' => $check_data->possession,
            'created' => $created
            ),
          array(
            '%s',
            '%d',
            '%s',
            '%s',
            '%s',
        		'%s'
          ));
      }
      //資産の作成
      // $wpdb->insert('wp1_asset',
      //   array(
      //     'wallet_name' => $wallet_name,
      //     'price' => $changed_price,
      //     'created' => $created
      //     ),
      //   array(
      //     '%s',
      //     '%d',
      //     '%s'
      //   ));
    } else if($act_type == "change") {

      $unknown_money = $wallet_price - $changed_price;
      if($unknown_money > 0) {
        $type = "支出";
      }else if($unkonwn_money < 0) {
        $type = "収入";
      }

      // 更新の処理
      $wpdb->update('wp1_wallet',
      array(
        'name' => $wallet_name,
        'price' => $changed_price,
        'created' => $created,
        'modified' => current_time('mysql') //編集年月日
        ),
      array(
        'id' => $wallet_id
        ),
      array(
        '%s',
        '%d',
        '%s',
        '%s'
      ));
      // 資産の財布名更新
      $wpdb->update('wp1_asset',
      array(
        'wallet_name' => $wallet_name
        ),
      array(
        'wallet_name' => $old_wallet_name
        ),
      array(
        '%s'
      ));
      //財布残高と資産推移の更新
      update_wallet_asset($type, $wallet_name, abs($unkonwn_money), $created);
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
//削除の処理（テスト中の為、削除しない）
if (!empty($_POST['ajaxid'])) {
  $id = $_POST['ajaxid'];
  $type = $_POST['ajaxtype'];
  if($_POST['type'] == '削除') {
    if($type == "支出") {
      $db_type = "wp1_purchases";
    } else if ($type == "収入") {
      $db_type = "wp1_incomes";
    } else if ($type == "振替") {
      $db_type = "wp1_transfer";
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
      //'price' => 11000,
      'modified' => current_time('mysql') //編集年月日
    ),
    array(
      'id' => $id
    ),
    array(
      //'%d',
      '%s'
    ));
  } else if($_POST['type'] == '振替') {
    $price = $_POST['price'];
    $source_from = $_POST['source_from'];
    $source_to = $_POST['source_to'];
    $created = $_POST['created'];
    //支出と収入に分解する（テスト中の為、振替を消す処理はなし）
    $wpdb->insert('wp1_purchases',
      array(
        'type' => "支出",
        'source' => $source_from,
        'category_main' => "未分類",
        'category_sub' => "未分類",
        'price' => $price,
        'name' => "",
        'created' => $created,
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
    $wpdb->insert('wp1_incomes',
      array(
        'type' => "収入",
        'source' => $source_to,
        'category_main' => "未分類",
        'category_sub' => "未分類",
        'price' => $price,
        'name' => "",
        'created' => $created,
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
?>

<script type="text/javascript">
//処理後ページを戻る
  history.back();
</script>
