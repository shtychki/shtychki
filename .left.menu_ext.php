<? 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
global $APPLICATION;
//if (!$USER->IsAdmin())
//$aMenuLinksExt = $APPLICATION->IncludeComponent(
//	"custom:menu.sections",
//	"",
//	array(
//		"IBLOCK_TYPE" => COption::GetOptionString("dw_electro", "PRODUCT_IBLOCK_TYPE"),
//		"IBLOCK_ID" => COption::GetOptionString("dw_electro", "PRODUCT_IBLOCK_ID"),
//		"DEPTH_LEVEL" => "3",
//		"CACHE_TYPE" => "A",
//		"CACHE_TIME" => "3600000",
//		"IS_SEF" => "N",
//		"ID" => $_REQUEST["ID"],
//		"SECTION_URL" => ""
//	),
//	false
//);
//else
    $aMenuLinksExt = $APPLICATION->IncludeComponent("shtychki:menu.smart.sections", "", array(
        "IS_SEF" => "Y",
        "SEF_BASE_URL" => "",
        "SECTION_PAGE_URL" => '',
        "DETAIL_PAGE_URL" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
        "IBLOCK_TYPE" => COption::GetOptionString("dw_electro", "PRODUCT_IBLOCK_TYPE"),
        "IBLOCK_ID" => COption::GetOptionString("dw_electro", "PRODUCT_IBLOCK_ID"),
        "DEPTH_LEVEL" => "2",
        "CACHE_TYPE" => "A",
        "SMART_FILTER_URL" => 'filter/#SMART_FILTER_PATH#/apply/',
        "PROPERTIES" => [45],
//        "ROOT_ITEM_PARAMS" => [
//            5 => ["COLUMN_COUNT" => 2, "COLUMN_BREAK" => "N", "FULLSIZE" => "Y"],
//            98 => ["COLUMNIZE" => "N", "OWN_COLUMN" => "N", "FULLSIZE" => "N", "MOVE_TO" => 0],
//        ],
//        "FILTER" => ["!PROPERTY_PRO" => 1]
    ), false, Array('HIDE_ICONS' => 'Y'));
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt); 
?>