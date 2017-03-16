<?php
function query_result_from_elastic($url, $queryjson) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    curl_close($ch);
    //$arr = json_decode($response, true);
    return json_decode($response, true);
}
$keyword = $event->message->text;
if ($event->message->type == "text") {
    //検索キーワードと同時検索させる広告の品を取得　-> $arrにjsonを返す
    $url = 'localhost:9200/item/sale/_search';
    $arr = query_result_from_elastic($url,null);

    //品名そのままだと使いづらいので正規化 Ex.ブロッコリースプラウト１袋->ブロッコリースプラウト
    $ptn = array("/袋/","/パック/","/[0-9]|[a-z]|\(|\)/");
    $rep = array(" "," "," ");

    //広告の品を5品に限定(多すぎると検索結果に対してどのアイテムが結果に影響しているのか判りづらいので5品に限定。状況により増やしても可
    for ($i=0; $i<min(count($arr['hits']['hits']), 5); $i++) {	//ここで5品に限定している

      //検索キーワードと一緒に検索する広告の品を正規表現にて検索食品名に変換
      $repstr =preg_replace($ptn,$rep,mb_convert_kana($arr['hits']['hits'][$i]['_source']['item_name'],"as"));
      $saleitem[] = explode(" ",$repstr)[0];
    }

    $url = 'localhost:9200/recipe/categoryranking/_search';
    //$saleitem = array("新じゃが芋（２Ｌサイズ）１玉","不ぞろい新じゃが芋　大袋８００ｇ１袋","不ぞろい人参　大袋６００ｇ１袋","大根１本","不ぞろい玉ねぎ　大袋１．２ｋｇ１袋");
    //$saleitem = array("新じゃが芋","不ぞろい新じゃが","不ぞろい人参","大根１本","不ぞろい玉ねぎ");
    //表示順(order by _score desc,rank asc)
    $larr["sort"] = ["_score","rank"];
    for($i=0;$i<min(count($saleitem), 5);$i++) {
      $larr["query"]["bool"]["should"][] = array("multi_match"=>array("query"=>$keyword." ".$saleitem[$i],"type"=>"cross_fields","fields"=>array("recipeTitle","recipeDescription","recipeMaterial")));
    }
    $jsonString = json_encode($larr);
    $arr = query_result_from_elastic($url,$jsonString);
    ..
}
?>
