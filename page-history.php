<?php  get_header();?>
<div class="history">
  <p>
    登録した銀行や証券会社の残高の推移がご覧いただけます。 金融機関の追加、修正は、 金融機関の設定 から行えます。<br>
    グラフの凡例をクリックすると、対象項目を含めたり、消したりできます。
  </p>
  <section class="total-cell-list">
    <script>
    var days = 0;
    var today = new Date();
    //月末日を取得
    var last_day = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    last_day = last_day.getDate();
    var current_day = today.getDate();

    function period_data_change(name="month"){
      //ボタン背景色で選択中表現（無理矢理）
      $('li[name=' + name + ']').addClass("select_item");
      if(name == "year") {
        $('li[name=month]').removeClass("select_item");
        days = 365;
      } else if (name == "month") {
        $('li[name=year]').removeClass("select_item");
        days = last_day;
      }
      $.ajax({
        type: "POST",
        url: ajaxurl,
        data: {
          'action': 'ajax_get_total_asset',
          'days' : days
              },
        dataType:'json'
        }).done(function(asset_list){
        /* 通信成功時 */
        console.log(asset_list);
        // $("th:eq(1), th:eq(3)").addClass("smart-hidden");
        $('#asset_table th:odd, #asset_table td:odd').addClass('smart-hidden');
        $('#asset_table th:even, #asset_table td:even').addClass('even-width');
        for(var i=0; i < current_day; i++) {
          real_asset = asset_list[0];
          point_asset = asset_list[1];
          var sumNum = real_asset[i] + point_asset[i];
          sumNum = separate(sumNum) + "円";
          $(".sum" + i).text(sumNum);
          var realNum = separate(real_asset[i]) + "円";
          $(".real" + i).text(realNum);
          var pointNum = separate(point_asset[i]) + "円";
          $(".point" + i).text(pointNum);
        }
        var month_list = getMonthList(days);
        var ctx = document.getElementById('lineChart-show').getContext('2d');
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
                data: real_asset.reverse(),
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
    <div class="canvas-box">
      <canvas id="lineChart-show"></canvas>
    </div>
  </section>

  <section id="asset-money" class="real-money">
    <?php
    $sort_text = ["日付", "合計","預金・現金・仮想通貨","ポイント"];
    $partition = 100 / count($sort_text);
    $data_count = 31;
    $text_count = count($sort_text);
    ?>
    <table id="asset_table">
      <thead>
        <tr class="cut-container">
          <?php for($i = 0; $i < $text_count; $i++) { ?>
          <th>
            <?php echo $sort_text[$i] ?>
          </th>
          <?php } ?>
        </tr>
      </thead>
      <?php
      date_default_timezone_set('Asia/Tokyo');
      $today = new DateTime();
      $today_y = $today->format('Y');
      $today_m = $today->format('m');
      $today_d = $today->format('d');
      $month_end_asset = [];
      //月末資産配列の作成
      for($i=1; $i<=12; $i++) {
    	  $end_time = date('Y-m-t', strtotime("-" .$i. "month"));
    	  $data = $wpdb->get_var( $wpdb->prepare(
    	    "SELECT sum(wp1_asset.price) FROM
    	    (SELECT wallet_name, max(created) AS created
    	    FROM $wpdb->asset WHERE created <= %s
    	    GROUP BY wallet_name) AS wp2_asset
    	    LEFT JOIN
    	    (SELECT * FROM $wpdb->asset) AS wp1_asset
    	    ON wp2_asset.wallet_name = wp1_asset.wallet_name AND wp2_asset.created = wp1_asset.created",
    	    $end_time
    	  ));
        if(empty($data)) {
          $data = 0;
        }
        array_push($month_end_asset, $data);
      }
      ?>
      <tbody>
        <?php for($i = 0; $i < $today_d; $i++) { ?>
          <?php $current_time = date('Y-m-d', strtotime("-" .$i. "day")); ?>
        <tr class="cut-container hover-row">
          <td>
            <?php echo $current_time ?>
          </td>
          <!-- 合計 -->
          <td class="<?php echo "sum" .$i ?>"></td>
          <!-- 貯金・預金・仮想通貨 -->
          <td class="<?php echo "real" .$i ?>"></td>
          <!-- 要作成 -->
          <td class="<?php echo "point" .$i ?>"></td>
        </tr>
        <?php } ?>

        <?php for($i = 0; $i < count($month_end_asset); $i++) { ?>
          <?php
           $target_month = $today_m - $i - 1;
           if($target_month == 0) {
             $target_month = 12;
             $today_m = 12 + $i + 1;
             $today_y -= 1;
           }
          ?>
        <tr class="cut-container hover-row">
          <td>
            <?php echo $today_y. "年" .$target_month. "月末" ?>
          </td>
          <td>
          <?php echo  number_format($month_end_asset[$i]). "円" ?>
          </td>
          <td>
          <?php echo  number_format($month_end_asset[$i]). "円" ?>
          </td>
          <td>
          <?php echo  number_format(0). "円" ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </section>
</div>
<script>
//初回呼び出し用
  period_data_change();
</script>

<?php get_footer(); ?>
