# query_elastic
query.phpを実行すると広告の品5つと検索ワードをもってelasticsearchを検索する
Ex.keyword=豚肉,広告の品=Array ( [0] => エリンギ大 [1] => ブロッコリースプラウト [2] => トマト [3] => にんにくカケラ [4] => 大葉大 )

curl -XGET "localhost:9200/recipe/categoryranking/_search?pretty" -d'
{
  "sort" : ["_score","rank"],
  "query": {
    "bool": {
      "should": [
        {
          "multi_match" : {
            "query":      "豚肉 エリンギ大",
            "type":       "cross_fields",
            "fields": ["recipeTitle","recipeDescription","recipeMaterial"]
           }
        },
        {
          "multi_match" : {
            "query":      "豚肉 ブロッコリースプラウト",
            "type":       "cross_fields",
            "fields": ["recipeTitle","recipeDescription","recipeMaterial"]
          }
        },
        {
          "multi_match" : {
            "query":      "豚肉 トマト",
            "type":       "cross_fields",
            "fields": ["recipeTitle","recipeDescription","recipeMaterial"]
          }
        },
        {
          "multi_match" : {
            "query":      "豚肉 にんにくカケラ",
            "type":       "cross_fields",
            "fields": ["recipeTitle","recipeDescription","recipeMaterial"]
          }
        },
        {
          "multi_match" : {
            "query":      "豚肉 大葉大",
            "type":       "cross_fields",
            "fields": ["recipeTitle","recipeDescription","recipeMaterial"]
          }
        }
      ]
    }
  }
}'
