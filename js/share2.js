//カテゴリーカラーの取得
function getCategoryColor(list) {
  var color = "";
  var colorList = [];
  for(var date of list) {
    console.log("date" + date);
    switch (date) {
      case "食費":
        color = "#f2595f";
        break;
      case "日用品":
        color = "#64e792";
        break;
      case "趣味・娯楽":
        color = "#ff4cff";
        break;
      case "交際費":
        color = "#235bc8";
        break;
      case "交通費":
        color = "#6d40ff";
        break;
      case "衣服・美容":
        color = "#ffe99f";
        break;
      case "健康・医療":
        color = "#e19661";
        break;
      case "自動車":
        color = "#180c2a";
        break;
      case "教養・教育":
        color = "#7bb984";
        break;
      case "特別な支出":
        color = "#f2595f";
        break;
      case "現金・カード":
        color = "#b37d40";
        break;
      case "水道・光熱費":
        color = "#52d9e2";
        break;
      case "通信費":
        color = "#c173c1";
        break;
      case "住宅":
        color = "#55a160";
        break;
      case "税・社会保障":
        color = "#d9bfa0";
        break;
      case "保険":
        color = "#da70d6";
        break;
      case "その他":
        color = "#d1d0d3";
        break;
      case "未分類":
        color = "#ed6d35";
        break;
      default:color = "#000000";
    }
    colorList.push(color);
  }
  return colorList;
}
//accounts, household
//削除後の残高、資産変更処理が必要
function dbDelete(type, ajaxId) {
  // JS関数内でPHPを呼び出す
  if (deleteChk()) {
    // 削除した行を非表示にする
    $("[name=" + type + ajaxId + "]").hide();
    $.ajax({
      type: 'POST',
      url: "https://verse-moneyform.ssl-lolipop.jp/db-submit",
      dataType: 'json',
      data: {
        type: '削除',
        ajaxid: ajaxId,
        ajaxtype: type
      }
    });
  } else {
    console.log("削除No");
  }
}

//振替後の残高、資産変更処理が必要
function dbTransfer(type, ajaxId, price, created) {
  // JS関数内でPHPを呼び出す
  if (transferChk()) {
    // 削除した行を非表示にする
    $("[name=" + type + ajaxId + "]").hide();
    var sourceFrom = $("[name=source_from" + ajaxId + "]").val();
    var sourceTo = $("[name=source_to" + ajaxId + "]").val();
    console.log(price);
    console.log(sourceFrom);
    console.log(sourceTo);
    console.log(created);
    $.ajax({
      type: 'POST',
      url: "../db-submit",
      dataType: 'json',
      data: {
        type: '振替',
        ajaxid: ajaxId,
        ajaxtype: type,
        price: price,
        source_from: sourceFrom,
        source_to: sourceTo,
        created: created
      }
    });
  } else {
    console.log("振替No");
  }
}

//household 月切り替え
function change_month(name) {
  var changed = $("#changeCount").val();
  if(name == "down") {
    changed --;
  } else if(name == "up") {
    changed ++;
  }
  $("#changeCount").val(changed);
  location.href='household/?month=' + changed + '#period_data_change';
}

//出来なくなる
function targetChangeBtn(name, value) {
  //チェック状況の取得
  var boolean = $("[name=" + name + "]:checked").prop("checked");
  if(boolean) {
    boolean = 1;
  } else {
    boolean = 2;
  }
  //数値のみ抜き出す
  id = name.replace(/[^0-9]/g, '');
  // console.log(boolean);
  // console.log(Number(id));
  // console.log(value);
  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: {
      'action': 'ajax_db',
      'nowTarget': boolean,
      'id': id,
      'source': value
    },
    dataType:'json'
  }).done(function(text){
      console.log(text);
    })
}

function targetChangeIpt(data, value) {
  //data を array[id, source, changeDataType] に変換
  //changeDataTypeはdbの変更要素名
  var result = data.split('-');
  // console.log(data);
  // console.log(result);
  // console.log(value);
  var id = result[0];
  var source = result[1];
  var type = result[2];
  //選択状態の解除
  $("[name=" + data + "]").attr('disabled','disabled');
  $("[name=" + data + "]").removeAttr('disabled');

  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: {
      'action': 'ajax_db',
      'id': id,
      'source': source,
      'changeDataType': type,
      'value': value
    },
    dataType:'json'
  }).done(function(){
    value = separate(value);
    if(type == "price") {
      if(source == "支出") {
        value =  "-" + value;
      } else if (source == "収入") {
        value =  "+" + value;
      }
      $("[name=" + data + "]").get(0).type = 'text';
      $("[name=" + data + "]").val(value);
      // //財布残高と資産推移の更新(PHP)
      // var walletSource = $("[name=" + id + "-" + source + "-" + "source]").val();
      // var created = $("[name=" + id + "-" + source + "-" + "created]").val();
      // update_wallet_asset(source, walletSource, value, created);
    }
  })
  //category_mainの時はcategory_subも変更して保存
   if(type == "category_main") {
     data2 = id + "-" + source + "-category_sub";
     // console.log($('[name=' + data2 + ']').val());
     //dbのcategory_subの最終要素を指定する
     // value = $('[name=' + data2 + ']').val();
     if(value == "その他") {
       value = "その他";
     } else if (value != "未分類") {
       value = "その他" + value;
     }
     targetChangeIpt(data2, value);
   }
  //リダイレクトによる更新（アドレス変更要）
  // if(["created"].includes(result[2])) {
  //   document.location.href = location.href;
  // }
}

function getNumberOnly(id, value) {
  //数値のみ抜き出して戻す type=numberに変換
  priceNumber = value.replace(/[^0-9]/g, '');
  $('#' + id).val(priceNumber);
  $('#' + id).get(0).type = 'number';
}

//optionの作成
function getOption(name, list) {
  list.forEach(function(item) {
  $('#' + name).append('<option value=' + item + '>' + item + '</option>');
  })
}

//main_categoryからsub_categoryを作成（ホームで自己保持が出来ないため、使えない）
function functionName(form, main, sub, sub_name) {
  // var select1 = document.forms[form][main];
  // var select2 = document.forms[form][sub];
  // 初期設定：optionを空にする
  $('#' + sub).children().remove();
  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: {
      'action': 'ajax_get_option',
      'selecter' : $('#' + main).val()
    },
    dataType:'json'
    //通信に成功したら
    }).done(function(sub_items){
      sub_items.forEach(function(value) {
      // select2.options[count] = new Option(value, value);
      $('#' + sub).append($('<option>').html(value).val(value));
      });
      //初期呼び出し以外は最終要素を選択
      // console.log(sub_name);
      if(sub_name == undefined ) {
        $('#' + sub).prop("selectedIndex", $('#' + sub).children().length - 1);
        // document.getElementById(sub).options[sub_items.length - 1].selected = true;
      } else {
        $('#' + sub).val(sub_name);
      }
    });
}

//foam表示・非表示切り替え
function hiddenBtn(){
    $('#dialog-form').toggle();
    $('#modal-backdrop').toggle();
  // const dialogForm = document.getElementById("dialog-form");
  // const modalBackdrop = document.getElementById("modal-backdrop");
  //
  // if(dialogForm.style.display=="block"){
  //   // noneで非表示
  //   dialogForm.style.display ="none";
  //   modalBackdrop.style.display ="none";
  // }else{
  //   // blockで表示
  //   dialogForm.style.display ="block";
  //   modalBackdrop.style.display ="block";
  // }
}

//foam内での表示・非表示切り替え
//表示中のページのみ送信する
$(document).on('click', '.select_radio', function(){ //ラジオボタンをクリックした際
$('.span_class').hide(); //全てのselectメニューを非表示にする
$('.select_disabled').attr('disabled','disabled'); //全てのselectメニューをdisabledにする
$('.selection-li').removeClass('select_item');//ボタンの色を外す
$('#' + $(this).val() + '-li').addClass('select_item');//選択中のボタンに色をつける
$('.selection-name').removeClass('submit_name');//送信判別用クラス
$('#' + $(this).val() + '-name').addClass('submit_name');//送信判別用クラス
$('.selection-price').removeClass('submit_price');//送信判別用クラス
$('#' + $(this).val() + '-price').addClass('submit_price');//送信判別用クラス
$('#' + $(this).val()).show(); //選択したところのselectメニューを表示させる
$('#' + $(this).val()).find('.select_disabled').removeAttr('disabled'); //選択したところのselectメニューのdisabledを解除する
//$('#span_color' + $(this).val()).show().find('.select_disabled').removeAttr('disabled');みたいにまとめてもいけそう
});

// if(location.href())
$(function(){//ページ読み込み時
$('.span_class').hide(); //全てのselectメニューを非表示にする
$('.select_disabled').attr('disabled','disabled'); //全てのselectメニューをdisabledにする
// $('#purchases-type-li').addClass("select_item");//選択中のボタンに色をつける
// $('#purchases-type-price').addClass('submit_price');//送信判別用クラス
$('#purchases-type').show(); //選択したところのselectメニューを表示させる
$('#purchases-type').find('.select_disabled').removeAttr('disabled');
});

// 任意の文字列にformatする
function dateToStr24HPad0(date, format) {

  if (!format) {
    // デフォルト値
    format = 'YYYY/MM/DD hh:mm:ss'
  }

  // フォーマット文字列内のキーワードを日付に置換する
  format = format.replace(/YYYY/g, date.getFullYear());
  format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
  format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));
  format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));
  format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
  format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));

  return format;
}

//1カ月の日付リストを作成する
function getMonthList(num = 31) {
  var today = new Date();
  var result_list = [];
  for(var i=0; i<num; i++) {
      var todayFormat = dateToStr24HPad0(today, 'MM月DD日');
      today.setDate(today.getDate() - 1);
      result_list[i] = todayFormat;
  }
  return result_list;
}

// ページを開いたとき
window.onload = function () {
}

function formatNum($num){
  return sprintf("%+d",$num);
}

// 正規表現で3桁区切りでカンマ付与
function separate(num){
    return String(num).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
}

function deleteChk () {
  /* 確認ダイアログ表示 */
  var flag = confirm ("削除してもよろしいですか？（テスト中の為、非表示にするだけ）");
  /* send_flg が TRUEなら送信、FALSEなら送信しない */
  return flag;
}

function transferChk () {
  /* 確認ダイアログ表示 */
  var flag = confirm ("振替を解除してもよろしいですか？（テスト中）");
  /* send_flg が TRUEなら送信、FALSEなら送信しない */
  return flag;
}

//うまく反応しない household edit-manual show-manual
function checkSubmit() {
  var err = $('.err-message').text();
  var price = $('.submit_price').val();
  var walletName = $('.submit_w_name').val();
  var contentName = $('.submit_name').val();
  var info = $('.submit_info').val();
  // console.log(err);
  // console.log(price);
  // console.log(walletName);
  // console.log(contentName);
  // console.log(info);
  // return false;
  if(err.length > 0) {
    alert(err);
    return false;
  }else if(price == ""){
    alert("金額を入力して下さい");
    return false;
  }else if(walletName == ""){
    alert("名前を入力して下さい");
    return false;
  }else if(price.length > 12){
    alert("金額は12桁以内で入力してください");
    return false;
  }else if(walletName.length > 20){
    alert("名前は20文字以内で入力してください");
    return false;
  }else if(info.length > 20){
    alert("メモは20文字以内で入力してください");
    return false;
  }else if(contentName.length > 50){
    alert("内容は50文字以内で入力してください");
    return false;
  }else{
    return true;
  }
  return false;
}
