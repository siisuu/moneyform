<?php  get_header();?>

<?php
//mypage、accountsからの編集
if(isset($_GET['edit-manual-act']))
  {
      $wallet_id = $_GET['id'];

      $row_data = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $wpdb->wallet WHERE id = %d",
        $wallet_id
      ));
      $wallet_name = $row_data->name;
      $wallet_info = $row_data->info;
      $possession = $row_data->possession;
  }

$data_list = [$wallet_name, $wallet_info];

// if(isset($_POST['dbChange']))
//   {
//    if($_POST['post_method'] == 'Y')
//     {
//       $wallet_id = $_POST['wallet-id'];
//       $wallet_name = $_POST['wallet-name'];
//       $wallet_info = $_POST['wallet-info'];
//
//       if (empty($wallet_name)) {
//         $message = '名前は必ず入力してください';
//       } else if  (iconv_strlen($wallet_name) > 20 ) {
//         $message = '名前は20文字以内で入力してください';
//       } else if  (iconv_strlen($wallet_info) > 20 ) {
//         $message = 'メモは20文字以内で入力してください';
//       } else {
//
//       // 同じ財布の名前がないか確認する
//       $check_data = $wpdb->get_row( $wpdb->prepare(
//         "SELECT * FROM $wpdb->wallet WHERE name = %s",
//         $wallet_name
//       ));
//       $befor_data = $wpdb->get_row( $wpdb->prepare(
//         "SELECT * FROM $wpdb->wallet WHERE id = %d",
//         $wallet_id
//       ));
//
//       if(!empty($check_data) && ($wallet_name != $befor_data->name))
//       {
//         $message = '既に登録されている財布の名前です';
//       }
//       else
//       {
//       // 更新の処理(wp1_asset wallet_name 書き換えが必要)
//       $wpdb->update('wp1_wallet',
//       array(
//         'name' => $wallet_name,
//         'possession' => $wallet_name,
//         'info' => $wallet_info
//         ),
//       array(
//         'id' => $wallet_id
//         ),
//       array(
//         '%s',
//         '%s'
//       ));
//       $message = '登録処理が完了しました';
//       }
//       }
//     }
//   }

?>
<script>
function changeWallet(name) {
  var walletName = $(".submit_w_name").val();
  var walletInfo = $(".submit_info").val();
  var walletId = $("[name=wallet-id]").val();
  var oldWalletName = $("[name=wallet-name]").val();
  var possession = $("[name=possession]").val();
  // console.log(walletName);
  // console.log(walletInfo);
  // console.log(walletId);
  // console.log(oldWalletName);
  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: {
      'action': 'ajax_db',
      'changeWallet': 'change',
      'wallet_name': walletName,
      'wallet_info': walletInfo,
      'wallet_id': walletId,
      'old_wallet_name': oldWalletName,
      'possession': possession
    },
    dataType:'json'
  }).done(function(date){
    console.log(date);
    var Message = date[0];
    var flag = date[1];
  //   var name = date[0];
  //   var id = date[1];
  //   var cashMessage = date[2];
  //   var assetMessage = date[3];
    if(Message) {
      if(flag == true) {
        $('.err-message').html("");
        $('.complete-message').html("<strong>"+ Message +"</strong>");
      } else {
        $('.complete-message').html("");
        $('.err-message').html("<strong>"+ Message +"</strong>");
      }
    } else {
      $('.message').html("");
    }
  })
}

</script>
<div class="edit-manual">
  <h3>財布（現金管理）情報編集</h3>
  <section>
    <h4 class="heading-normal">名称・メモの編集</h4>
    <p class="message err-message"></p>
    <p class="message complete-message"></p>
    <?php
    $title_text = ["財布の名前<br>(必須)", "メモ"];
    $text_list = ["wallet-name", "wallet-info"];
    $class_list = ["submit_w_name", "submit_info"];
    $text_count = count($title_text);
    ?>
    <div class="form-table">
      <input type="hidden" name="wallet-name" value=<?php echo $wallet_name ?> >
      <input type="hidden" name="wallet-id" value=<?php echo $wallet_id ?> >
      <input type="hidden" name="possession" value=<?php echo $possession ?> >
      <div class="service-li">
        <?php for($i = 0; $i < $text_count; $i++) { ?>
        <dl class="data-table">
          <dt class="nowrap">
            <span><?php echo $title_text[$i] ?></span>
          </dt>
          <dd>
            <div class="">
              <span>
                <input class="<?php echo $class_list[$i] ?>" autocomplete="off" type="text" name=<?php echo $text_list[$i] ?> value=<?php echo $data_list[$i] ?>>
              </span>
            </div>
          </dd>
        </dl>
        <?php } ?>
      </div>
      <p class="submit">
        <input type="submit" name="dbChange" value="変更" onclick="changeWallet(this.name)">
      </p>
    </div>
  </section>
</div>

<?php get_footer(); ?>
