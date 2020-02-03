<?php  get_header();?>
<div class="service_list">
  <section class="">
    <h4 class="heading-normal">手元の現金・資産を登録する</h4>
    <?php
    $title_text = ["財布（現金管理）", "その他保有資産"];
    $link_list = ["#cash-submit", "#asset-submit"];
    $text_count = count($title_text);
     ?>
    <ul class="cut-container">
      <?php for($i = 0; $i < $text_count; $i++) { ?>
      <li class="service-li">
        <a href="<?php echo $link_list[$i] ?>"><?php echo $title_text[$i] ?></a>
      </li>
      <?php } ?>
    </ul>
  </section>
  <?php
  // if(isset($_POST['newCash']))
  //   {
  //    if($_POST['post_method'] == 'Y')
  //     {
  //       $wallet_name = $_POST['wallet_name'];
  //       $wallet_price = $_POST['wallet_price'];
  //
  //       if (empty($wallet_name)) {
  //         $cash_message = '名前は必ず入力してください';
  //       } else if (empty($wallet_price)) {
  //         $cash_message = '残高は必ず入力してください';
  //       } else if  (iconv_strlen($wallet_name) > 20 ) {
  //         $cash_message = '名前は20文字以内で入力してください';
  //       } else if  (iconv_strlen($wallet_price) > 12 ) {
  //         $cash_message = '残高は12桁以内で入力してください';
  //       } else {
  //
  //       // 同じ財布の名前がないか確認する
  //       $check_data = $wpdb->get_row( $wpdb->prepare(
  //         "SELECT * FROM $wpdb->wallet WHERE name = %s",
  //         $wallet_name
  //       ));
  //       if(!empty($check_data))
  //       {
  //         $cash_message = '既に登録されている財布の名前です';
  //       }
  //       else
  //       {
  //       // 登録の処理(資産データの作成も必要)
  //       // $wpdb->insert('wp1_wallet',
  //       //   array(
  //       //     'name' => $wallet_name,
  //       //     'price' => $wallet_price,
  //       //     'type' => "財布",
  //       //     'type_sub' => "現金",
  //       //     'possession' => $wallet_name,
  //       //     'created' => current_time('mysql')
  //       //     ),
  //       //   array(
  //       //     '%s',
  //       //     '%d',
  //       //     '%s',
  //       //     '%s',
  //       //     '%s'
  //       //   ));
  //       $wallet_name = NULL;
  //       $wallet_price = NULL;
  //       $cash_message = '登録処理が完了しました';
  //
  //       $row_data = $wpdb->get_row( $wpdb->prepare(
  //         "SELECT * FROM $wpdb->wallet WHERE name = %s",
  //         $wallet_name
  //       ));
  //       }
  //     }
  //   }
  // }
  ?>
  <script type="text/javascript">
  function submitWallet(name) {
    var walletName = $(".submit_w_name").val();
    var walletPrice = $(".submit_price").val();
    var assetType = $("#category_main").val();
    var assetName = $(".asset_name").val();
    console.log(walletName);
    console.log(walletPrice);
    console.log(assetType);
    console.log(assetName);
    console.log(name);
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: {
        'action': 'ajax_db',
        'submitWallet': 'submit',
        'name': name,
        'wallet_name': walletName,
        'wallet_price': walletPrice,
        'asset_type': assetType,
        'asset_name': assetName
      },
      dataType:'json'
    }).done(function(date){
      console.log(date);
      var id = date[0];
      var cashMessage = date[1];
      var assetMessage = date[2];
      var typeSub = date[3];
      if(cashMessage) {
        $('.cash-err-message').html("<strong>"+ cashMessage +"</strong>");
      } else {
        $('.cash-err-message').html("");
      }
      if (assetMessage) {
        $('.asset-err-message').html("<strong>"+ assetMessage +"</strong>");
      } else {
        $('.asset-err-message').html("");
      }
      if(name == 'newCash' && id != null) {
        location.href = '../show-manual?show-manual-act=show&id=' + id;
      } else if(typeSub == "ポイント" && id != null) {
        location.href = '../show-manual?show-manual-act=detail&id=' + id;
      }
    })
  }
  </script>
  <section id="cash-submit">
    <h4 class="heading-normal">財布（現金管理）</h4>
    <p>
      財布を登録すると、現金残高を簡単に管理できます。<br>
      財布（現金管理）機能の詳細は<a href=#>こちら</a>をご確認ください。
    </p>
    <p class="cash-err-message err-message"></p>
    <?php
    $title_text = ["財布の名前<br>(必須)", "残高<br>(必須)"];
    $text_count = count($title_text);
    $input_row = ["<input class='submit_w_name' autocomplete='off' type='text' name='wallet_name'",
                  "<input class='submit_price' autocomplete='off' type='number' name='wallet_price'"]
    ?>
    <div class="form-table">
      <div class="service-li">
      <?php for($i = 0; $i < $text_count; $i++) { ?>
      <dl class="data-table">
        <dt class="nowrap">
          <span><?php echo $title_text[$i] ?></span>
        </dt>
        <dd>
          <div class="">
            <span>
              <?php echo $input_row[$i] ?>
            </span>
          </div>
        </dd>
      </dl>
      <?php } ?>
      </div>
      <p class="submit">
        <input type="submit" name="newCash" value="登録" onclick="submitWallet(this.name)">
      </p>
    </div>
  </section>

  <section id="asset-submit">
    <h4 class="heading-normal">未対応のその他保有資産</h4>
    <p>
      自動取得に対応していない口座やお手元の資産を、一つの口座として登録できます。<br>
      複数の資産・負債があり、個別に入金先の指定などを行いたい場合には、同一の口座として登録せず、別々の口座としてご作成ください。<br>
      未対応金融機関機能については<a href=#>FAQ</a>をご確認ください。<br>
      また、財布につきましては<a href=#>こちら</a>からご作成ください。<br>
    </p>
    <p class="asset-err-message err-message"></p>
    <?php
    $title_text = ["金融機関カテゴリ(必須)", "金融機関名(必須)"];
    $text_count = count($title_text);
    $input_row = "<input class='asset_name' autocomplete='off' type='text' name='asset_name'>"
    ?>
    <div class="form-table">
      <div class="service-li">
        <dl class="data-table">
          <dt class="nowrap">
            <span>金融機関カテゴリ<br>(必須)</span>
          </dt>
          <dd>
            <div class="">
              <p><!--選択肢その1-->
                <select id="category_main" class="category-menue category-buttom" name="asset_type">
                <script>
                  var items = [];
                  <?php
                  $option_datas = $wpdb->get_col( $wpdb->prepare(
                    "SELECT distinct category_main
                     FROM $wpdb->asset_category"
                  ) );
                  foreach($option_datas as $option) { ?>
                    items.push("<?php echo $option ?>");
                  <?php } ?>
                  getOption("category_main", items);
                  //初期設定の選択肢
                  $("#category_main option[value='その他保有資産']").prop('selected', true);
                </script>
                </select>
              </p>
            </div>
          </dd>
        </dl>
        <dl class="data-table">
          <dt class="nowrap">
            <span>金融機関名<br>(必須)</span>
          </dt>
          <dd>
            <div class="">
              <span>
                <?php echo $input_row ?>
              </span>
            </div>
          </dd>
        </dl>
      </div>
      <p class="submit">
        <input type="submit" name="newAsset" value="登録" onclick="submitWallet(this.name)">
      </p>
    </div>
  </section>
</div>
<?php get_footer(); ?>
