<?php

function get_monthly_total($this_month = 0) {
  // date_default_timezone_set('Asia/Tokyo');
  // $today = new DateTime();
  //一カ月の収支を取得
  $start_time = date('Y-m-01', strtotime($this_month. "month"));// .' 00:00:00';
  $end_time = date('Y-m-t', strtotime($this_month. "month"));// .' 23:59:59';
  // globalキーワードを使うことで$wpdbクラスを利用できる
  global $wpdb;
  //収入の取得
  $month_i_money = $wpdb->get_var( $wpdb->prepare(
    "SELECT sum(price) FROM $wpdb->incomes
    WHERE target = 1 AND created BETWEEN %s AND %s",
    [$start_time, $end_time]
  ) );

  //購入の取得
  $month_p_money = $wpdb->get_var( $wpdb->prepare(
    "SELECT sum(price) FROM $wpdb->purchases
    WHERE target = 1 AND created BETWEEN %s AND %s",
    [$start_time, $end_time]
  ) );

  if($month_i_money == NULL) {
    $month_i_money = 0;
  }
  if($month_p_money == NULL) {
    $month_p_money = 0;
  }
  $month_money = [$month_i_money, $month_p_money, $month_i_money - $month_p_money];
  return $month_money;
  }

?>
