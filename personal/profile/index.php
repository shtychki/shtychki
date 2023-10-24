<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Настройка профилей");
?><h1>Настройка профилей</h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.profile",
	"",
Array(),
false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>