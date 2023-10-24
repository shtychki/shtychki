<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * Квази-компонент подставляющий значение выбранного свойства умного фильтра в: h1, title, description
 * при условии, что выбрано одно свойство и одно значение этого свойства
 */

//region SEO умного фильтра
$propertyColor = "=PROPERTY_2992"; //свойство цвет
$propertyBrand = "=PROPERTY_3885"; //свойство бренд
$propertyBrand_zaryadnye_ustroystva = "=PROPERTY_3423"; //свойство бренд
$propertyIblock = "IBLOCK_ID"; //если есть в фильтре не выводим
$highLoadBlockColorId = 22; //HL block цвета

//debug($GLOBALS["MAX_SMART_FILTER"]);
//debug($APPLICATION->GetCurPage(false));

//Массив умного фильтра со свойствами
$smartFilterNameCustom = $GLOBALS["MAX_SMART_FILTER"];
if(is_array($smartFilterNameCustom) && !empty($smartFilterNameCustom) && array_key_first($smartFilterNameCustom) !== '>=CATALOG_PRICE_7') {
//Количество примененных свойств в умном фильтре
    $countPropertiesFilter = count($smartFilterNameCustom);
//Количество выбранных значений в одном свойстве
    $countValueProperty = $countPropertiesFilter === 1 ? count($smartFilterNameCustom[array_key_first($smartFilterNameCustom)]) : false;
//Проверяем ключ IBLOCK_ID в массиве фильтра
    $filterHasIblock = array_key_exists($propertyIblock, $smartFilterNameCustom);
}

if(!empty($GLOBALS["MAX_SMART_FILTER"])) {
//    debug($GLOBALS["MAX_SMART_FILTER"]);
    //Если выбран 1 параметр и 1 значение в свойстве (+ IBLOCK_ID присутствует в массиве)
    if($countPropertiesFilter === 1 && $countValueProperty === 1 || $filterHasIblock) {

//    $smartFilterNameCustom = $GLOBALS["MAX_SMART_FILTER"];
        //Убираем из массива IBLOCK_ID
        unset($smartFilterNameCustom[$propertyIblock]);

        $currentH1 = $APPLICATION->GetTitle(false);
        $currentTitle = $APPLICATION->GetProperty('title');
        $currentDescription = $APPLICATION->GetProperty('description');

        //Если свойство цвет
        if(array_key_exists($propertyColor, $smartFilterNameCustom)) {
            $propertyFilter = $smartFilterNameCustom[array_key_first($smartFilterNameCustom)];
            $xmlId = $propertyFilter[0];
            $propertyName = getHLelementByXML_ID($highLoadBlockColorId, $xmlId)["UF_NAME"];
        //Если свойство бренд
        } elseif(array_key_exists($propertyBrand, $smartFilterNameCustom) || array_key_exists($propertyBrand_zaryadnye_ustroystva, $smartFilterNameCustom)) {
            $propertyFilter = $smartFilterNameCustom[array_key_first($smartFilterNameCustom)];
            $propValueId = $propertyFilter[0];
            $propertyName = getListValueById($propValueId);
            if(empty($propertyName)){
                $propertyName = getelementByID($propValueId);
            }
        //Если свойство цена
        } elseif (preg_grep("/^(.*)CATALOG_PRICE(.*)$/", array_keys($smartFilterNameCustom))) {
            $propertyFilter = $smartFilterNameCustom[array_key_first($smartFilterNameCustom)];
            $propertyName = "цена от {$propertyFilter} руб";
        } else {
            $propertyFilter = $smartFilterNameCustom[array_key_first($smartFilterNameCustom)];
            $propertyName = empty(getListValueById($propertyFilter[0]))?$propertyFilter[0]:getListValueById($propertyFilter[0]);
        }

        //Если выбрано одно значение свойства
        if(count($propertyFilter) === 1) {
            $APPLICATION->SetTitle("{$currentH1} {$propertyName}");

            $changedTitle = str_replace("{$currentH1}", "{$currentH1} {$propertyName}", "$currentTitle");
            $APPLICATION->SetPageProperty("title", $changedTitle);

            $changedDescription = str_replace("{$currentH1}", "{$currentH1} {$propertyName}", "$currentDescription");
            $APPLICATION->SetPageProperty("description", $changedDescription);

            //Делаем замену og:title и og:description в init.php
            $GLOBALS['NEW_META_ITCENTRE'] = [
                "CHANGED_TITLE" => $changedTitle,
                "CHANGED_DESCRIPTION" => $changedDescription
            ];
            ?>
        <?}
//Иначе прописываем canonical на текущий раздел
    } else {
        $parseSmartFilterUrl = preg_replace('/(?>filter).*/', '$1', $APPLICATION->GetCurPage(false));
        $currentSectionPageUrl = $GLOBALS["CURRENT_SECTION_PAGE_CSTM"] ?: $parseSmartFilterUrl;

        \Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="canonical" href="https://'.SITE_SERVER_NAME.$currentSectionPageUrl.'" />', true);
    }
}
//endregion
