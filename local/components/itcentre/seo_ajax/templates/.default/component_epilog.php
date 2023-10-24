<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * Квази-компонент подставляющий значение выбранного свойства умного фильтра в: h1, title, description
 * при условии, что выбрано одно свойство и одно значение этого свойства
 */
$arMeta = array();
//region SEO умного фильтра
$propertyColor = "=PROPERTY_2992"; //свойство цвет
$propertyBrand = "=PROPERTY_3885"; //свойство бренд
$propertyBrand_zaryadnye_ustroystva = "=PROPERTY_3423"; //свойство бренд
$propertyIblock = "IBLOCK_ID"; //если есть в фильтре не выводим
$highLoadBlockColorId = 22; //HL block цвета

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

//Если выбран 1 параметр и 1 значение в свойстве (+ IBLOCK_ID присутствует в массиве)
if((!empty($GLOBALS["MAX_SMART_FILTER"]) && $countPropertiesFilter === 1 && $countValueProperty === 1) || (!empty($GLOBALS["MAX_SMART_FILTER"] && $filterHasIblock))) {
//    if($countPropertiesFilter === 1 && $countValueProperty === 1 || $filterHasIblock) {
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
        if(count($propertyFilter) === 1 && $arParams['METADATA']['TITLE'] == $arParams['TITLE']) {
            $arMeta['changedH1'] = $currentH1 . ' ' . $propertyName;
            $arMeta['changedTitle'] = str_replace("{$currentH1}", "{$currentH1} {$propertyName}", "$currentTitle");
            $arMeta['changedDescription'] = str_replace("{$currentH1}", "{$currentH1} {$propertyName}", "$currentDescription");

            //Делаем замену og:title и og:description в init.php
            $GLOBALS['NEW_META_ITCENTRE'] = [
                "CHANGED_TITLE" => $arMeta['changedTitle'],
                "CHANGED_DESCRIPTION" => $arMeta['changedDescription']
            ];
            ?>
        <?} else {
            $arMeta = generate_meta();
        }
} else {
    $arMeta = generate_meta();
}
?>
<script type="text/javascript">
    BX.removeCustomEvent("onAjaxSuccessFilterMeta", function tt(e){});
    BX.addCustomEvent("onAjaxSuccessFilterMeta", function tt(e){
        let arAjaxPageTitle = <?=CUtil::PhpToJSObject($arMeta['changedH1']);?>;
        let arAjaxMetaTitle = <?=CUtil::PhpToJSObject($arMeta['changedTitle']);?>;
        let arAjaxMetaDesc = <?=CUtil::PhpToJSObject($arMeta['changedDescription']);?>;
        var decodeHTML = function (html) {
            var txt = document.createElement('textarea');
            txt.innerHTML = html;
            return txt.value;
        };
        <?if( $arParams["AJAX_MODE"] !== "Y" ):?>
           if (arAjaxPageTitle){
               BX.ajax.UpdatePageTitle(arAjaxPageTitle);
           }
           if (arAjaxMetaTitle) {
               BX.ajax.UpdateWindowTitle(arAjaxMetaTitle);
               document.querySelector('meta[property="og:title"]').setAttribute("content", decodeHTML(arAjaxMetaTitle));
           }
           if (arAjaxMetaDesc){
               document.querySelector('meta[property="og:description"]').setAttribute("content", decodeHTML(arAjaxMetaDesc));
               document.getElementsByTagName('meta')["description"].content = decodeHTML(arAjaxMetaDesc);
           }
        <?endif;?>
    });
</script>
