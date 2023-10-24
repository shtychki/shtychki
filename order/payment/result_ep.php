<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$GLOBALS['APPLICATION']->RestartBuffer();
$APPLICATION->SetTitle("Прием информации об оплате");

$APPLICATION->IncludeComponent(
	"bitrix:sale.order.payment.receive",
	"",
	array(
		//"PAY_SYSTEM_ID" => $_GET['p_id'],
		"PAY_SYSTEM_ID" => '28',
		"PERSON_TYPE_ID" => "1"
	),
	false
);

//die();
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/webpay.payment/result_rec.php");

?>