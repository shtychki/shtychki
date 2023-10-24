<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('design2u.syncms');

file_put_contents($_SERVER['DOCUMENT_ROOT'].'/started_aprix.txt', 'Y');

StartSync::StartProducts();

echo 'ok';