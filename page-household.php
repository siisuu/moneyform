<?php  get_header();?>
<?php
$this_month = 0;
if(isset($_GET['month'])) {
  $this_month = $_GET['month'];
}
  //入力フォームデータをdbに送信する　関数で呼び出す
  if(isset($_POST['Submit'])) {
    // POSTリクエストの場合
    if( $_POST['post_method'] == 'Y' )
    {

      global $wpdb;
      $source = $_POST['wallet_source'];
      $category_main = $_POST['category_main'];
      $category_sub = $_POST['category_sub'];
      $price = $_POST['price'];
      $content_name = $_POST['text'];
      $created = $_POST['created'];
      $type = $_POST['type'];
      $transfer_from = $_POST['transfer-from'];
      $transfer_to = $_POST['transfer-to'];

      if (empty($price)) {
        $price_message = '金額は必ず入力してください';
      } else if  (iconv_strlen($price) > 12 ) {
        $price_message = '金額は12桁以下で入力してください';
      } else if  (iconv_strlen($content_name) > 50 ) {
        $content_message = '内容は50文字以内で入力してください';
      } else {

      if($type == "支出") {
        $db_type = "wp1_purchases";
      } else if ($type == "収入") {
        $db_type = "wp1_incomes";
      } else if ($type == "振替") {
        $db_type = "wp1_transfer";
      }

      if($type == "支出" || $type == "収入") {
        // データベースに登録
        $wpdb->insert($db_type,
          array(
            'type' => $type,
            'source' => $source,
            'category_main' => $category_main,
            'category_sub' => $category_sub,
            'price' => $price,
            'name' => $content_name,
            'created' => $created, //current_time('mysql')
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
          )
        );
        //財布残高と資産推移の更新
        update_wallet_asset($type, $source, $price, $created);

        }else if($type == "振替") {
          // データベースに登録
          $wpdb->insert($db_type,
            array(
              'type' => $type,
              'source_from' => $transfer_from,
              'source_to' => $transfer_to,
              'price' => $price,
              'name' => $content_name,
              'created' => $created,
              'target' => 1
              ),
            array(
              '%s',
              '%s',
              '%s',
              '%d',
              '%s',
              '%s',
              '%d'
            )
          );
          //財布残高と資産推移の更新
          update_wallet_asset("支出", $transfer_from, $price, $created);
          update_wallet_asset("収入", $transfer_to, $price, $created);
      }
    }
  }
}
if($empty_message) {
?>
<script>
  alert("<?php echo $empty_message ?>");
</script>
<?php
}
//財布データ配列の作成
$wallet_datas = $wpdb->get_results( $wpdb->prepare(
  "SELECT* FROM $wpdb->wallet WHERE type = '財布'"
) );
?>
<script>
var wallet_items = [];
<?php foreach($wallet_datas as $row_data) { ?>
  wallet_items.push("<?php echo $row_data->name ?>");
<?php } ?>
wallet_items.push("なし");
//支出大項目の取得
//大項目配列データの作成（収入用作成 切替機能 db化 要）
var p_items = [];
<?php
$option_datas = $wpdb->get_results( $wpdb->prepare(
  "SELECT distinct category_main
   FROM $wpdb->purchases_category"
) );
foreach($option_datas as $row_data) { ?>
  p_items.push("<?php echo $row_data->category_main ?>");
<?php } ?>
//収入大項目の取得
//大項目配列データの作成（収入用作成 切替機能 db化 要）
var i_items = [];
<?php
$option_datas = $wpdb->get_results( $wpdb->prepare(
  "SELECT distinct category_main
   FROM $wpdb->incomes_category"
) );
foreach($option_datas as $row_data) { ?>
  i_items.push("<?php echo $row_data->category_main ?>");
<?php } ?>
</script>

<head>
  <script type="text/javascript">
    $(document).ready(function()
      {
        $("#purchases_table").tablesorter({
          //ソートしておく（0:昇順、1:降順）
          sortList: [
            [0,1],
            [1,1]
          ],
          //ソート機能を外す
          headers: {
            0: {sorter:false},
            2: {sorter:false},
            3: {sorter:false},
            4: {sorter:false},
            5: {sorter:false},
            6: {sorter:false},
            7: {sorter:false},
            8: {sorter:false},
            9: {sorter:false}
          }
        });
      }
    );
  </script>
</head>
<div class="house-hold">
  <section class="">
    <h4 class="heading-normal">月次収支</h4>
    <button class="manual_input" type="button" onclick="hiddenBtn()">
      <img style="height:50px" src="http://moneyform.verse.jp/wp-content/uploads/manual/manual-pen.png">
        <strong>手入力</strong>
    </button>
    <!-- 非表示ボックス -->
    <div id="dialog-form" class="dialog-form">
      <h3>家計簿入力</h3>
      <ul class="graph-change">
        <li id="purchases-type-li" class="selection-li select_item">
          <label class="selection-label" for="purchases">支出</label>
          <input class="select_radio" value="purchases-type" id="purchases" type="radio" name="type" checked>
        </li>
        <li id="incomes-type-li" class="selection-li">
          <label class="selection-label" for="incomes">収入</label>
          <input class="select_radio" value="incomes-type" id="incomes" type="radio" name="type">
        </li>
        <li id="transfer-type-li" class="selection-li">
          <label class="selection-label" for="transfer">振替</label>
          <input class="select_radio" value="transfer-type" id="transfer" type="radio" name="type">
        </li>
      </ul>
      <form name="submit" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="post_method" value="Y">
        <!-- 支出の入力項目 -->
        <div id="purchases-type" class="purchases-type span_class">
          <input class="select_disabled" type="hidden" name="type" value="支出">
          <div class="control-group line-color">
            <?php
            //日本時間に設定
            date_default_timezone_set('Asia/Tokyo');
            $day_value = date('Y/m/d');
            ?>
            <label class="control-label" for="">日付</label>
            <input class="select_disabled" required= "required" style="width:190px; height:25px" type="text" name="created" id="datepicker-p" value="<?php echo $day_value ?>">
            <script>
            $('#datepicker-p').datepicker({
              changeMonth: true,
              changeYear: true,
              dateFormat: 'yy/mm/dd',
              duration: 300,
              showAnim: 'slideDown'
            });
            </script>
          </div>
          <div class="control-group line-color" style="height:120px">
            <label class="control-label" for="">支出金額<br>(支出元）</label>
            <div class="">
              <div class="">
                <input id="purchases-type-price" class="select_disabled selection-price submit_price" style="width:90%; height:25px" type="number" name="price" data-type="number">
                <span>円</span>
              </div>
              <div class="">
                <select style="width:90%" id="wallet_p_source" class="category-buttom select_disabled" name="wallet_source">
              	</select>
                <script>
                  getOption("wallet_p_source", wallet_items);
                </script>
              </div>
            </div>
          </div>
          <div class="control-group line-color" style="height:120px">
            <label class="control-label" for="">項目</label>
            <div class="">
              <!-- 大項目 -->
              <div class="">
                <select id="category_p_main" class="category-menue category-buttom select_disabled" name="category_main"
                        onChange="functionName('category_form', 'category_p_main', 'category_p_sub')">
                </select>
              </div>
              <!-- 中項目 -->
              <div class="">
                <select id="category_p_sub" class="category-menue category-buttom select_disabled" name="category_sub" >
                </select>
              </div>
              <script>
                getOption("category_p_main", p_items);
                //初期ロード時呼び出し用
                functionName('category_form', 'category_p_main', 'category_p_sub');
              </script>
              <input id="purchases-type-name" class="select_disabled selection-name submit_name" style="height:25px" type="text" name="text" value="" placeholder="内容を入力してください">
            </div>
          </div>
          <div style="height:0" class="control-group line-color"></div>
        </div>
        <!-- 収入の入力項目 -->
        <div id="incomes-type" class="incomes-type span_class">
          <input class="select_disabled" type="hidden" name="type" value="収入">
          <div class="control-group line-color">
            <label class="control-label" for="">日付</label>
            <input class="select_disabled" required= "required" style="width:190px; height:25px" type="text" name="created" id="datepicker-i" value="<?php echo $day_value ?>">
            <script>
            $('#datepicker-i').datepicker({
              changeMonth: true,
              changeYear: true,
              dateFormat: 'yy/mm/dd',
              duration: 300,
              showAnim: 'slideDown'
            });
            </script>
          </div>
          <div class="control-group line-color" style="height:120px">
            <label class="control-label" for="">収入金額<br>(入金先）</label>
            <div class="">
              <div class="">
                <input id="incomes-type-price" class="select_disabled selection-price" style="width:90%; height:25px" type="number" name="price" data-type="number">
                <span>円</span>
              </div>
              <div class="">
                <select style="width:90%" id="wallet_i_source" class="category-buttom select_disabled" name="wallet_source">
              	</select>
                <script>
                  getOption("wallet_i_source", wallet_items);
                </script>
              </div>
            </div>
          </div>
          <div class="control-group line-color" style="height:120px">
            <label class="control-label" for="">項目</label>
            <div class="">
              <!-- 大項目 -->
              <div class="">
                <select id="category_i_main" class="category-menue category-buttom select_disabled" name="category_main"
                        onChange="functionName('category_form', 'category_i_main', 'category_i_sub')">
                </select>
              </div>
              <!-- 中項目 -->
              <div class="">
                <select id="category_i_sub" class="category-menue category-buttom select_disabled" name="category_sub" >
                </select>
              </div>
              <script>
                getOption("category_i_main", i_items);
                //初期ロード時呼び出し用
                functionName('category_form', 'category_i_main', 'category_i_sub');
              </script>
              <input id="incomes-type-name" class="select_disabled selection-name" style="height:25px" type="text" name="text" value="" placeholder="内容を入力してください">
            </div>
          </div>
          <div style="height:0" class="control-group line-color"></div>
        </div>
        <!-- 振替の入力項目 -->
        <div id="transfer-type" class="transfer-type span_class">
          <input class="select_disabled" type="hidden" name="type" value="振替">
          <div class="control-group line-color">
            <label class="control-label" for="">日付</label>
            <input class="select_disabled" required= "required" style="width:190px; height:25px" type="text" name="created" id="datepicker-t" value="<?php echo $day_value ?>">
            <script>
            $('#datepicker-t').datepicker({
              changeMonth: true,
              changeYear: true,
              dateFormat: 'yy/mm/dd',
              duration: 300,
              showAnim: 'slideDown'
            });
            </script>
          </div>
          <div class="control-group line-color" style="height:120px">
            <label class="control-label" for="">振替金額</label>
            <div class="">
              <div class="">
                <input id="transfer-type-price" class="select_disabled selection-price" style="width:90%; height:25px" type="number" name="price" data-type="number">
                <span>円</span>
              </div>
              <input id="transfer-type-name" class="select_disabled selection-name" style="height:25px" type="text" name="text" value="" placeholder="内容を入力してください">
            </div>
          </div>
          <!-- 振替場所 -->
          <div class="control-group line-color" style="height:120px">
            <label class="control-label" for="">振替場所</label>
            <div class="">
              <span>振替元： </span>
              <select id="transfer-from" class="transfer-select category-menue category-buttom select_disabled" name="transfer-from">
              </select>
              <p class="down-arrow">↓</p>
              <span>振替先： </span>
              <select id="transfer-to" class="transfer-select category-menue category-buttom select_disabled" name="transfer-to">
              </select>
              <script>
                getOption("transfer-from", wallet_items);
                getOption("transfer-to", wallet_items);
                //初期呼び出し時に最終要素を選択
                $('#transfer-from').prop("selectedIndex", $('#transfer-from').children().length - 1);
                $('#transfer-to').prop("selectedIndex", $('#transfer-to').children().length - 1);
                //振替用振替元・振替先確認
                $('.transfer-select').change(function() {
                  var transferFrom = $('#transfer-from').val();
                  var transferTo = $('#transfer-to').val();
                  if(transferFrom != "なし" || transferTo != "なし") {
                    if(transferFrom == transferTo) {
                      $('.err-message').html("<strong>同じ口座同士での振替は出来ません。</strong>");
                    } else {
                      $('.err-message').html("");
                    }
                  }
                })
              </script>
            </div>
          </div>
          <div style="height:0" class="control-group line-color">
          </div>
          <p class="down-arrow err-message"></p>
        </div>
        <div class="control-group">
            <input type="submit" name="Submit" value="保存する" onclick="return checkSubmit()">
        </div>
      </form>
    </div>
    <div id="modal-backdrop" class="modal-backdrop" onclick="hiddenBtn()"></div>

    <script>
    //ホームからの手入力
    <?php if( "new" == $_GET['asset-act'] ) { ?>
      hiddenBtn();
    <?php } ?>
    </script>
    <p>
      登録した金融機関等のデータから、月々の収入、支出を自動的に計算します。<br>
    </p>
    <!-- ホームに同じ要素 -->
    <div class="monthly_total">
      <?php
      $month_text = ["当月収入", "当月支出", "当月収支"];
      $month_money = get_monthly_total($this_month);
      ?>
      <table>
        <tbody>
          <?php for($i = 0; $i <= 2; $i++) { ?>
          <tr class="cut-container">
            <th class="cut-container-20">
              <?php echo $month_text[$i] ?>
            </th>
            <td class="cut-container-80 text-right">
              <?php
              //数値によって文字色を変更する
              $font_color = 'black';
              if ($month_text[$i] == "当月収支") {
                if ($month_money[$i] < 0) {
                    $font_color = 'red';
                } else if ($month_money[$i] > 0){
                    $font_color = 'blue';
                }
              }
              ?>
              <p style="color:<?php echo $font_color ?>"><?php echo number_format($month_money[$i]). "円" ?>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="calendar">
  </section>

  <section id="money-detail" class="real-money">
    <h4 class="heading-normal">収入・支出詳細</h4>
    <p class="summary-link">
      入出金詳細では、自動取得された金融機関の情報に含まれる文言をもとに、システムにより項目を自動分類いたします（手動でもご変更いただけます）。<br>
      なお、 自動取得以外の手入力の情報については、項目は自動分類されませんので、ご了承ください。<br><br>
      収入・支出データは <a href="">収支内訳</a> および 月次推移 でまとめてご覧になれます。
    </p>
    <?php
    //一カ月の購入を取得
    $start_time = date('Y-m-01', strtotime($this_month. "month"));// .' 00:00:00';
    $end_time = date('Y-m-t', strtotime($this_month. "month"));// .' 23:59:59';
    ?>

    <div id="period_data_change" class="period_data_change">
      <div class="change_box">
        <input type="hidden" id="changeCount" value="<?php echo $this_month ?>">
        <input type="button" name="down" value="◄" onclick="change_month(this.name)">
        <span><strong><?php echo $start_time. " ～ " . $end_time ?> </strong></span>
        <input type="button" name="up" value="►" onclick="change_month(this.name)">
      </div>
    </div>
    <?php

    //収入と購入の値を取得・結合して日付順に並び変える
    $purchases_datas = $wpdb->get_results( $wpdb->prepare(
      "SELECT * FROM $wpdb->purchases
      WHERE created BETWEEN %s AND %s order by id desc",
      [$start_time, $end_time]
    ) );
    $incomes_datas = $wpdb->get_results( $wpdb->prepare(
      "SELECT * FROM $wpdb->incomes
      WHERE created BETWEEN %s AND %s order by id desc",
      [$start_time, $end_time]
    ) );
    $transfer_datas = $wpdb->get_results( $wpdb->prepare(
      "SELECT * FROM $wpdb->transfer
      WHERE created BETWEEN %s AND %s order by id desc",
      [$start_time, $end_time]
    ) );
    $row_datas = array_merge($purchases_datas, $incomes_datas, $transfer_datas);
    foreach($row_datas as $key)
    {
        $sort_createds[] = $key->created;
        $sort_ids[] = $key->id;
    }
    //日付順（降順）→id順（降順）で並び替え
    array_multisort($sort_createds, SORT_DESC, $sort_ids,  SORT_DESC, $row_datas);
    //各項目のwidthパーセンテージ
    $partition_list = ["4","8","12","10","12","12","16","12","8","8"];
    $sort_text = ["計算対象", "日付", "内容","金額（円）","保有金融機関","大項目","中項目","メモ","振替","削除"];
    $data_count = count($row_datas);
    $text_count = count($sort_text);
    ?>
    <table id="purchases_table" class="tablesorter">
      <thead>
        <tr class="cut-container">
          <?php for($i = 0; $i < $text_count; $i++) { ?>
          <th style="width:<?php echo $partition_list[$i] ?>%">
            <?php echo $sort_text[$i] ?>
          </th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>

        <?php
        //"+""-"マーク付与して数値によって文字色を変更する
        for($i = 0; $i < $data_count; $i++) {
          if($row_datas[$i]->type == "支出") {
            $price = "-" .(number_format($row_datas[$i]->price));
            $font_color = 'red';
          }else if($row_datas[$i]->type == "収入") {
            $price = "+" .(number_format($row_datas[$i]->price));
            $font_color = 'blue';
          }else if($row_datas[$i]->type == "振替") {
            $price = number_format($row_datas[$i]->price);
            $font_color = '';
          }

          $created  = $row_datas[$i]->created;
          $name = $row_datas[$i]->name;
          $info = $row_datas[$i]->info;
          $source = $row_datas[$i]->source;
          $category_main = $row_datas[$i]->category_main;
          $category_sub = $row_datas[$i]->category_sub;

          $source_from = $row_datas[$i]->source_from;
          $source_to = $row_datas[$i]->source_to;

          //計算対象確認
          $p_i_type = $row_datas[$i]->type;
          $p_i_id = $row_datas[$i]->id;
          $checkd = NULL;
          if($row_datas[$i]->target == 1) {
            $checkd = "checked";
          }
        ?>
          <tr class="cut-container hover-row" name="<?php echo $p_i_type.$p_i_id ?>">
            <!-- <form name="<?php echo 'category_form'. $i ?>" method="post" action="<?php echo ($_SERVER['REQUEST_URI']); ?>"> -->
            <!-- <input type="hidden" name="post_method" value="Y">
            <input type="hidden" name="id" value="<?php echo $p_i_id ?>" >
            <input type="hidden" name="type" value="<?php echo $p_i_type ?>" > -->
                <!-- 計算対象 -->
              <td style="width:4%;">
                  <!-- チェック状況を個別に判断 -->
                  <input onchange="targetChangeBtn(this.name, this.value)" type="checkbox" name="<?php echo $p_i_type. $p_i_id ?>" value="<?php echo $p_i_type ?>" <?php echo $checkd ?> style="width: 35px;">
              </td>
              <!-- 日付 -->
              <td style="width: 8%">
                <span class="balloonoya">
                  <input id=<?php echo "datepicker". $i ?> onchange="targetChangeIpt(this.name, this.value)" type="text" name=<?php echo $p_i_id. "-" .$p_i_type. "-created" ?> value="<?php echo $created ?>" readonly>
                  <script>
                  $("<?php echo '#datepicker'. $i ?>").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd',
                    duration: 300,
                    showAnim: 'slideDown'
                  });
                  </script>
                  <span class="balloon house">クリックして編集し、Enterキーで変更できます。</span>
                </span>
              </td>
              <!-- 内容 -->
              <td style="width: 12%">
                <span class="balloonoya">
                  <input onchange="targetChangeIpt(this.name, this.value)" type="text" name=<?php echo $p_i_id. "-" .$p_i_type. "-name" ?> value="<?php echo $name ?>" />
                  <span class="balloon house">クリックして編集し、Enterキーで変更できます。</span>
                </span>
              </td>
              <!-- 金額（円） -->
              <td style="width: 10%" name="price">
                <span class="balloonoya">
                  <input id=<?php echo "price". $i ?> style="color:<?php echo $font_color ?>" onclick="getNumberOnly(this.id, this.value)" onchange="targetChangeIpt(this.name, this.value)" type="text" name=<?php echo $p_i_id. "-" .$p_i_type. "-price" ?>  value="<?php echo $price ?>" />
                  <span class="balloon house">クリックして編集し、Enterキーで変更できます。</span>
                </span>
              </td>
              <!-- 保有金融機関 -->
              <td style="width: 12%">
                <?php if(isset($source_from)) { ?>
                  <span class="balloonoya">
                    <input readonly type="text" name="<?php echo "source_from" .$p_i_id ?>" value="<?php echo $source_from ?>">
                    <input readonly type="text" name="<?php echo "source_to" .$p_i_id ?>" value="<?php echo $source_to ?>">
                    <span class="balloon2">振替元<br>  ↓  <br>振替先</span>
                  </span>
                <?php } else { ?>
                  <select id="<?php echo "wallet_source". $i ?>" class="category-menue category-buttom" style="width:100%;" onchange="targetChangeIpt(this.name, this.value)" name=<?php echo $p_i_id. "-" .$p_i_type. "-source" ?> >
                  </select>
                  <script>
                    getOption("<?php echo "wallet_source". $i ?>", wallet_items);
                    //初期設定の選択肢
                    $("<?php echo '#wallet_source'. $i .' option[value='. $source .']'?>").prop('selected', true);
                  </script>
                <?php } ?>
              </td>
              <!-- 大項目 -->
              <td style="width: 12%">
                <?php if(!isset($source_from)) { ?>
                  <select id="<?php echo "category_main". $i ?>" class="category-menue category-buttom" style="width:100%;" name=<?php echo $p_i_id. "-" .$p_i_type. "-category_main" ?>
                          onChange="functionName('<?php echo "category_form". $i ?>', '<?php echo "category_main". $i ?>', '<?php echo "category_sub". $i ?>'); targetChangeIpt(this.name, this.value)">
                  </select>
                  <script>
                    <?php if($p_i_type == "支出") { ?>
                      getOption("<?php echo "category_main". $i ?>", p_items);
                    <?php } elseif($p_i_type == "収入") { ?>
                      getOption("<?php echo "category_main". $i ?>", i_items);
                    <?php } ?>
                    //初期設定の選択肢
                    $("<?php echo '#category_main'. $i .' option[value='. $category_main .']'?>").prop('selected', true);
                  </script>
                <?php } ?>
              </td>
              <!-- 中項目 -->
              <td style="width: 16%">
                <select id="<?php echo "category_sub". $i ?>" class="category-menue category-buttom" style="width:100%; "
                        onchange="targetChangeIpt(this.name, this.value)" name=<?php echo $p_i_id. "-" .$p_i_type. "-category_sub" ?> >
                </select>
              </td>
              <!-- 初期ロード時呼び出し用 -->
              <script>
                functionName('<?php echo "category_form". $i ?>', '<?php echo "category_main". $i ?>', '<?php echo "category_sub". $i ?>', '<?php echo $category_sub ?>');
              </script>
              <!-- メモ -->
              <td style="width: 12%">
                <span class="balloonoya">
                  <input onchange="targetChangeIpt(this.name, this.value)" type="text" name=<?php echo $p_i_id. "-" .$p_i_type. "-info" ?>  value="<?php echo $info ?>" />
                  <span class="balloon house">クリックして編集し、Enterキーで変更できます。</span>
                </span>
              </td>
              <!-- 振替 -->
              <td id="transfer-bottom" style="width: 8%" onclick="dbTransfer('<?php echo $p_i_type ?>', '<?php echo $p_i_id ?>', '<?php echo $row_datas[$i]->price ?>', '<?php echo $created ?>') ">
              </td>
              <!-- 削除 -->
              <td style="width: 8%">
                <input type="submit" name="Delete" value="削除" onclick="dbDelete('<?php echo $p_i_type ?>', '<?php echo $p_i_id ?>')">
              </td>
            <!-- </form> -->
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </section>
  <section id="money-export">
    <h4 class="heading-normal">家計簿データの出力（Excel形式対応）</h4>
    <button style="margin:0 0 10px 0" type="button" id="dl-xlsx">Download Excel</button>
    <!-- 出力用テーブル -->
    <table style="display:none" id="export_table" data-sheet-name="入出金データ">
      <thead>
        <tr>
          <?php for($i = 0; $i < $text_count; $i++) { ?>
          <th>
            <?php echo $sort_text[$i] ?>
          </th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <?php for($i = 0; $i < $data_count; $i++) {
          $created  = $row_datas[$i]->created;
          $name = $row_datas[$i]->name;
          $info = $row_datas[$i]->info;
          $price = $row_datas[$i]->price;
          $source = $row_datas[$i]->source;
          $category_main = $row_datas[$i]->category_main;
          $category_sub = $row_datas[$i]->category_sub;

          $target = $row_datas[$i]->target;
          $type = $row_datas[$i]->type;
          $source_from = $row_datas[$i]->source_from;
          $source_to = $row_datas[$i]->source_to;

          if($target == 1) {
            $target = "〇";
          } else {
            $target = "×";
          }

          if($type == "振替") {
            $output_list = ["", $created, $name, $price, $source_from. "→" .$source_to, "", "", $info, "〇"];
          }else{
            $output_list = [$target, $created, $name, $price, $source, $category_main, $category_sub, $info];
          }
          ?>
          <tr>
            <?php for($j = 0; $j < count($output_list); $j++) { ?>
            <td>
              <?php echo $output_list[$j] ?>
            </td>
            <?php } ?>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.9.10/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
    <!-- <script src="export-xlsx.js"></script> -->
    <script type="text/javascript">

    document.getElementById('dl-xlsx').addEventListener('click', function () {
      if(confirm("Excelファイルをダウンロードしますか？")) {
        $('#export_table').show();
         var wopts = {
           bookType: 'xlsx',
           bookSST: false,
           type: 'binary'
         };

         var workbook = {SheetNames: [], Sheets: {}};

         document.querySelectorAll('table#export_table').forEach(function (currentValue, index) {
           // sheet_to_workbook()の実装を参考に記述
           var n = currentValue.getAttribute('data-sheet-name');
           if (!n) {
             n = 'Sheet' + index;
           }
           workbook.SheetNames.push(n);
           workbook.Sheets[n] = XLSX.utils.table_to_sheet(currentValue, wopts);
         });

         var wbout = XLSX.write(workbook, wopts);

         function s2ab(s) {
           var buf = new ArrayBuffer(s.length);
           var view = new Uint8Array(buf);
           for (var i = 0; i != s.length; ++i) {
             view[i] = s.charCodeAt(i) & 0xFF;
           }
           return buf;
         }

         saveAs(new Blob([s2ab(wbout)], {type: 'application/octet-stream'}), '<?php echo $start_time. " ～ " . $end_time. ' 入出金データ.xlsx'?>');
         $('#export_table').hide();
       }
     }, false);
    </script>
  </section>
</div>
<script>
//リンク先の変更
var changed = $("#changeCount").val();
$(".summary-link a").attr("href", "summary/?month=" + changed);
//振替の編集
$('[name^=振替]').addClass('no-form');
$('[name^=振替] input[type=checkbox]').hide();
$('[name^=振替] select').hide();
$('[name^=振替] input').attr('readonly','readonly');
$('[name^=振替] [name*=price]').removeAttr('onclick');
$('[name^=振替] .balloon').remove();
$('[name^=振替] [name=price]').append('（振替）');
$('[name^=振替] #transfer-bottom').html('<input type="submit" name="transfer" value="振替">');
</script>
<?php get_footer(); ?>
