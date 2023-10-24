<?php
    ini_set("log_errors", 1);
    ini_set("error_log", "err_log_171.log");

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

	$key = $_GET['key'];
	if ("qwe123" != $key) die("Err");

    $compare = json_decode($_POST['compare'], true);
    $order_id = $_POST['order_id'];

    file_put_contents("hook.log", date("Y-m-d H:i:s")."\n".json_encode($_REQUEST)."\n\n", FILE_APPEND);

    $result_resp = [];

    if (CModule::IncludeModule('sale'))
    {
        $order = \Bitrix\Sale\Order::load($order_id);
        $basket = $order->getBasket();
        foreach ($basket as $basketItem) {
            $id = $basketItem->getProductId();
            $b24_id = $compare['ids_conv'][$id]['b24_id'];
            if (isset($compare['to_change'][$b24_id])) {
                $basketItem->setFields([
                    'QUANTITY' => $compare['to_change'][$b24_id]['qnt'],
                    'PRICE' => $compare['to_change'][$b24_id]['price'],
                    'CUSTOM_PRICE' => 'Y',
                ]);
                $result_resp[] = "Обновляем ".$b24_id;
            }
            if (isset($compare['to_remove'][$b24_id])) {
                $result_resp[] = "Удаляем ".$b24_id;
                $basketItem->delete();
            }
        }

        foreach ($compare['to_add'] as $b24_id=>$item) {
            $bus_id = 0;
            foreach ($compare['ids_conv'] as $_bus_id => $cache) {
                if ($cache['b24_id'] == $b24_id)
                    $bus_id = $_bus_id;
            }
            if ($bus_id == 0) {
                $result_resp[] = "Пытались добавить ".$b24_id." - не нашли bus_id, ищем по xml_id";
                CModule::IncludeModule('iblock');
                $externalId = "Yt3laQ6SgXZqnkYQG8L4p2";

                // выбираем нужные поля для вывода
                $selectFields = array('*');

                // задаем параметры фильтрации
                $filter = array(
                    'ELEMENT_XML_ID' => $item['XML_ID']
                );

                $rsProducts = CCatalogProduct::GetList(
                    array(),
                    $filter,
                    false,
                    false,
                    $selectFields
                );
               
                // перебираем результаты запроса
                if ($arProduct = $rsProducts->Fetch()) {
                    $result_resp[] = json_encode($arProduct, JSON_UNESCAPED_UNICODE);
                    $bus_id = $arProduct['ID'];
                    $result_resp[] = "Добавляем ID ".$bus_id;
                } else {
                    $result_resp[] = "Пытались добавить ".$b24_id." - не нашли по xml_id, пропускаем";
                    continue;
                }
            };
            $res = \Bitrix\Catalog\Product\Basket::addProductToBasket($basket, [
                'PRODUCT_ID' => $bus_id, // ID товара, обязательно
                'QUANTITY' => $item['qnt'],
                'CURRENCY' => 'BYN',
                'PRICE' => $item['price'],
                'CUSTOM_PRICE' => 'Y',
                'IS_ENABLED' => 'Y'
            ], array('SITE_ID' => \Bitrix\Main\Context::getCurrent()->getSite()));
            if ($res->isSuccess()) {
                $result_resp[] = "Добавилил";
            } else {
                $result_resp[] = "Возникли ошибки:".join(";", $res->getErrorMessages());
            }
        }
        $order->save();
    }

    echo join("\n", $result_resp);