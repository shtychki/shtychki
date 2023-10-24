<?php
    ini_set("display_errors", 1);


    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


    $product_xml_id = "Yt3laQ6SgXZqnkYQG8L4p2";

    if (CModule::IncludeModule('iblock'))
    {
        $externalId = "Yt3laQ6SgXZqnkYQG8L4p2";

        // выбираем нужные поля для вывода
        $selectFields = array('*');

        // задаем параметры фильтрации
        $filter = array(
            'ELEMENT_XML_ID' => $externalId
        );

        $rsProducts = CCatalogProduct::GetList(
            array(),
            $filter,
            false,
            false,
            $selectFields
        );
        
        // перебираем результаты запроса
        while ($arProduct = $rsProducts->Fetch()) {
            // здесь можно использовать данные найденного товара
           
            echo 'ID: '.$arProduct['ID'].'<br>';
            echo 'Название: '.$arProduct['ELEMENT_NAME'].'<br>';
            echo 'Внешний код: '.$arProduct['ELEMENT_XML_ID'].'<br>';
        }
        

        $list = \Bitrix\Catalog\ProductTable::getList([
            'filter' => [
                "=ELEMENT_XML_ID" => $product_xml_id
            ]
        ]);
        echo json_encode($list, JSON_UNESCAPED_UNICODE);

        
    }

    echo "OK";