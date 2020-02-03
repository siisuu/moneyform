<?php  get_header();?>
<?php
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

      $update_wallet = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $wpdb->wallet WHERE name = %s",
        $source
      ));
      $wallet_price = $update_wallet->price - $price;

      if (empty($price)) {
        $price_message = '金額は必ず入力してください';
      } else if  (iconv_strlen($price) > 12 ) {
        $price_message = '金額は12桁以内で入力してください';
      } else if  ($wallet_price < 0 ) {
        $price_message = $source. 'の残高を超えています。';
      } else if  (iconv_strlen($content_name) > 50 ) {
        $content_message = '内容は50文字以内で入力してください';
      } else {
      // データベースに登録
      $wpdb->insert('wp1_purchases',
        array(
          'type' => "支出",
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
      update_wallet_asset('支出', $source, $price, $created);

      // if($source != "なし") {
      //   $wpdb->update('wp1_wallet',
      //   array(
      //     'price' => $wallet_price
      //     ),
      //   array(
      //     'name' => $source
      //     ),
      //   array(
      //     '%d'
      //   ));
      // }
      //
      // date_default_timezone_set('Asia/Tokyo');
      // $today = new DateTime();
      // $today = $today->format('Y-m-d');
      // $check_data = $wpdb->get_row( $wpdb->prepare(
      //   "SELECT * FROM $wpdb->asset WHERE wallet_name = %s AND created = %s",
      //   [$source, $today]
      // ));
      // if(empty($check_data))
      // {
      // // ない時はデータベースに登録
      // $wpdb->insert('wp1_asset',
      //   array(
      //     'wallet_name' => $source,
      //     'price' => $wallet_price,
      //     'created' => current_time('mysql')
      //     ),
      //   array(
      //     '%s',
      //     '%d',
      //     '%s'
      //   ));
      // } else {
      //   //ある時はデータベースを更新
      //   $wpdb->update('wp1_asset',
      //   array(
      //     'price' => $wallet_price,
      //     'modified' => current_time('mysql')
      //     ),
      //   array(
      //     'wallet_name' => $source,
      //     'created' => $today
      //     ),
      //   array(
      //     '%d',
      //     '%s'
      //   ));
      // }
      //更新ボタンで再入力されるので初期化したい（リダイレクトで出来るかも（header使えない））
      // $source = NULL;
      // $category_main = NULL;
      // $category_sub = NULL;
      // $price = NULL;
      // $content_name = NULL;
      // $message = '登録処理が完了しました';
      // header("Location: {$_SERVER['PHP_SELF']}");
      // exit;
      ?>
      <script>
      //リダイレクトによる初期化
        document.location.href = location.href;
      </script>
      <?php
    }
  }
}
?>
  <div class="main-box">
    <div class="mypage-column left-column">
      <section class="total-graph">
        <h4 class="heading-normal">総資産</h4>

        <?php
        //総資産の取得
        $real_money = $wpdb->get_var( $wpdb->prepare(
          "SELECT sum(price) FROM $wpdb->wallet
           WHERE type_sub = '現金'"
        ) );

        $point = $wpdb->get_var( $wpdb->prepare(
          "SELECT sum(price) FROM $wpdb->wallet
           WHERE type_sub = 'ポイント'"
        ) );

        $total_money = $real_money + $point;
        // $row_datas = $wpdb->get_results( $wpdb->prepare(
        //   "SELECT * FROM $wpdb->wallet"
        // ) );
        // $real_money = 0;
        // foreach($row_datas as $row_data){
        //   if ($row_data->type == "財布"
        //   || $row_data->type == "銀行"
        //   || $row_data->type == "電子マネー")	{
        //     $real_money += $row_data->price;
        //   } elseif ($row_data->type == "ポイント") {
        //     $point += $row_data->price;
        //   } else {
        //     $other += $row_data->price;
        //   }
        //   $total_money += $row_data->price;
        // }
        $par_real = round(($real_money / $total_money) * 100, 2);
        $par_point = 100 - $par_real;
        ?>

         <!-- 円グラフの出力 -->
        <div class="canvas-box">
          <canvas id="myChart"></canvas>
        </div>
        <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        ctx.canvas.height = 320;
        var myChart1 = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: ["貯金・現金・仮想通貨", "ポイント"],
            datasets: [{
              backgroundColor: [
                "#3C00FF",
                "#BB5179",
                //"#FAFF67"
              ],
              data: [<?php echo $real_money ?>, <?php echo $point ?>]
            }]
          },
          options: {
            title: {
              display: true,
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
      </section>

      <section class="accounts">
        <h6>手元の現金を登録・管理</h6>
        <div class="regist-action">
        <div class="action-buttom right">
          <a href="../accounts/new-wallet">財布を作成</a>
        </div>
        </div>
        <?php
        $type_list = $wpdb->get_col( $wpdb->prepare(
          "SELECT DISTINCT type FROM $wpdb->wallet",
        ) );
        ?>
        <!-- 財布データを展開する -->
        <?php foreach ($type_list as $type) {
        	$row_datas = $wpdb->get_results( $wpdb->prepare(
        		"SELECT * FROM $wpdb->wallet Where type = %s",
            $type
        	) );
          echo "<h4 class='heading-normal'>" .$type. "</h4>";
        	foreach($row_datas as $row_data){
            echo "<div class='account-box'>";
            if($row_data->type == "財布") {
        		  echo "<a href='accounts/show-manual?show-manual-act=show&id=". $row_data->id ."'>". $row_data->possession ."</a><br>";
            } else if($row_data->type == "ポイント") {
              echo "<a href='accounts/show-manual?show-manual-act=detail&id=". $row_data->id ."'>". $row_data->name ."</a><br>";
            } else {
              echo $row_data->possession ."<br>";
            }
            echo "<strong>". number_format($row_data->price). "円</strong>";
            echo "<div class='pull-right'>";
            echo "<a href='edit-manual?edit-manual-act=change&id=". $row_data->id ."'>編集</a>";
            echo "</div>";
        		echo "<br>";
            echo "</div>";
        	}
        } ?>
      </section>

      <section class="accounts">
        <p>登録金融機関</p>
        <div class="regist-action">
          <!-- <div class="sub-action action-buttom">
            <a href="#">一括更新</a>
          </div> -->
          <div class="sub-action action-buttom">
            <a class="fa fa-check-square-o" href="../accounts/service-list">新規登録</a>
          </div>
        </div>
        <div class="pull-right">
          <a href="accounts" class="arrow right1">金融機関の管理へ</a>
        </div>
        <!-- <h4 class="heading-normal">銀行</h4> -->
        <?php
      	// $row_datas = $wpdb->get_results( $wpdb->prepare(
      	// 	"SELECT * FROM $wpdb->wallet Where type = '銀行'"
      	// ) );
        //
      	// foreach($row_datas as $row_data){
        //   echo "<div class='account-box'>";
        //   echo "取得日時（". substr($row_data->created, 0, 16). "）<br>";
      	// 	echo $row_data->name. "<br>";
        //   echo "<strong>". number_format($row_data->price). "円</strong><br>";
        //   echo "ステータス：正常";
        //   echo "<div class='pull-right'>";
        //   echo "<a href='#'>編集 </a>";
        //   echo "<a href='#'>更新</a>";
        //   echo "</div>";
      	// 	echo "<br>";
        //   echo "</div>";
      	// }
      	?>
         <!-- <h4 class="heading-normal">仮想通貨・FX・貴金属</h4> -->
      </section>

    </div>

    <div class="mypage-column center-column">
      <section class="easy-input">
        <h4 class="heading-normal">カンタン入力</h4>
        <div class="updated">
      		<p><strong><?php echo $message; ?></strong></p>
      	</div>
        <div class="wrap">
          <script type = "text/javascript">
          //main_categoryからsub_categoryを作成
          function functionName()
              {
                var select1 = document.forms.categorieform.category_main;
                var select2 = document.forms.categorieform.category_sub;
        				var count = 0;
                // 選択肢の数がそれぞれに異なる場合
                select2.options.length = 0;
                // var selecter = select1.options[select1.selectedIndex].value
                // console.log( $("#category_main").val());
                $.ajax({
                  type: "POST",
                  url: ajaxurl, // admin-ajax.php のURLが格納された変数
                  data: {
                    'action': 'ajax_get_option',
                    //selectedだとerrになる
                    'selecter' : $("#category_main").val()
                  },
                  dataType:'json'
                  //通信に成功したら
                  }).done(function(sub_items){
                    /* 通信成功時 */
                    // console.log(sub_items);
                    sub_items.forEach(function(value) {
                      // select2.options[count] = new Option(value, value);
                      $('#category_sub').append($('<option>').html(value).val(value));
                    });
                    //初期呼び出し時は最終要素を選択
                    document.getElementById("category_sub").options[sub_items.length - 1].selected = true;
                    //入力エラー時は前回の入力を選択
                    <?php if((!empty($category_sub)) && ($category_sub != "未分類")) { ?>
                    //メインカテゴリーを変えると存在しないoption valueだから空白になる。
                      $("#category_sub option[value='<?php echo $category_sub ?>']").prop('selected', true);
                    <?php } ?>
                    //通信に失敗したら
                  }).fail(function(jqXHR, textStatus, errorThrown){
                    console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
                    console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
                    console.log("errorThrown    : " + errorThrown.message); // 例外情報
                    console.log("URL            : " + url);
                  });
              }
          </script>
          <form name="categorieform" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="post_method" value="Y">
          	<div class="categories_list">
          		<body bgcolor onLoad="functionName()">
          			<div class="category-menue">
            			<p><!--選択肢その1-->
            				<select id="category_main" class="category-menue category-buttom" style="width:100%;" name="category_main" onChange="functionName()">
                    <script>
                      var items = [];
                      <?php
                      $option_datas = $wpdb->get_results( $wpdb->prepare(
                        "SELECT distinct category_main
                         FROM $wpdb->purchases_category"
                      ) );
                      foreach($option_datas as $row_data) { ?>
                        items.push("<?php echo $row_data->category_main ?>");
                      <?php } ?>
                      getOption("category_main", items);
                      //初期設定の選択肢
                      <?php if(empty($category_main)) { ?>
                        $("#category_main option[value='未分類']").prop('selected', true);
                      <?php } else { ?>
                        $("#category_main option[value='<?php echo $category_main ?>']").prop('selected', true);
                      <?php } ?>
                    </script>
            				</select>
            			</p>
          			</div>

          			<div class="category-menue">
            			<p><!--選択肢その2（選択肢その1の項目によって変化）-->
            				<select id="category_sub" class="category-menue category-buttom" style="width:100%;" name="category_sub">
            				</select>
            			</p>
          			</div>
                <div class="calendar category-menue">
                  <p class="nowrap" style="text-align:right;">日付
                  <span class="short-text">
                    <?php
                    //日本時間に設定
                    date_default_timezone_set('Asia/Tokyo');
                     if(isset($created)) {
                      $day_value = "$created";
                    } else {
                      $day_value = date('Y/m/d');
                    } ?>
                    <input style="width: 80%;"type="text" name="created" id="datepicker" value="<?php echo $day_value ?>" readonly>
                    <script>
                    $('#datepicker').datepicker({
                      changeMonth: true,
                      changeYear: true,
                      dateFormat: 'yy/mm/dd',
                      duration: 300,
                      showAnim: 'slideDown'
                      // ボタン設定
                      // buttonImage: "images/calendar.gif"
                      // buttonImageOnly: true,
                      // buttonText: "Select date"
                    });
                    </script>
                  </span>
                  </p>
                </div>
          		</body>
          	</div>
            <div class="category-menue" style="width:60%">
            	<input style="width:90%" type="number" name="price" value="<?php echo $price ?>" data-type="number" placeholder="金額を入力してください">
              <span>円</span>
            </div>

            <div class="category-menue" style="width:35%">
              <p>支出元
              <span class="short-text">
              	<select id="wallet_source" class="category-buttom" name="wallet_source">
              		<?php
                  $row_datas = $wpdb->get_col( $wpdb->prepare(
                    "SELECT name FROM $wpdb->wallet WHERE type IN ('財布', 'ポイント')
                    ORDER BY name DESC, id ASC"
                  ) );

                  $wallet_items = [];
                  foreach($row_datas as $row_data) {
                    array_push($wallet_items, $row_data);
                  }
                  array_push($wallet_items, "なし");
              		$wallet_select = "";
              		$wallet_select_flag = true;
              		foreach($wallet_items as $wallet_item_key => $wallet_item_val){
              			$wallet_select = "";
              			if($source == $wallet_item_val )
              			{
              				$wallet_select = "selected";
              				$wallet_select_flag = false;
              			}
              			else if($wallet_item_val == "財布" && $wallet_select_flag)
              			 {
              				$wallet_select = "selected";
              			}
              				$wallet_items .= "<option value='". $wallet_item_val;
              				$wallet_items .= "'". $wallet_select ;
              				$wallet_items .= ">". $wallet_item_val. "</option>";
              		}
              		echo $wallet_items;
              		?>
              	</select>
              </span></p>
            </div>
            <p class="err-message"><strong><?php echo $price_message; ?></strong></p>
            <input type="text" name="text" value="<?php echo $content_name ?>" placeholder="内容を入力してください（任意）">
            <p class="err-message"><strong><?php echo $content_message; ?></strong></p>
            <p class="submit">
            <input type="submit" name="Submit" value="保存する" onClick="" />
            </p>
          </form>
        </div>
        <div class="pull-right">
          <a href="household?asset-act=new" class="arrow right1">収入・振替を入力する</a>
        </div>
      </section>

      <section class="last-money-list">
        <div>
          <h4 class="heading-normal">最新の入出金
          <span class="balloonoya">
            <img style="margin: 5px 0;" src="http://moneyform.verse.jp/wp-content/uploads/manual/question.jpeg" alt="？">
            <span class="balloon">所得日時順に、手入力や金融機関から取得した入出金データを取得します</span>
          </span>
          </h4>
        </div>
        <?php
        //収入と購入の値を取得・結合して日付順に並び変える
        $row_datas = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM
        (SELECT * FROM $wpdb->purchases
        UNION ALL
        SELECT * FROM $wpdb->incomes) AS result
        order by created desc, id desc limit 5"
        ) );
        //収入と購入の値を取得・結合して日付順に並び変える
      	// $purchases_datas = $wpdb->get_results( $wpdb->prepare(
      	// 	"SELECT * FROM $wpdb->purchases order by created desc, id desc limit 5"
      	// ) );
        // $incomes_datas = $wpdb->get_results( $wpdb->prepare(
        //   "SELECT * FROM $wpdb->incomes order by created desc, id desc limit 5"
        // ) );
        // $row_datas = array_merge($purchases_datas, $incomes_datas);
        // foreach($row_datas as $key)
        // {
        //     $sort_createds[] = $key->created;
        //     $sort_ids[] = $key->id;
        // }
        // //日付順（降順）→id順（降順）で並び替え
        // array_multisort($sort_createds, SORT_DESC, $sort_ids,  SORT_DESC, $row_datas);
        ?>
        <!-- 最新5件出力 -->
        	<?php  foreach($row_datas as $row_data){ ?>
            <div class="cut-container">
              <div class="cut-container-80">
              <?php
              echo $row_data->created. "　"; //日付を出力
              echo $row_data->category_main. ">"; //分類を出力
              echo $row_data->category_sub. "<br>"; //分類を出力
          		echo $row_data->name. "<br>"; //名前を出力
              ?>
              </div>
              <div class="cut-container-20">
              <?php
              if($row_data->type == "支出") {
                echo "-". number_format($row_data->price). "円<br>";
                } else {
          		  echo number_format($row_data->price). "円<br>";
              }
              ?>
              </div>
            </div>
            <?php
            // $output_count++;
            // if ($output_count == 5) {
            //   break;
            // }
          } ?>
          <div class="pull-right">
            <a href="household" class="arrow right1">履歴の詳細を見る</a>
          </div>
      </section>

      <section class="monthly_total">
        <?php
        date_default_timezone_set('Asia/Tokyo');
        $today = new DateTime();
        ?>
        <h4 class="heading-normal">
          <?php echo $today->format('n月'). "の収支 " ?>
          <span style="font-size: 12px; font-weight: normal;">
            <?php echo "（" .$today->format('Y-m-01'). " - " .$today->format('Y-m-t'). "）" ?>
          </span>
        </h4>
        <?php
        $month_text = ["当月収入", "当月支出", "当月収支"];
        $month_money = get_monthly_total();
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
        <div class="pull-right">
          <a href="household" class="arrow right1">詳細（家計簿）を見る</a>
        </div>

      </section>

      <section class="week-money">
        <?php
        //今週の日数を取得
        date_default_timezone_set('Asia/Tokyo');
        $day1 = new DateTime();
        $day2 = new DateTime('2020-01-05');
        $interval = $day1->diff($day2);
        $this_week_day = ($interval->format('%a')) % 7;

        //１カ月の収入取得
        $date = 0;
        $incomes_datas = $wpdb->get_results( $wpdb->prepare(
          "SELECT * FROM $wpdb->incomes
           WHERE target = 1"
        ) );
        $month_incomes = [];
        for($i=0; $i<31; $i++) {
          $oneday_total_data = 0;
          $today_result = date('Y-m-d', strtotime("-" .$i ." day"));
          foreach($incomes_datas as $incomes_data){
            $income_day = substr($incomes_data->created, 0, 10);
            if ($today_result == $income_day) {
              $oneday_total_data += $incomes_data->price;
            }
          }
          $month_incomes[$i] = $oneday_total_data;
          // 総資産（前日比）用収入
          if(!(isset($oneday_incomes))) {
            $oneday_incomes = $oneday_total_data;
          }
          // 総資産用の今週の収入
          if($i < $this_week_day) {
            $week_i_money += $oneday_total_data;
          }
        }
        ?>
        <!-- １カ月の支出取得 -->
        <?php
        $date = 0;
        $purchases_datas = $wpdb->get_results( $wpdb->prepare(
          "SELECT * FROM $wpdb->purchases
           WHERE target = 1"
        ) );
        $month_purchases = [];
        for($i=0; $i<31; $i++) {
          $oneday_total_data = 0;
          $today_result = date('Y-m-d', strtotime("-" .$i ." day"));
          foreach($purchases_datas as $purchases_data){
            $purchase_day = substr($purchases_data->created, 0, 10);
            if ($today_result == $purchase_day) {
              $oneday_total_data += $purchases_data->price;
            }
          }
          $month_purchases[$i] = $oneday_total_data;
          // 総資産（前日比）用支出
          if(!(isset($oneday_purchases))) {
            $oneday_purchases = $oneday_total_data;
          }
          // 総資産用の今週の支出
          if($i < $this_week_day) {
            $week_p_money += $oneday_total_data;
          }
        }
        ?>

        <h4 class="heading-normal">入出金の時系列推移 <span style="font-size: 12px; font-weight: normal;">(直近一カ月)</span></h4>
        <!-- 棒グラフの出力 -->
        <div class="canvas-box">
          <canvas id="barChart"></canvas>
        </div>
        <script>
        //１カ月の日付取得
        var month_list = getMonthList();
          var ctx = document.getElementById('barChart').getContext('2d');
          ctx.canvas.height = 200;
          var myChart2 = new Chart(ctx, {
          type: 'bar',
          data: {
           labels: month_list.reverse(),
           datasets: [
               {
               label: "収入",
               data: (JSON.parse("<?php echo json_encode($month_incomes); ?>")).reverse(),
               backgroundColor:"#3C00FF"
             },{
               label: "支出",
               data: (JSON.parse("<?php echo json_encode($month_purchases); ?>")).reverse(),
               backgroundColor:"#BB5179"
              }
            ]
           },
           options: {
             tooltips: {
               callbacks: {
                 label: function(tooltipItem, data){
                   return [data.datasets[tooltipItem.datasetIndex].label + "：" + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + "円"]
                 }
               }
             },
           },
           });
         </script>

      </section>

      <?php
        //一年間の収支を取得
        $start_time = $today->format('y-01-01'); // .' 00:00:00';
        $end_time = $today->format('y-12-31'); // .' 23:59:59';
        //年間収入の取得
        $year_i_money = $wpdb->get_var( $wpdb->prepare(
          "SELECT sum(price) FROM $wpdb->incomes
          WHERE target = 1 AND created BETWEEN %s AND %s",
          [$start_time, $end_time]
        ) );
        //年間購入の取得
        $year_p_money = $wpdb->get_var( $wpdb->prepare(
          "SELECT sum(price) FROM $wpdb->purchases
          WHERE  target = 1 AND created BETWEEN %s AND %s",
          [$start_time, $end_time]
        ) );

        $oneday = $oneday_incomes - $oneday_purchases;
        $oneday_par = ($oneday / ($total_money - $oneday)) * 100;

        $title_text = ["今週", "今月", "今年"];
        //増減円
        $week_result = $week_i_money - $week_p_money;
        $month_result = $month_money[2];
        $year_result = $year_i_money - $year_p_money;
        $moneys_list = [$week_result, $month_result, $year_result];
        //増減％
        $week_par = ($week_result / ($total_money - $week_result)) * 100;
        $month_par = ($month_result / ($total_money - $month_result)) * 100;
        $year_par = ($year_result / ($total_money - $year_result)) * 100;
        $pars_list = [$week_par, $month_par, $year_par];

      ?>

      <section class="total-cell-list">
        <h4 class="heading-normal">総資産</h4>
        <h6>
          <strong>
            <?php echo number_format($total_money)
          . "円（前日比）" .number_format($oneday)
          . "円（" . number_format($oneday_par, 1)
          . "％）" ?>
          </strong>
        </h6>
        <div id="small-font" class="sub-box">
          <div style="width:250px">
            <h4 class="heading-small">増減</h4>
            <table>
              <tbody>
                <?php for($i = 0; $i <= 2; $i++) { ?>
                <tr class="cut-container">
                  <th class="cut-container-30 nowrap">
                    <?php echo $title_text[$i] ?>
                  </th>
                  <td class="cut-container-30 text-right">
                    <?php
                    //数値によって文字色を変更する
                      $font_color = 'black';
                      if ($pars_list[$i] < 0) {
                          $font_color = 'red';
                      } else if ($pars_list[$i] > 0){
                          $font_color = 'blue';
                      }
                    ?>
                    <?php if ($pars_list[$i] > 0) { ?>
                    <p style="color:<?php echo $font_color ?>"><?php echo "+". number_format($pars_list[$i], 1). "％" ?>
                    <?php } else { ?>
                    <p style="color:<?php echo $font_color ?>"><?php echo number_format($pars_list[$i], 1). "％" ?>
                    <?php } ?>
                  </td>
                  <td class="cut-container-40 text-right">
                    <?php
                    //数値によって文字色を変更する
                      $font_color = 'black';
                      if ($moneys_list[$i] < 0) {
                          $font_color = 'red';
                      } else if ($moneys_list[$i] > 0){
                          $font_color = 'blue';
                      }
                    ?>
                    <?php if ($moneys_list[$i] > 0) { ?>
                    <p style="color:<?php echo $font_color ?>"><?php echo "+" .number_format($moneys_list[$i]). "円" ?>
                    <?php } else { ?>
                    <p style="color:<?php echo $font_color ?>"><?php echo number_format($moneys_list[$i]). "円" ?>
                    <?php } ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <?php
          $title_text = ["貯金・現金・仮想通貨", "ポイント"];
          $moneys_list = [$real_money, $point];
          $pars_list = [$par_real, $par_point];
           ?>
          <div style="width:350px">
            <h4 class="heading-small">内訳</h4>
            <table>
              <tbody>
                <?php for($i = 0; $i <= 1; $i++) { ?>
                <tr class="cut-container">
                  <th class="cut-container-50 nowrap">
                    <?php echo $title_text[$i] ?>
                  </th>
                  <td class="cut-container-30 text-right">
                    <p><?php echo number_format($moneys_list[$i]). "円" ?>
                  </td>
                  <td class="cut-container-20 text-right">
                    <p><?php echo number_format($pars_list[$i], 2). "％" ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="pull-right">
          <a href="portfolio" class="arrow right1">詳細（資産内訳）を見る</a>
        </div>
      </section>

      <section class="total-cell-list">
        <!-- １カ月の現金推移の取得 未完成バグあり-->
        <?php
        $asset_datas = $wpdb->get_results( $wpdb->prepare(
          "SELECT * FROM $wpdb->asset WHERE wallet_name = '財布'"
        ) );
        //初期値を数値で代入
        $befor_price = intval($asset_datas[count($asset_datas) - 1]->price);
        $month_c_asset = [];
        for($i=0; $i<31; $i++) {
          $today_result = date('Y-m-d', strtotime("-" .$i ." day"));
          foreach($asset_datas as $asset_data){
            $asset_day = $asset_data->created;
            if ($today_result == $asset_day) {
              $befor_price = intval($asset_data->price);
              $month_c_asset[$i] = $befor_price;
              continue;
            }
          }
          //値が無い日付は前日のデータを使用する
          $month_c_asset[$i] = $befor_price;
        }
        ?>
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
            // console.log(asset_list);
            if(name = "month") {
              var borderWidth = 2;
              var pointHitRadius = 6;
            } else if(name = "year") {
              var borderWidth = 0;
              var pointHitRadius = 1;
            }
            real_asset = asset_list[0];
            point_asset = asset_list[1];
            var month_list = getMonthList(days);
            var ctx = document.getElementById('lineChart').getContext('2d');
            ctx.canvas.height = 180;
            //すでにグラフがあるときは破棄する
            if(typeof myChart3 !== 'undefined' && myChart3) {
              myChart3.destroy();
            }
            window.myChart3 = new Chart(ctx, {
            type: 'line',
            data: {
             labels: month_list.reverse(),
             datasets: [
                 {
                   label: "預金・現金・仮想通貨",
                   data: real_asset.reverse(),
                   backgroundColor: "rgba(60,0,255,1.0)",
                   pointStyle: "rectRounded",
                   borderWidth: borderWidth,
                   borderColor: 'rgba(0,0,0,1.0)',
                   pointHitRadius: pointHitRadius,
                   hoverRadius: 10,
                   hoverBorderWidth: 3
               },{
                 label: "ポイント",
                 data: [0],
                 // data: (JSON.parse("<?php echo json_encode($month_p_asset); ?>")).reverse(),
                 backgroundColor: "#BB5179",
                 pointStyle: "line"
                }
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
        <h4 class="heading-normal">資産の時系列推移</h4>
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
          <canvas id="lineChart"></canvas>
        </div>
      </section>
    </div>
    <script>
    //初回呼び出し用
      period_data_change();
    </script>

    <div class="mypage-column right-column">
      <section class="side-column">
        <h4 class="heading-normal">もっとお得なお金のコラム</h4>
        <!-- 一覧表示 -->
        <?php
          $arg = array(
                     'posts_per_page' => -1, // 表示する件数
                     //'orderby' => 'date', // 日付でソート
                     'order' => 'ASC', // DESCで最新から表示、ASCで最古から表示
                     'category_name' => "未分類" // 表示したいカテゴリーのスラッグを指定
                 );
          $posts = get_posts( $arg );
              if( $posts ): ?>
                <h4 class="heading-small"><?php echo $category->name ?></h4>
                <ul>
              <?php
                  foreach ( $posts as $post ) :
                    setup_postdata( $post ); ?>
                  <li class="faq-li"><a href="<?php the_permalink(); ?>">・<?php the_title(); ?></a></li>
            <?php endforeach; ?>
                </ul>
            <?php
            endif;
          //入れるとfooterが消える
          //wp1_reset_postdata();
        ?>
      </section>
    </div>
  </div>

<?php get_footer(); ?>
