//ajaxの使い方
//main_categoryからsub_categoryを作成
<div class="result">ここにajaxの結果を書き換えます</div>
<input type="number" value="3" class="input_number" placeholder="数値を入力してください">
<input type="button" class="sample_btn" value="ajax通信で取得する">

<script>
$(function(){
  //.sampleをクリックしてajax通信を行う
$('.sample_btn').click(function(){
  $.ajax({
    type: "POST",
    url: ajaxurl, // admin-ajax.php のURLが格納された変数
    data: {
      'action': 'my_ajax_do',
      'no' : $('.input_number').val()
          },
    dataType:'json'
    }).done(function(data){
      /* 通信成功時 */
      $('.result').text(data); //取得したHTMLを.resultに反映

    }).fail(function(data){
      /* 通信失敗時 */
      alert('通信失敗！');

    });
  });
});

//main_categoryからsub_categoryを作成
function functionName()
    {
      var select1 = document.forms.categorieform.category_main;
      var select2 = document.forms.categorieform.category_sub;
      var count = 0;
      // 選択肢の数がそれぞれに異なる場合
      select2.options.length = 0;
      //後でdbにする
      switch(select1.options[select1.selectedIndex].value)
      {
      case "未分類":
        var items = ["未分類"];
        break;
      case "食費":
        var items = ["食費","食料品","朝ごはん","夜ご飯","カフェ","その他食費"];
        break;
      case "日用品":
        var items = ["日用品","子育て用品","ドラッグストア","おこづかい","ペット用品","タバコ","その他日用品"];
        break;
      case "趣味・娯楽":
        var items = ["アウトドア","ゴルフ","スポーツ","映画・音楽・ゲーム","本","旅行","秘密の趣味","その他の趣味・娯楽"];
        break;
      case "交際費":
        var items = ["交際費","飲み会","プレゼント代","冠婚葬祭","その他の交際費"];
        break;
      }
      items.forEach(function(value) {
        select2.options[count] = new Option(value, value);
        count++
      });
      //初期呼び出し時は最終要素を選択
      document.getElementById("category_sub").options[items.length - 1].selected = true;
      //入力エラー時は前回の入力を選択
      <?php if((!empty($category_sub)) && ($category_sub != "未分類")) { ?>
        //メインカテゴリーを変えると存在しないoption valueだから空白になる。
        // document.getElementById("category_sub").value = "<?php //echo $category_sub ?>";
        $("#category_sub option[value='<?php echo $category_sub ?>']").prop('selected', true);
      <?php } ?>
    }
</script>

<!-- 日付色々 -->
<?php
$today = new DateTime();
$today = $today->format('Y-m-t');
$day =  date('Y-m-d', strtotime("-31 day"));
?>

?>
<script>
console.log(<?php echo $ ?>)
</script>
<?php

?>
<!-- Chrome Loggerの使い方 -->
<?php include 'ChromePhp.php';?>
<?php ChromePhp::log('Hello console!日本語もOK'); ?>

<!-- PHPリダイレクト（headerがerrを出す） -->
<?php
  header('Location: http://localhost/wordpress/household/portfolio/');
  exit;
?>
<!-- 確認 -->

// <?php
// $wpdb->delete('wp_wallet',
// array(
//   'id' => $wallet_id
//   ),
// array(
//   '%s'
//   )
// );
// ?>

<script type="text/javascript">
if(deleteChk ()) {
  console.log("Yes");
} else {
  console.log("No");
}
</script>

<!-- JSリダイレクト -->
<script type="text/javascript">
  setTimeout("link()", 0);
  function link(){
  location.href='http://localhost/wordpress/accounts/edit-manual/';
  }
</script>

<!-- 要素を表示・非表示 -->
<div class="hidden_box">
    <label for="label1">クリックして表示</label>
    <input type="checkbox" id="label1"/>
    <div class="hidden_show">
      <!--非表示ここから-->
      <!--ここまで-->
    </div>
</div>

<?php
//データベースの削除
$wpdb->delete('wp_wallet',
array(
  'id' => $wallet_id
   ),
 array(
   '%s'
 )
);
 ?>
<?php
  //すべての財布名での最新の資産情報を取得
  $datas = $wpdb->get_results( $wpdb->prepare(
    "SELECT wp2_asset.wallet_name ,wp2_asset.created ,wp1_asset.price FROM
    (SELECT wallet_name, max(created) AS created
    FROM $wpdb->asset
    GROUP BY wallet_name) AS wp2_asset
    LEFT JOIN
    (SELECT * FROM $wpdb->asset) AS wp1_asset
    ON wp2_asset.wallet_name = wp1_asset.wallet_name AND wp2_asset.created = wp1_asset.created"
  ));

  // select
  // wp2_asset.wallet_name
  // ,wp2_asset.created
  // ,wp1_asset.price
  // from
  // (SELECT
  // wallet_name
  // ,max(created) as created
  // FROM wp1_asset
  // group by wallet_name) as wp2_asset
  // left join
  // wp1_asset
  // on wp2_asset.wallet_name = wp1_asset.wallet_name and wp2_asset.created = wp1_asset.created;
  //
  // SELECT distinct wallet_name, First_Value(price) over(partition by wallet_name order by created desc) as current_price FROM $wpdb->asset
  //

  //列で取得
  $category_main_all = $wpdb->get_col( $wpdb->prepare(
    "SELECT category_main
     FROM $wpdb->purchases_category"
  ) );

	//重複した値を除外して取得
	$row_datas = $wpdb->get_results( $wpdb->prepare(
		"SELECT distinct name FROM $wpdb->wallet"
	) );

  //複数の条件を指定
  $befor_datas = $wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM $wpdb->asset
    where created <= %s and wallet_name = %s order by created desc limit 1" ,
    [$end_time, $wallet_name]
  ));

  //合計値を取得
  $asset_sum = $wpdb->get_var( $wpdb->prepare(
    "SELECT sum(price) FROM $wpdb->asset WHERE wallet_name = '財布'"
  ));

?>

<div>
	<?php
	//dbから値を取得
	$ipt_id = 5;//ID

	$row_data = $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM $wpdb->purchases WHERE id = %s",
		$ipt_id
	) );

	echo $row_data->name. "<br>"; //名前を出力
	echo $row_data->price; //金額を出力
	 ?>

	<?php if(is_page( '資産' )):
	echo "カンタン入力"
	?>
	<?php else: ?>
	<?php endif; ?>
</div>

<!-- <script>
//3桁毎にカンマを入れる
/* テキストボックスを取得 */
var NBR = document.querySelectorAll( "[data-type='number']" );
/* イベント操作 */
for(var i=0;i<NBR.length;i++){ NBR[ i ].oninput = fmtInput }

/* 入力時に実行する処理 */
function fmtInput( evt ){
var target = evt.target;
var data = target.value[ target.value.length-1 ];
if( ! data.match( /[0-9]/ ) ){
target.value = target.value.slice( 0, target.value.length-1 );
}
target.value = target.value
.replace( /,/g, '' )
.replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,' );
}
</script> -->

<?php
function page_form_easy_input2($content) {

		// POSTリクエストの場合
		if( $_POST['post_method'] == 'Y' )
		{
			global $wpdb;
			$email = $_POST['email'];
			$status = $_POST['status'];

			// データベースに登録
			$wpdb->insert('wp_mailmagazine',
				array(
					'email' => $email,
			'status' => $status,
			'date' => current_time('mysql', 1)
		),
		array(
			'%s',
			'%d',
			'%s'
		)
			);

			$message = '登録処理が完了しました';
}
	else
	{
		return $content;
	}
}

?>
<div class="updated">
	<p><strong><?php echo $message; ?></strong></p>
</div>


<div class="wrap">
<h2>メールアドレスを入力してください</h2>

<h3>登録</h3>
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="post_method" value="Y">
	<input type="hidden" name="status" value="1">
	<input type="text" name="email" value="">
	<p class="submit">
	<input type="submit" name="Submit" value="登録する" />
	</p>
</form>
</div>

<?php
add_filter('the_content', 'page_form_easy_input2');
?>

<!-- フォームによる送信 -->
<!DOCTYPE html>
<html>
<head>
<body>
<h1>フォームデータの送信</h1>
<pre>
  <?php
  if(isset($_POST['comment'])) {
    var_dump($_POST);
  }
   ?>
</pre>

<form action = "<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method = "post">
  <input type = "text" name = "comment">
  <input type = "submit" value ="送信">
</form>
</body>
</head>
</html>

<?php
$date = 0;
$purchases_datas = $wpdb->get_results( $wpdb->prepare(
  "SELECT * FROM $wpdb->purchases"
) );
$week_purchases = [];
// $today = date('Y-m-d');
for($i=0; $i<7; $i++) {
  $total_data = 0;
  $today_result = date('Y-m-d', strtotime("-" .$i ." day"));
  foreach($purchases_datas as $purchases_data){
    $purchase_day = substr($purchases_data->created, 0, 10);
    if ($today_result == $purchase_day) {
      $total_data += $purchases_data->price;
    }
  }
  $week_purchases[$i] = $total_data;
}
?>
<?php for($i=0; $i<7; $i++) { ?>
  <p><?php echo  $week_purchases[$i] ?></p>
<?php } ?>
<p><?php echo "today: " .$today_result ?></p>
<p><?php echo "purchases: " .$purchase_day ?></p>
<p><?php echo $data; ?></p>

<?php
//使わない
$items = ["食費","日用品","趣味・娯楽","交際費","交通費","衣類・美容","健康・医療","教養・教育","特別な支出","現金・カード","水道・光熱費","通信費","住宅","税・社会保障","保険","その他","未分類"];
$select_flag = true;
foreach($items as $item_key => $item_val){
	$select = "";
	if($category_main == $item_val )
	{
		$select = "selected";
		$select_flag = false;
	}
	else if($item_val == "未分類" && $select_flag)
	 {
		$select = "selected";
	}
    $items .= "<option value='". $item_val;
		$items .= "'". $select ;
    $items .= ">". $item_val. "</option>";
}
echo $items;
?>
