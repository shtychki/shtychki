<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("История заказов");
?><h1>История заказов</h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order", 
	"template1", 
	array(
		"PROP_1" => array(
		),
		"PROP_2" => array(
		),
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SEF_MODE" => "N",
		"SEF_FOLDER" => SITE_DIR."personal/history/",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"ORDERS_PER_PAGE" => "20",
		"PATH_TO_PAYMENT" => SITE_DIR."personal/payment/",
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"SET_TITLE" => "Y",
		"SAVE_IN_SESSION" => "Y",
		"NAV_TEMPLATE" => "",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"HISTORIC_STATUSES" => array(
			0 => "F",
		),
		"STATUS_COLOR_N" => "green",
		"STATUS_COLOR_F" => "gray",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>