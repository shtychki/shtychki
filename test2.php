<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Diag\Debug;
use Itcentre\CatalogPrice;

global $USER;

if(! $USER->IsAdmin()) LocalRedirect('/', true);

//$iblockID = 119;
//$priceTypeID = 5;
//$catalog = new CatalogPrice($iblockID, $priceTypeID);

//$minPrices = $catalog->findMinPrice();
//$catalog->setMinPriceCategory();
//$catalog->setZeroMinPriceCategory();

//debug($minPrices);

\Bitrix\Main\Loader::includeModule('iblock');

$iblockId = 119;

// собираем подзапрос в таблицу свойств раздела
$subQuery = \Bitrix\Iblock\SectionPropertyTable::query()
    ->setSelect(['PROPERTY_ID'])
    ->where('IBLOCK_ID', $iblockId)
    ->where('SMART_FILTER', 'Y');

// получаем свойства выводимые в умный фильтр
$dbIblockProps = \Bitrix\Iblock\PropertyTable::query()
    ->where('IBLOCK_ID', $iblockId)
    ->whereIn('ID', $subQuery)
    ->setSelect(["ID", "NAME", "PROPERTY_TYPE"])
    ->exec();

$prop = [];

while ($arIblockProp = $dbIblockProps->fetch()){
    $prop[] = $arIblockProp;
}

debug($_SERVER["DOCUMENT_ROOT"]);
debug(count($prop));
debug($prop);