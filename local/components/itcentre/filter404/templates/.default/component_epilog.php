<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$curPage = $APPLICATION->GetCurPage(false);

if(is_array($GLOBALS["MAX_SMART_FILTER"]) && array_key_first($GLOBALS["MAX_SMART_FILTER"]) !== '>=CATALOG_PRICE_7'){
	unset($GLOBALS["MAX_SMART_FILTER"]['FACET_OPTIONS']);
    $count_url_filters = 0;
    $url = explode('/', stristr(str_replace('/apply/', '', $curPage), '/filter/'));
    foreach ($url as $filter_param){
        if($filter_param && $filter_param != 'filter' && substr_count($filter_param, '-or-') > 0){
            $count_url_filters += substr_count($filter_param, '-or-') + 1;
        } elseif(substr_count($filter_param, '-is-') > 0) {
            $count_url_filters += 1;
        }
    }

    $count_filters = count($GLOBALS["MAX_SMART_FILTER"], COUNT_RECURSIVE) - count($GLOBALS["MAX_SMART_FILTER"]);

    if((strpos($curPage, "/filter/") !== false && empty($GLOBALS["MAX_SMART_FILTER"]) && strpos($curPage, "/clear/") === false) || $count_filters !== $count_url_filters) {
        define("ERROR_404","Y");
    }
}