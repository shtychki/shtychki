<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?global $isShowBottomBanner;?>
<?if($isShowBottomBanner):?>
	<?$APPLICATION->IncludeComponent(
	"aspro:com.banners.next",
	"adv_bottom",
	Array(
		"BANNER_TYPE_THEME" => "WIDE",
		"BANNER_TYPE_THEME_CHILD" => "20",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"FILTER_NAME" => "arRegionLink",
		"IBLOCK_ID" => "36",
		"IBLOCK_TYPE" => "aspro_next_adv",
		"NEWS_COUNT" => "10",
		"NEWS_COUNT2" => "20",
		"PROPERTY_CODE" => array("URL",""),
		"SET_BANNER_TYPE_FROM_THEME" => "N",
		"SORT_BY1" => "SORT",
		"SORT_BY2" => "ID",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "DESC",
		"TYPE_BANNERS_IBLOCK_ID" => "33"
	)
);?>
<?endif;?>