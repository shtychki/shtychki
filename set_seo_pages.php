<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

if(! $USER->IsAdmin()) LocalRedirect('/', true);

\Bitrix\Main\Loader::IncludeModule("iblock");

//region Сброс шаблонов SEO для разделов и элементов не каталоговых инфоблоков
$iblockContentIDs = [];

$order = [];
$filter = [
    "ACTIVE" => "Y",
    "TYPE" => "aspro_max_content",
    "SITE_ID" => SITE_ID,
];

$res = CIBlock::GetList($order, $filter, false);

while($arRes = $res->Fetch()) {
    $iblockContentIDs[] = $arRes["ID"];
}

foreach($iblockContentIDs as $iblockId) {
    clearMetaSections($iblockId);
    clearMetaElements($iblockId);
}
//endregion


//region Получаем список ID инфоблоков типа - "Контент"
$excludeIblock = 114; //Blog

$iblockContentIDs = [];

$order = [];
$filter = [
    "ACTIVE" => "Y",
    "TYPE" => "aspro_max_content",
    "SITE_ID" => SITE_ID,
    "!ID" => $excludeIblock,
];

$res = CIBlock::GetList($order, $filter, false);

while($arRes = $res->Fetch()) {
    $iblockContentIDs[] = $arRes["ID"];
}

debug($iblockContentIDs);
//endregion

//region Установка meta title и meta description для некаталоговых страниц и разделов кроме БЛОГА
$titleTemplate = "{=this.Name} – &#128640; Мобильные штучки";
$descriptionTemplate = "&#11088;&#11088;&#11088;&#11088;&#11088; {=this.Name}. Магазин shtychki.by. Огромный выбор гаджетов и аксессуаров для мобильной техники. &#10004; Гарантия &#10004; Акции &#10004; Скидки &#10004; Доставка по Беларуси";
$h1 = "{=this.Name}";

//foreach($iblockContentIDs as $iblockId) {
//    $ipropTemplates = new \Bitrix\Iblock\InheritedProperty\IblockTemplates($iblockId);
//    $ipropTemplates->delete();
//
//    $arNewTemplates = [
//        "SECTION_META_TITLE" => "{$titleTemplate}",
//        "SECTION_META_DESCRIPTION" => "{$descriptionTemplate}",
//        "SECTION_PAGE_TITLE" => "{$h1}",
//        "ELEMENT_META_TITLE" => "{$titleTemplate}",
//        "ELEMENT_META_DESCRIPTION" => "{$descriptionTemplate}",
//        "ELEMENT_PAGE_TITLE" => "{$h1}",
//    ];
//
//    $ipropTemplates->set($arNewTemplates);
//}
//endregion

//region Установка meta title и meta description для БЛОГА
$iblockBlogId = 114;

$titleBlogTemplate = "{=this.Name} – &#128640; Мобильные штучки";
$descriptionBlogTemplate = "&#11088;&#11088;&#11088;&#11088;&#11088; {=this.Name}. &#9989; Интересные обзоры мира современных цифровых технологий от магазина shtychki.by.";
$h1Blog = "{=this.Name}";

//$ipropTemplates = new \Bitrix\Iblock\InheritedProperty\IblockTemplates($iblockBlogId);
//$ipropTemplates->delete();
//
//$arNewTemplates = [
//    "SECTION_META_TITLE" => "{$titleBlogTemplate}",
//    "SECTION_META_DESCRIPTION" => "{$descriptionBlogTemplate}",
//    "SECTION_PAGE_TITLE" => "{$h1Blog}",
//    "ELEMENT_META_TITLE" => "{$titleBlogTemplate}",
//    "ELEMENT_META_DESCRIPTION" => "{$descriptionTemplate}",
//    "ELEMENT_PAGE_TITLE" => "{$h1Blog}",
//];
//
//$ipropTemplates->set($arNewTemplates);
//endregion