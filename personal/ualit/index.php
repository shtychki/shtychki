<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("УАЛИТ");
?><?$APPLICATION->IncludeComponent(
	"bitrix:main.profile",
	"",
Array(),
false
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>