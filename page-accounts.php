<?php  get_header();?>
<script>
//編集ボタンからの転送
function dbChange(id) {
  window.location.href = "edit-manual/?edit-manual-act=edit&id=" + id
}
</script>
<div class="accounts">
  <section id="accounts">
    <h4 class="heading-normal">手元の現金・資産</h4>
    <?php
    $row_datas = $wpdb->get_results( $wpdb->prepare(
      "SELECT * FROM $wpdb->wallet WHERE type IN ('財布', 'ポイント')
      ORDER BY type_sub DESC"
    ));
    $title_text = ["登録名", "資産", "登録日", "編集", "削除"];
    $data_count = count($row_datas);
    $text_count = count($title_text);
    ?>
    <table id="real_money_table" class="tablesorter">
      <thead>
        <tr class="cut-container">
          <!-- スマホ表示では資産と登録日を表示しない -->
          <?php for($i = 0; $i < $text_count; $i++) {
            if($title_text[$i] == "資産" || $title_text[$i] == "登録日") {?>
            <th class="smart-hidden">
              <?php echo $title_text[$i] ?>
            </th>
          <?php } else {?>
            <th>
              <?php echo $title_text[$i] ?>
            </th>
            <?php } ?>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <?php
        for($i = 0; $i < $data_count; $i++) {
          $wallet_id = $row_datas[$i]->id;
          $data_list = [
            number_format($row_datas[$i]->price)."円",
            substr($row_datas[$i]->created,0 , 10)
          ]
          ?>
            <tr class="cut-container" name="<?php echo '財布' .$wallet_id ?>">
              <td>
                <!-- 財布情報へのリンク -->
                <?php if($row_datas[$i]->type == "財布") { ?>
                  <a href='show-manual?show-manual-act=show&id=<?php echo $wallet_id ?>'><?php echo $row_datas[$i]->name ?></a>
                <?php } else if($row_datas[$i]->type == "ポイント") { ?>
                  <a href='show-manual?show-manual-act=detail&id=<?php echo $wallet_id ?>'><?php echo $row_datas[$i]->name ?></a>
                <?php } ?>
              </td>
              <?php for($j = 0; $j < count($data_list); $j++) { ?>
                <td class="smart-hidden">
                  <?php echo $data_list[$j] ?>
                </td>
              <?php } ?>
              <td>
                <input type="submit" name="Change" value="編集" onclick="dbChange('<?php echo $wallet_id ?>')">
              </td>
              <td>
                <input type="submit" name="Delete" value="削除" onclick="dbDelete('財布', '<?php echo $wallet_id ?>')">
              </td>
            </tr>
        <?php } ?>
      </tbody>
    </table>
  </section>
</div>

<?php get_footer(); ?>
