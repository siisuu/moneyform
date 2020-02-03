<?php
// dbからoptionを取得
function get_option_items() {
  $datas = $wpdb->get_results( $wpdb->prepare(
    "SELECT distinct %category_main
     FROM $wpdb->purchases_category"
  ) );
  return $datas;
}
?>
