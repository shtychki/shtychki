<?php
//v1
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
set_time_limit(0);

use Bitrix\Main\Loader;
use Bitrix\Iblock\PropertyIndex;
use Itcentre\SitemapFilter;

CBitrixComponent::includeComponentClass('bitrix:catalog.smart.filter');

$iblockID = 119;
$iblockSkuID = 122;

//region Генерируем sitemap для умного фильтра
try {
    $fileNameSitemap = 'sitemap-filter-custom.xml'; //полное название файла sitemap
    $fileRobots = $_SERVER["DOCUMENT_ROOT"]."/robots.txt"; //путь к robots.txt
    $sitemapString = "Sitemap: https://test.shtychki.by/{$fileNameSitemap}"; //строка для записи в robots.txt

    $filterSitemap = new SitemapFilter($iblockID, $iblockSkuID, "https://test.shtychki.by");
    $filterSitemapLinks = $filterSitemap->generateSmartLinks();
    $filterSitemap->generateSitemap($_SERVER["DOCUMENT_ROOT"], $fileNameSitemap);

    //Если в robots.txt нет строки в sitemap добавляем в конец
    if(!strripos(file_get_contents($fileRobots), 'sitemap-filter-custom.xml')) {
        file_put_contents($fileRobots, PHP_EOL.$sitemapString, FILE_APPEND | LOCK_EX);
    }

} catch (Throwable $e) {
    echo $e->getMessage();
}
//endregion

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php"); //если ругается на него php, то комментим и дальше пользуемся
?>