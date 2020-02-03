<?php  get_header();?>
<head>
  <script type="text/javascript">
    $(document).ready(function()
      {
        $("#real_money_table").tablesorter({
          //ソートしておく（0:昇順、1:降順）
          sortList: [
            [0,1],
            [1,1]
          ],
          //ソート機能を外す
          headers: {
            3: {sorter:false},
            4: {sorter:false}
          }
        });
        $("#point_money_table").tablesorter({
          sortList: [
            [0,1],
            [4,1]
          ],
          headers: {
            5: {sorter:false},
            6: {sorter:false}
          }
        });
      }
    );
  </script>
</head>

<body>
  <div class="portfolio">
    <section class="asset-composition">
      <h4 class="heading-normal">資産構成</h4>
      <p>登録した銀行や証券会社の残高、詳細がご覧いただけます。 金融機関の追加、修正は、 <a href="../accounts">金融機関の設定</a> から行えます。<br>
      不動産など他資産を追加する場合は、 <a href="#">手入力で資産を追加</a> から行ってください。<br>
      万が一、データに不具合等があった場合は、お手数ですが、 <a href="../contact">お問い合わせフォーム</a> よりご連絡ください。</p>
      <div class="total-graph-left">
        <?php
        //総資産の取得
        $row_datas = $wpdb->get_results( $wpdb->prepare(
          "SELECT * FROM $wpdb->wallet"
        ) );
        $real_money = 0;
        foreach($row_datas as $row_data){
         if ($row_data->type == "財布"
             || $row_data->type == "銀行"
             || $row_data->type == "電子マネー")	{
           $real_money += $row_data->price;
         } elseif ($row_data->type == "ポイント") {
           $point += $row_data->price;
         } else{
           $other += $row_data->price;
         }
           $total_money += $row_data->price;
         }

         $par_real = round(($real_money / $total_money) * 100, 2);
         $par_point = 100 - $par_real;
        ?>

      </div>
      <div class="main-box">
        <div class="container-50">
          <!-- 円グラフの出力 -->
           <div class="canvas-box">
             <canvas id="myChart"></canvas>
           </div>
           <script>
             var ctx = document.getElementById('myChart').getContext('2d');
             ctx.canvas.height = 280;
             var myChart = new Chart(ctx, {
             type: 'pie',
             data: {
              labels: ["貯金・現金・仮想通貨", "ポイント"],
              datasets: [{
                backgroundColor: [
                    "#3C00FF",
                    "#BB5179",
                    //"#FAFF67"
                ],
                 data: [<?php echo $real_money ?>, <?php echo $point ?>, <?php echo $other ?>]
               }]
              },
              options: {
                title: {
                  display: false,
                  fontSize: 25,
                  text: '<?php echo number_format($total_money) ?> 円'
                },
                tooltips: {
                  callbacks: {
                    label: function(tooltipItem, data){
                      return [data.labels[tooltipItem.index] + "：" + data.datasets[0].data[tooltipItem.index].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + "円"];
                    }
                  }
                }
              },
              });
            </script>
          </div>
          <div class="container-50">
            <div class="pull-right">
              <a href="#">手入力で資産を追加</a>
              <a href="../../accounts/service-list/#accounts-regist-menu">　金融機関を追加</a>
            </div>
            <p>資産総額：<?php echo number_format($total_money) ?>円 </p>
            <div id="asset-breakdown">
              <h4 class="heading-small">資産の内訳</h4>
              <table>
                <tbody>
                  <tr class="cut-container">
                    <th style="width:50%">
                      <a href="#real-money">預金・現金・仮想通貨</a>
                    </th>
                    <td style="width:25%" class= "text-right">
                      <p><?php echo number_format($real_money). "円" ?></p>
                    </td>
                    <td style="width:25%" class= "text-right">
                      <p><?php echo $par_real. "％" ?> </p>
                    </td>
                  </tr>
                  <tr class="cut-container">
                    <th style="width:50%">
                      <a href="#point-money">ポイント</a>
                    </th>
                    <td style="width:25%" class= "text-right">
                      <p><?php echo number_format($point). "円" ?></p>
                    </td>
                    <td style="width:25%" class= "text-right">
                      <p><?php echo $par_point. "％" ?> </p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

    <section id="real-money" class="real-money">
      <h4 class="heading-normal">預金・現金・仮想通貨</h4>
      <h4 class="heading-small">合計：<?php echo number_format($real_money). "円" ?></h4>
      <?php
      $row_datas = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM $wpdb->wallet WHERE type_sub = '現金'"
      ));
      $sort_text = ["種類・名称", "残高", "保有金融機関","変更","削除"];
      $data_count = count($row_datas) - 1;
      $text_count = count($sort_text) - 1;
      ?>
      <table id="real_money_table" class="tablesorter">
        <thead>
          <tr class="cut-container">
            <?php for($i = 0; $i <= $text_count; $i++) { ?>
            <th style="width:15%">
              <?php echo $sort_text[$i] ?>
            </th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php for($i = 0; $i <= $data_count; $i++) {
                $data_list = [
                  $row_datas[$i]->name,
                  number_format($row_datas[$i]->price)."円",
                  $row_datas[$i]->possession
                  ] ?>
          <tr class="cut-container hover-row">
            <?php for($j = 0; $j <= $text_count; $j++) { ?>
              <td style="width:15%">
                <?php echo $data_list[$j] ?>
              </td>
            <?php } ?>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </section>

    <section id="point-money" class="point-money">
      <h4 class="heading-normal">ポイント</h4>
      <h4 class="heading-small">合計：<?php echo number_format($point). "円" ?></h4>
      <?php
      $row_datas = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM $wpdb->wallet WHERE type_sub = 'ポイント'"
      ));
      $sort_text = ["名称", "種類", "ポイント数", "現在の価値", "保有金融機関","変更","削除"];
      $data_count = count($row_datas) - 1;
      $text_count = count($sort_text) - 1;
      ?>
      <table id="point_money_table" class="tablesorter">
        <thead>
          <tr class="cut-container">
            <?php for($i = 0; $i <= $text_count; $i++) { ?>
            <th style="width:12%">
              <?php echo $sort_text[$i] ?>
            </th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php for($i = 0; $i <= $data_count; $i++) {
                $data_list = [
                  $row_datas[$i]->name,
                  $row_datas[$i]->type,
                  number_format($row_datas[$i]->price)."ポイント",
                  number_format($row_datas[$i]->price * 1.00)."円",
                  $row_datas[$i]->possession
                  ] ?>
          <tr class="cut-container hover-row">
            <?php for($j = 0; $j <= $text_count; $j++) { ?>
              <td style="width:12%">
                <?php echo $data_list[$j] ?>
              </td>
            <?php } ?>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </section>

  </div>

</body>

<?php get_footer(); ?>
