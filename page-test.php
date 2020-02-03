<?php  get_header();?>

<?php
// date_default_timezone_set('Asia/Tokyo');
// $today = new DateTime();
// $today_d = $today->format('d');
//
// $start_time = date('Y-m-01');
// $end_time = date('Y-m-d', strtotime("-" .$day. "day"));
//
// //グラフ範囲外の最新priceを取得
// $befor_data = $wpdb->get_var( $wpdb->prepare(
//   "SELECT sum(wp1_asset.price) FROM
//   (SELECT wallet_name, max(created) AS created
//   FROM $wpdb->asset WHERE created <= %s
//   GROUP BY wallet_name) AS wp2_asset
//   LEFT JOIN
//   (SELECT * FROM $wpdb->asset) AS wp1_asset
//   ON wp2_asset.wallet_name = wp1_asset.wallet_name AND wp2_asset.created = wp1_asset.created",
//   $start_time
// ));
// if(empty($befor_data)) {
//   $befor_data = 0;
// }
// $datas = [];
// global $wpdb;
// //残高推移グラフ用配列の作成
// for($i=0; $i<$today_d; $i++) {
//   $start_time = date('Y-m-01');
//   $end_time = date('Y-m-d', strtotime("-" .$i. "day"));
//   $data = $wpdb->get_var( $wpdb->prepare(
//     "SELECT sum(wp1_asset.price) FROM
//     (SELECT wallet_name, max(created) AS created
//     FROM $wpdb->asset WHERE created BETWEEN %s AND %s
//     GROUP BY wallet_name) AS wp2_asset
//     LEFT JOIN
//     (SELECT * FROM $wpdb->asset) AS wp1_asset
//     ON wp2_asset.wallet_name = wp1_asset.wallet_name AND wp2_asset.created = wp1_asset.created",
//     [$start_time, $end_time]
//   ));
//   if(empty($data)) {
//     $data = $befor_data;
//   }
//   array_push($datas, $data);
// }
// ?>
 <?php //foreach ($datas as $data): ?>
   <p><?php //echo $data ?></p>
 <?php //endforeach; ?>

<?php


$wallet_name = $wpdb->get_col( $wpdb->prepare(
  "SELECT DISTINCT wallet_name
   FROM $wpdb->asset"
));

$asset_sum = 0;
//合計値を取得
for($i=0; $i<count($wallet_name); $i++) {
  $sum = $wpdb->get_row( $wpdb->prepare(
    "SELECT * FROM $wpdb->asset
    WHERE wallet_name = %s
    AND created BETWEEN %s AND %s
    order by created desc limit 1",
    [$wallet_name[$i], $start_time, $end_time]
  ));
  $asset_sum += $sum->price;
}
?>
<h1><?php echo $asset_sum. "円"; ?></h1>

<?php get_footer(); ?>
