<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

if(! $USER->IsAdmin()) LocalRedirect('/', true);

use Bitrix\Main\Loader;
use Bitrix\Iblock\PropertyIndex;
use Itcentre\SitemapFilter;

Loader::includeModule("main");

CBitrixComponent::includeComponentClass('bitrix:catalog.smart.filter');

$iblockID = 119;
$iblockSkuID = 122;

try {
    $fileNameSitemap = 'sitemap-filter-custom.xml';
    $fileRobots = $_SERVER["DOCUMENT_ROOT"]."/robots.txt";
    $sitemapString = "Sitemap: https://test.shtychki.by/{$fileNameSitemap}";

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
