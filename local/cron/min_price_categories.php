<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
set_time_limit(0);

use Bitrix\Main\Diag\Debug;
use Itcentre\CatalogPrice;

$catalog = new CatalogPrice(119, 7);
$minPricesCategory = $catalog->findMinPrice();
$catalog->setMinPriceCategory();


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php"); //если ругается на него php, то комментим и дальше пользуемся
?>