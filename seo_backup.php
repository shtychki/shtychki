<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

if(! $USER->IsAdmin()) LocalRedirect('/', true);

use Bitrix\Main\Diag\Debug;

\Bitrix\Main\Loader::IncludeModule("iblock");

/*
Копируем текущие шаблоны SEO в поля:

Старый META SECTION TITLE - UF_META_SECTION_TITLE
Старый META SECTION DESCRIPTION - UF_META_SECTION_DESCRIPTION
Старый SECTION H1 - UF_META_SECTION_H1

Старый META ELEMENT TITLE - UF_META_ELEMENT_TITLE
Старый META ELEMENT DESCRIPTION - UF_META_ELEMENT_DESCRIPTION
Старый ELEMENT H1 - UF_META_ELEMENT_H1
*/

$iblockId = 119;

//region Получение списка ID разделов
$sort = [
    "DEPTH_LEVEL" => "DESC"
];

$filter = [
    "IBLOCK_ID" => $iblockId,
    "ACTIVE" => "Y",
];

$select = [
    "IBLOCK_ID",
    "ID",
    "NAME"
];

$rsSect = CIBlockSection::GetList($sort, $filter, false, $select);

$sectionList = [];

while($arSect = $rsSect->Fetch()) {
    $sectionList[] = $arSect["ID"];
}

//debug($sectionList);
//debug(count($sectionList));

//endregion

//region Записываем старую META в поля разделов и сбрасываем шаблон

foreach($sectionList as $sectionId) {
    $ipropTemplates = new \Bitrix\Iblock\InheritedProperty\SectionTemplates($iblockId, $sectionId);

    $arTemplates = $ipropTemplates->findTemplates(); //получить
//    $ipropTemplates->delete();

    $bs = new CIBlockSection;

//    $arFields = [
//        "IBLOCK_ID" => $iblockId,
//        "UF_META_SECTION_TITLE" => $arTemplates["SECTION_META_TITLE"]["TEMPLATE"],
//        "UF_META_SECTION_DESCRIPTION" => $arTemplates["SECTION_META_DESCRIPTION"]["TEMPLATE"],
//        "UF_META_SECTION_H1" => $arTemplates["SECTION_PAGE_TITLE"]["TEMPLATE"],
//        "UF_META_ELEMENT_TITLE" => $arTemplates["ELEMENT_META_TITLE"]["TEMPLATE"],
//        "UF_META_ELEMENT_DESCRIPTION" => $arTemplates["ELEMENT_META_DESCRIPTION"]["TEMPLATE"],
//        "UF_META_ELEMENT_H1" => $arTemplates["ELEMENT_PAGE_TITLE"]["TEMPLATE"],
//    ];
//
//    $metaArray[$sectionId] = $arFields;
//
//    $res = $bs->Update($sectionId, $arFields);
//
//    if(! $res) {
//        debug($bs->LAST_ERROR);
//    }

    //debug($arFields);
}

//debug($metaArray);
//Debug::writeToFile($metaArray, '', 'seo_backup.txt');


//endregion





