<?php  get_header();?>

<?php
//ホームからの情報
if(isset($_GET['show-manual-act']))
  {
      $wallet_id = $_GET['id'];
      $show_manual_act = $_GET['show-manual-act'];

      $row_data = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $wpdb->wallet WHERE id = %d",
        $wallet_id
      ));
      $wallet_name = $row_data->name;
      $wallet_price = $row_data->price;
      $possession = $row_data->possession;
      //資産総額の取得
      $total_asset = $wpdb->get_var( $wpdb->prepare(
        "SELECT sum(price) FROM $wpdb->wallet WHERE possession = %s",
        $possession
      ));
      //残高詳細用データ
      $asset_list = $wpdb->get_results( $wpdb->prepare(
    		"SELECT * FROM $wpdb->wallet WHERE possession = %s",
        $possession
    	));
  }
?>

<script>
function showManualAct() {
  var showManualAct = $("[name=show-manual-act]").val();
  if(showManualAct == "show") {
    $(".balance-cell").show();
    $('#balance-cell .selection-price').addClass('submit_price');//入力判別用クラス
  } else if(showManualAct == "detail") {
    $(".asset-total-amount").show();
    $('#asset-total-amount .selection-price').addClass('submit_price');//入力判別用クラス
  }
}

function hiddenBtnShow(name, type){
  if(name=="add-price") {
    if(type == "new")
    {
      $('#asset-total-amount [name=old_wallet_name]').val('');
      $('#asset-total-amount [name=asset_name]').val('');
      $('#asset-total-amount [name=changed_price]').val('');
      $('#asset-total-amount [name=created]').val('');
      $('#asset-total-amount [name=act_type]').val('new');
      $('#new-msg').show();
    }
    else if(type == "change")
    {
      $('#asset-total-amount [name=act_type]').val('change');
      $('#new-msg').hide();
    }
    $('#asset-total-amount .dialog-form').show();
    $('#modal-backdrop').show();
  } else if(name=="change-price"){
    $('#balance-cell .dialog-form').show();
    $('#modal-backdrop').show();
  } else {
    $('.dialog-form').hide();
    $('#modal-backdrop').hide();
  }
}

function dbChange(name, id){
  var assetName = $('[name=財布' + id + '] [name=name]').val();
  var price = $('[name=財布' + id + '] [name=price]').val();
  var created = $('[name=財布' + id + '] [name=created]').val();
  $('#asset-total-amount [name=old_wallet_name]').val(assetName);
  $('#asset-total-amount [name=asset_name]').val(assetName);
  $('#asset-total-amount [name=changed_price]').val(price);
  $('#asset-total-amount [name=created]').val(created);
  hiddenBtnShow(name, 'change');
}
</script>

<input type="hidden" name="show-manual-act" value="<?php echo $show_manual_act ?>">
<div class="show-manual">
    <!-- show -->
    <div id="balance-cell">
      <div id="dialog-form1" class="dialog-form">
        <h3>残高修正</h3>
        <div class="modal-body">
          <p>現在の残高をご入力ください。同時に入出金履歴への記帳も可能です。</p>
          <form name="balance-change" method="post" action="../../db-submit">
            <input type="hidden" name="post_method" value="Y">
            <input type="hidden" name="wallet_price" value=<?php echo $wallet_price ?> >
            <input type="hidden" name="wallet_id" value=<?php echo $wallet_id ?> >
            <input type="hidden" name="wallet_name" value=<?php echo $wallet_name ?> >
            <div class="control-group">
              <label class="control-label" for="">現在の残高</label>
              <span class="control-label" style="text-align: left"><?php echo number_format($wallet_price) ?>円</span>
            </div>
            <div class="control-group">
              <label class="control-label" for="">修正後の残高</label>
              <input class="selection-price" required="required" autocomplete="off" style="width:150px" type="number" name="changed_price" data-type="number">
              <span>円</span>
            </div>
            <div class="control-group">
              <label class="control-label" for="">差額の処理</label>
              <input id="checkbox" type="checkbox" name="checkbox" checked="checked">
              <label for="checkbox" style="font-size:14px">不明金として記帳</label>
            </div>
            <div class="control-group hidden-group">
              <?php
              //日本時間に設定
              date_default_timezone_set('Asia/Tokyo');
              $day_value = date('Y/m/d');
              ?>
              <label class="control-label" for="">記帳日(年/月/日)</label>
              <input required= "required" style="width:160px" type="text" name="created" id="datepicker" value="<?php echo $day_value ?>">
              <script>
              $(document).on('click', '#checkbox', function(){
                $('.hidden-group').toggle();
              })
              $('#datepicker').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy/mm/dd',
                duration: 300,
                showAnim: 'slideDown'
              });
              </script>
            </div>
            <div class="control-group">
                <input type="submit" name="Balance" value="この内容で登録する" onclick="return checkSubmit()">
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- detail -->
    <div id="asset-total-amount">
      <div id="dialog-form2" class="dialog-form">
        <h3>資産の新規追加</h3>
        <div class="modal-body">
          <p id="new-msg">新規追加する資産の情報をご入力ください。</p>
          <form name="balance-change" method="post" action="../../db-submit">
            <input type="hidden" name="post_method" value="Y">
            <input type="hidden" name="wallet_price" value=<?php echo $wallet_price ?> >
            <input type="hidden" name="wallet_id" value=<?php echo $wallet_id ?> >
            <input type="hidden" name="old_wallet_name">
            <input type="hidden" name="act_type">
            <!-- <input type="hidden" name="possession_name" value=<?php echo $possession ?> > -->
            <div class="control-group">
              <label class="control-label" for="">追加する口座</label>
              <span class="control-label" style="text-align: left"><strong><?php echo $possession ?></strong></span>
            </div>
            <div class="control-group">
              <label class="control-label" for="">資産の名称</label>
              <input class="submit_w_name" required="required" autocomplete="off" style="width:150px" type="text" name="asset_name">
            </div>
            <div class="control-group">
              <label class="control-label" for="">残高</label>
              <input class="selection-price" required="required" autocomplete="off" style="width:150px" type="number" name="changed_price" data-type="number">
              <span>円</span>
            </div>
            <div class="control-group">
              <?php
              //日本時間に設定
              date_default_timezone_set('Asia/Tokyo');
              $day_value = date('Y/m/d');
              ?>
              <label class="control-label" for="">購入日(年/月/日)</label>
              <input required= "required" style="width:160px" type="text" name="created" id="datepicker-asset" value="<?php echo $day_value ?>">
              <script>
              $('#datepicker-asset').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy/mm/dd',
                duration: 300,
                showAnim: 'slideDown'
              });
              </script>
            </div>
            <div class="control-group">
                <input type="submit" name="Asset" value="この内容で登録する" onclick="return checkSubmit()">
            </div>
          </form>
        </div>
      </div>
    </div>
    <div id="modal-backdrop" class="modal-backdrop" onclick="hiddenBtnShow()"></div>

    <h1 class="title"><?php echo $possession ?></h1>
    <!-- show -->
    <div class="balance-cell">
      <h6 class="heading-small">残高: <?php echo number_format($wallet_price) ?>円
        <input class="" type="button" name="change-price" value="残高修正" onclick="hiddenBtnShow(this.name)" style="display: inline; margin-left:10px;">
      </h6>
    </div>
    <!-- detail -->
    <div class="asset-total-amount">
      <h6 class="heading-small">資産総額: <?php echo number_format($total_asset) ?>円
        <input class="" type="button" name="add-price" value="手入力で資産を追加" onclick="hiddenBtnShow(this.name, 'new')" style="display: inline; margin-left:10px;">
      </h6>
    </div>
    <section class="total-cell-list">
      <!-- １カ月の現金推移の取得-->
      <script>
      var days = 0;
      var today = new Date();
      //月末日を取得
      var last_day = new Date(today.getFullYear(), today.getMonth() + 1, 0);
      last_day = last_day.getDate();
      //3カ月分の日数を取得
      // today.setDate(1);
      // today.setDate(today.getDate() - 1);
      // today.getDate();
      // console.log(today);
      function period_data_change(name="month"){
        //ボタン背景色で選択中表現（無理矢理）
        $('li[name=' + name + ']').addClass("select_item");
        if(name == "year") {
          $('li[name=month]').removeClass("select_item");
          days = 365;
        } else if (name == "month") {
          $('li[name=year]').removeClass("select_item");
          days = last_day;
        }// else if (name == "three") {
        //   days = count_day;
        // }
        var wallet_name = "<?php echo $wallet_name ?>";
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
                'action': 'ajax_day_change',
                'data_type' : 'total-cell-list',
                'wallet_name': wallet_name,
                'days' : days
                },
          dataType:'json'
        }).done(function(asset_list){
          /* 通信成功時 */
          var month_list = getMonthList(days);
          var ctx = document.getElementById('lineChart-show').getContext('2d');
          // ctx.clearRect(0, 0, 0, 0);
          ctx.canvas.height = 180;
          //すでにグラフがあるときは破棄する
          if(typeof myChart !== 'undefined' && myChart) {
            myChart.destroy();
          }
          window.myChart = new Chart(ctx, {
            type: 'line',
            data: {
              labels: month_list.reverse(),
              datasets: [
                {
                  label: "預金・現金・仮想通貨",
                  data: asset_list.reverse(),
                  backgroundColor: "rgba(60,0,255,1.0)",
                  pointStyle: "rectRounded",
                  borderWidth: 2,
                  borderColor: 'rgba(0,0,0,1.0)',
                  pointHitRadius: 6,
                  hoverRadius: 10,
                  hoverBorderWidth: 3
                },
              ]
            },
            options: {
              scales: {
                yAxes: [{
                  ticks: {
                    beginAtZero: true,
                    min: 0,
                  }
                }]
              },
              tooltips: {
                callbacks: {
                  label: function(tooltipItem, data){
                    return [data.datasets[0].label + "：" + data.datasets[0].data[tooltipItem.index].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + "円"];
                  }
                }
              },
              legend: {
                display: false //凡例の削除 → 全てのグラフ
             }
            }
          });
        })
      }

    </script>
      <?php
      // $asset_datas = $wpdb->get_results( $wpdb->prepare(
      //   "SELECT * FROM $wpdb->asset WHERE wallet_name = %s limit 31",
      //   $wallet_name
      // ));
      //
      // //グラフ範囲外の最新priceを初期値に代入
      // $today = new DateTime();
      // $end_time = date('Y-m-d', strtotime("-31 day"));
      // $befor_datas = $wpdb->get_results( $wpdb->prepare(
      //   "SELECT * FROM $wpdb->asset
      //   where created <= %s and wallet_name = %s order by created desc limit 1" ,
      //   [$end_time, $wallet_name]
      // ));
      // $befor_price = intval($befor_datas[0]->price);
      // //要素数を指定して配列を宣言（やらないと辞書型になる）
      // $month_c_asset = [];
      // $month_c_asset = array_pad($month_c_asset, 31, 0);
      // //残高推移グラフ用配列の作成
      // for($i=30; $i>=0; $i--) {
      //   $today_result = date('Y-m-d', strtotime("-" .$i ." day"));
      //   foreach($asset_datas as $asset_data){
      //     $asset_day = $asset_data->created;
      //     if ($today_result == $asset_day) {
      //       $befor_price = intval($asset_data->price);
      //       $month_c_asset[$i] = $befor_price;
      //       continue;
      //     }
      //   }
      //   //値が無い日付は前日のデータを使用する
      //   $month_c_asset[$i] = $befor_price;
      // }

      ?>
      <h4 class="heading-normal">残高推移</h4>
      <div id="graph-change" class="graph-change">
        <ul>
          <li name="month">
            <!-- 一カ月 -->
            <label class="selection-label" for="period-month">1カ月</label>
            <input id="period-month" type="radio" name="period" value="month" onclick="period_data_change(this.value)" checked>
          </li>
          <li name="year">
            <!-- 一年 -->
            <label class="selection-label" for="period-year">1年</label>
            <input id="period-year" type="radio" name="period" value="year" onclick="period_data_change(this.value)">
          </li>
        </ul>
      </div>
      <!-- 線グラフの出力 -->
      <script>
        period_data_change();
      </script>
      <div class="canvas-box">
        <canvas id="lineChart-show"></canvas>
      </div>
  </section>
  <section class="asset-total-amount">
    <!-- detail -->
    <h4 class="heading-normal">残高詳細</h4>
    <h6 class="heading-small">合計: <?php echo number_format($total_asset) ?>円</h6>

    <?php
    $row_datas = $wpdb->get_results( $wpdb->prepare(
      "SELECT * FROM $wpdb->wallet WHERE possession = %s",
      $possession
    ));
    $sort_text = ["種類・名称", "残高","変更","削除"];
    $data_count = count($row_datas);
    $text_count = count($sort_text);
    ?>
    <table id="real_money_table" class="tablesorter">
      <thead>
        <tr class="cut-container">
          <?php for($i = 0; $i < $text_count; $i++) { ?>
          <th style="width:15%">
            <?php echo $sort_text[$i] ?>
          </th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <?php for($i = 0; $i < $data_count; $i++) { ?>
        <tr class="cut-container hover-row" name="<?php echo '財布' .$row_datas[$i]->id ?>">
          <input type="hidden" name="created" value="<?php echo str_replace("-", "/", $row_datas[$i]->created) ?>">
          <input type="hidden" name="name" value="<?php echo $row_datas[$i]->name ?>">
          <input type="hidden" name="price" value="<?php echo $row_datas[$i]->price ?>">
          <td style="width:15%">
            <?php echo $row_datas[$i]->name ?>
          </td>
          <td style="width:15%">
            <?php echo number_format($row_datas[$i]->price)."円" ?>
          </td>
          <td style="width:15%">
            <input type="submit" name="add-price" value="編集" onclick="dbChange(this.name, '<?php echo $row_datas[$i]->id ?>')">
          </td>
          <td style="width:15%">
            <input type="submit" name="Delete" value="削除" onclick="dbDelete('財布', '<?php echo $row_datas[$i]->id ?>')">
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </section>
</div>

<script>
showManualAct();
</script>
<?php get_footer(); ?>
