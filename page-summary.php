<?php  get_header();?>
<?php
  if(isset($_GET["month"])) {
    $this_month = $_GET["month"];
  } else {
    $this_month = 0;
  }
?>
<div class="summary">
  <script>
  function change_month(name) {
  var changed = $("#changeCount").val();
  if(name == "down") {
    changed --;
  } else if(name == "up") {
    changed ++;
  }
  $("#changeCount").val(changed);

  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: {
      'action': 'ajax_summary_change',
      'this_month': changed
    },
    dataType:'json'
  }).done(function(date){
      // 集計期間の表示
      console.log(date);
      changed = Number(changed);
      var monthly_total = date[0];
      var category_main_datas = date[1];
      var category_main_sums = date[2];
      var month_sum = date[3];
      // category_main_sums = category_main_sums.map(Number);
      start_time = new Date();
      start_time.setDate(1);
      start_time.setMonth(start_time.getMonth() + changed);
      end_time = new Date(start_time.getFullYear(), start_time.getMonth() + 1, 0);
      format_start_time = dateToStr24HPad0(start_time, 'YYYY/MM/DD');
      format_end_time = dateToStr24HPad0(end_time, 'YYYY/MM/DD');
      $('.display-time').html("<strong>" + format_start_time + "～" + format_end_time + "</strong>");
      //当月収入・支出・収支の表示
      var num = 0;
      for(var i=0; i<3; i++) {
        num = separate(monthly_total[i]);
        $(".p" + i).text(num + "円");
      }
      //収支に色を付与
      $(".p2").removeClass("price-color-blue");
      $(".p2").removeClass("price-color-red");
      if(monthly_total[2] > 0) {
        $(".p2").addClass("price-color-blue");
      } else if(monthly_total[2] < 0){
        $(".p2").addClass("price-color-red");
      }
      //合計の表示
      $(".total_spending").html("<strong>合計：" + separate(monthly_total[1]) + "円</strong>");
      //リンク先の変更
      $(".household-link a").attr("href", "../?month=" + changed + "#period_data_change")
      //詳細の表示
      // var $row = $(".in-out-tr").closest("tr");
      // var $newRow = $row.clone(true);
      $(".in-out-tr").remove();

      for(var i=0; i<category_main_datas.length; i++) {
        var category_par = Math.round((category_main_sums[i] / month_sum) * 10000) / 100;
        var print_datas = [category_main_datas[i], separate(category_main_sums[i]) + "円", category_par + "％"];
        $("#summary_tbody").append(
          $("<tr class='cut-container'></tr>")
            .append($("<td></td>").text(print_datas[0]),
                    $("<td></td>").text(print_datas[1]),
                    $("<td></td>").text(print_datas[2]))
        );
        }
      $("#summary_tbody tr").addClass("in-out-tr");
      // グラフの描画
      var background_color = getCategoryColor(category_main_datas);
      console.log(background_color);
      var ctx = document.getElementById('pieChart-summary').getContext('2d');
      ctx.canvas.height = 320;
      //すでにグラフがあるときは破棄する
      if(typeof myChart !== 'undefined' && myChart) {
        myChart.destroy();
      }
      window.myChart = new Chart(ctx, {
      type: 'pie',
      data: {
       labels: category_main_datas,
       datasets: [{
         data: category_main_sums,
         borderWidth: 1,
         backgroundColor: background_color,
         borderColor: 'rgba(0,0,0,1.0)',
        }]
       },
       options: {
         tooltips: {
           callbacks: {
             label: function(tooltipItem, data){
               return [data.labels[tooltipItem.index] + "：" + data.datasets[0].data[tooltipItem.index].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + "円"];
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
  // labelを常に表示する
  Chart.plugins.register({
    afterDatasetsDraw: function (chart, easing) {
      var ctx = chart.ctx;
      var dataSum = 0;
      chart.data.datasets.forEach(function (dataset, i) {
        dataset.data.forEach(function (element){
            dataSum += element;
        });
        var meta = chart.getDatasetMeta(i);
        if (!meta.hidden) {
          meta.data.forEach(function (element, index) {
            ctx.fillStyle = 'rgb(255, 255, 255)';

            var fontSize = 16;
            var fontStyle = 'normal';
            var fontFamily = 'Helvetica Neue';
            ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

            var dataString = chart.data.labels[index];

            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            var padding = 5;
            var position = element.tooltipPosition();
            // 割合１０％以上の時に表示
            if((dataset.data[index] / dataSum) >= 0.1) {
              ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
            }
          })
        }
      })
    }
  });
  </script>
  <div id="period_data_change" class="period_data_change">
    <div class="change_box">
      <input type="hidden" id="changeCount" value="<?php echo $this_month ?>">
      <input type="button" name="down" value="◄" onclick="change_month(this.name)">
      <span class="display-time"><strong>Loading...</strong></span>
      <input type="button" name="up" value="►" onclick="change_month(this.name)">
    </div>
  </div>
  <section class="">
    <div class="monthly_total">
      <?php
      $month_text = ["当月収入", "当月支出", "当月収支"];
      ?>
      <table>
        <tbody>
          <?php for($i = 0; $i <= 2; $i++) { ?>
          <tr class="cut-container">
            <th class="cut-container-20">
              <?php echo $month_text[$i] ?>
            </th>
            <td class="cut-container-80 text-right">
              <p class="in-out-p <?php echo 'p'.$i ?>"></p>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
  <section class="">
    <h4 class="heading-normal">支出</h4>
    <script>
      var changed = $("#changeCount").val();
    </script>
    <p class="household-link">
      月々の支出内訳をご覧いただけます。<br>
      他の月をご覧になりたい場合は、上部にある「前月」「次月」で選択してください。<br>
      <a href="">家計簿の収入・支出詳細</a> で、計算対象にチェックをいれた項目が集計対象となりますので、
      追加・変更は <a href="">家計簿の収入・支出詳細</a> から行ってください。
    </p>
    <p class="total_spending"></p>

    <?php
    $title_text = ["項目", "金額", "割合"];
    $text_count = count($title_text);
    ?>
    <div style="max-width:500px; min-width:350px">
      <table id="summary_table">
        <thead>
          <tr class="cut-container">
            <?php for($i = 0; $i < $text_count; $i++) { ?>
            <th>
              <?php echo $title_text[$i] ?>
            </th>
            <?php } ?>
          </tr>
        </thead>
        <tbody id="summary_tbody">
        </tbody>
      </table>
      <!-- 円グラフの出力 -->
      <div class="canvas-box">
        <canvas id="pieChart-summary"></canvas>
      </div>
      <div class="pull-right household-link">
        <a class="arrow right1" href="">支出詳細へ</a>
      </div>
    </div>
  </section>
  <script>
    //初回呼び出し用
    change_month();
  </script>
<?php get_footer(); ?>
