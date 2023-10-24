<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}?>
<?$APPLICATION->IncludeComponent(
	"aspro:wrapper.block.max", 
	".default", 
	array(
		"IBLOCK_TYPE" => "aspro_max_catalog",
		"IBLOCK_ID" => "119",
		"FILTER_NAME" => "arRegionLink",
		"COMPONENT_TEMPLATE" => ".default",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"FILTER_PROP_CODE" => "tovar_dnya",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"HIDE_NOT_AVAILABLE" => "Y",
		"ELEMENT_COUNT" => "30",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_COMPARE" => "Y",
		"SHOW_MEASURE" => "Y",
		"DISPLAY_WISH_BUTTONS" => "Y",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_DISCOUNT_PERCENT_NUMBER" => "Y",
		"SHOW_DISCOUNT_TIME" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
		),
		"PRICE_CODE" => array(
			0 => "Цена в долларах",
		),
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPERTIES" => array(
		),
		"PARTIAL_PRODUCT_PROPERTIES" => "Y",
		"SHOW_RATING" => "N",
		"STIKERS_PROP" => "HIT",
		"SALE_STIKER" => "SALE_TEXT",
		"CONVERT_CURRENCY" => "Y",
		"TITLE_BLOCK" => "Товар дня",
		"STORES" => array(
			0 => "3",
			1 => "4",
			2 => "6",
			3 => "7",
			4 => "8",
			5 => "9",
			6 => "10",
			7 => "11",
			8 => "12",
			9 => "",
		),
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"TITLE_BLOCK_ALL" => "Весь каталог",
		"ALL_URL" => "catalog/",
		"USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"FIELDS" => array(
			0 => "",
			1 => "",
		),
		"CURRENCY_ID" => "BYN"
	),
	false
);?>