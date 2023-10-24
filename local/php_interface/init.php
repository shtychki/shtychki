<?
/**/
$pos = strpos($_SERVER['REQUEST_URI'], '/bitrix/');
$pos_aj = strpos($_SERVER['REQUEST_URI'], '/ajax/');
/* if ($pos === false && $pos_aj === false) {
    $parts_url = explode("?", $_SERVER['REQUEST_URI']);
    $parts_url_0= $parts_url[0]; // кусок1
    $parts_url_1= $parts_url[1]; // кусок2

    if ( $parts_url_0 != strtolower( $parts_url_0) ) {
        if(empty($parts_url_1)){
            header('Location: https://'.$_SERVER['HTTP_HOST'] .
                strtolower($parts_url_0), true, 301);
        }else{
            header('Location: https://'.$_SERVER['HTTP_HOST'] .
                strtolower($parts_url_0).'?'.$parts_url_1, true, 301);
        }
        exit();
    }
} */
/**/
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/wsrubi.smtp/classes/general/wsrubismtp.php");
AddEventHandler('form', 'onBeforeResultAdd', 'my_onBeforeResultAdd');
function my_onBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES) {
	global $APPLICATION;
	// действие обработчика распространяется только на форму с ID=11
	if ($WEB_FORM_ID == 11) 
	{
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/1clog.txt", "\n****************\n".print_r($arFields, true), FILE_APPEND | LOCK_EX);       
        file_put_contents($_SERVER["DOCUMENT_ROOT"]."/1clog.txt", "\n****************\n".print_r($arrVALUES, true), FILE_APPEND | LOCK_EX);       

		//\Bitrix\Main\Loader::includeModule('iblock');  
		CModule::IncludeModule("iblock");
		$el = new CIBlockElement;
		$arLoadProductArray = Array(
		"IBLOCK_ID" => 57,
		"NAME" => $arrVALUES['form_text_52'],
		"ACTIVE" => "N",// не активен
		"PROPERTY_VALUES" => array(
			"FIO" => $arrVALUES['form_text_52'],
			"PHONE" => $arrVALUES['form_text_53'],
			"EMAIL" => $arrVALUES['form_email_54'],
			"OCENKA" => $arrVALUES['form_text_55'],
			"OTZIV" => $arrVALUES['form_textarea_56'],
			)
		);
		//$oElement = new CIBlockElement();
		//$idElement = $oElement->Add($arLoadProductArray, true, true, true); 
		if($PRODUCT_ID = $el->Add($arLoadProductArray))
		  echo "New ID: ".$PRODUCT_ID;
		else
		  echo "Error: ".$el->LAST_ERROR;
	}           
}

//-- Добавление обработчика события

AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");

//-- Собственно обработчик события

function bxModifySaleMails($orderID, &$eventName, &$arFields) {
	$arOrder = CSaleOrder::GetByID($orderID);
  
	//-- получаем телефоны и адрес
	$order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
	$phone = "";
	$index = ""; 
	$country_name = "";
	$city_name = "";
	$city = "";
	$address = "";
	$street = "";
	$apartment = "";
	$driveway = "";
	$floor = "";
	
	while ($arProps = $order_props->Fetch()) {
		if ($arProps["CODE"] == "PHONE") {
			$phone = htmlspecialchars($arProps["VALUE"]);
		}
		if ($arProps["CODE"] == "CITY") {
			$arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
			$country_name =  $arLocs["COUNTRY_NAME_ORIG"];
			$city_name = $arLocs["CITY_NAME_ORIG"];
			
			$city = $country_name.", ".$city_name;
		}

		if ($arProps["CODE"] == "INDEX" || $arProps["CODE"] == "ZIP") {
			$index = $arProps["VALUE"];
		}

		if ($arProps["CODE"] == "ADDRESS") {
			$address = $arProps["VALUE"];
		}
		
		//if ($arProps["CODE"] == "CITY") {
			//$city = $arProps["VALUE"];
		//}
		
		if ($arProps["CODE"] == "STREET") {
			$street = $arProps["VALUE"];
		}
		
		if ($arProps["CODE"] == "HOUSE") {
			$home = $arProps["VALUE"];
		}
		
		if ($arProps["CODE"] == "APARTMENT") {
			$apartment = $arProps["VALUE"];
		}
		
		if ($arProps["CODE"] == "DRIVEWAY") {
			$driveway = $arProps["VALUE"];
		}
		
		if ($arProps["CODE"] == "FLOOR") {
			$floor = $arProps["VALUE"];
		}
	}
	
	//$index = (!empty($index) ? "Индекс: ".$index.", " : "");
	//$index = (!empty($index) ? "Индекс: ".$index.", " : "");
	//$country_name = (!empty($country_name) ? "Страна: ".$country_name.", " : "");
	//$country_name = (!empty($country_name) ? "Страна: ".$country_name.", " : "");
	//$city_name = (!empty($city_name) ? "Город: ".$city_name.", " : "");
	//$city_name = (!empty($city_name) ? "Город: ".$city_name.", " : "");
	//$street = (!empty($street) ? "Улица: ".$street.", " : "");
	//$home = (!empty($home) ? "дом: ".$home.", " : "");
	//$apartment = (!empty($apartment) ? "квартира: ".$apartment.", " : "");
	//$driveway = (!empty($driveway) ? "подъезд: ".$driveway.", " : "");
	//$floor = (!empty($floor) ? "этаж: ".$floor : "");

	$full_address = $index.", ".$country_name."-".$city_name." ".$address;
	//$full_address = "";
	$full_address_new = $index.", ".$city.", ".$street.", ".$home.", ".$apartment.", ".$driveway.", ".$floor;
	//$full_address_new = "";

	//-- получаем название службы доставки
	$arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
	$delivery_name = "";
	if ($arDeliv) {
		$delivery_name = $arDeliv["NAME"];
	}

	//-- получаем название платежной системы
	$arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
	$pay_system_name = "";
	if ($arPaySystem) {
		$pay_system_name = $arPaySystem["NAME"];
	}

	//-- добавляем новые поля в массив результатов
	$arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"]; 
	$arFields["PHONE"] =  $phone;
	$arFields["DELIVERY_NAME"] =  $delivery_name;
	$arFields["PAY_SYSTEM_NAME"] =  $pay_system_name;
	$arFields["FULL_ADDRESS"] = $full_address;
	$arFields["FULL_ADDRESS_NEW"] = $full_address_new;
	
	$arFields["INDEX"] = $index;
	$arFields["CITY"] = $city;
	$arFields["STREET"] = $street;
	$arFields["HOME"] = $home;
	$arFields["APARTMENT"] = $apartment;
	$arFields["DRIVEWAY"] = $driveway;
	$arFields["FLOOR"] = $floor;
}

/**/
AddEventHandler("sale", "OnSaleBeforeOrderCanceled", "OnSaleBeforeOrderCanceledHandlers");
function OnSaleBeforeOrderCanceledHandlers(&$order){
	if ($order->isCanceled()){
		$order->setField("STATUS_ID", 'W');
	}
}

AddEventHandler("sale", "OnSaleStatusOrder", "IfUserCancelOrder");
function IfUserCancelOrder($orderId, $CANCELED) {
    if (CModule::IncludeModule("sale")){
        $arOrder = CSaleOrder::GetByID($orderId);
        if (($arOrder["USER_ID"] == $arOrder["EMP_CANCELED_ID"]) && $CANCELED== 'Y') {
			$order = \Bitrix\Sale\Order::load($orderId); // объект заказа
			$order->setField("STATUS_ID", 'W');
		}
    }
}
/**/

// ROISTAT BEGIN

AddEventHandler('sale', 'OnOrderSave', 'roistatLead');
function roistatLead($ID, $fields, $orderFields, $isNew){
    CModule::IncludeModule('iblock');
    CModule::IncludeModule('sale');
    $orderId = $ID;

    if (!empty($orderId) && $isNew) {

        $props = CSaleOrderPropsValue::GetOrderProps($orderId);
        $dbBasket = CSaleBasket::GetList(Array("ID"=>"ASC"), Array("ORDER_ID"=>$orderId));
        $name  = null;
        $email = null;
        $phone = null;
        $comment = "Номер заказа: {$orderId}" . PHP_EOL . "Товар: ";
        $totalPrice = 0;

        while ($item = $dbBasket->Fetch()) {
            $price = (int) $item['PRICE'] * $item['QUANTITY'];
            $totalPrice += $price;
            $comment .= $item['NAME'] . ' ' . $item['QUANTITY'] . 'шт. ' . $price . 'руб.' . PHP_EOL;
        }

        $comment .= "Итого: {$totalPrice} руб." . PHP_EOL;

        while ($prop = $props->Fetch()) {
            switch ($prop['ORDER_PROPS_ID']) {
                case '1':
                    $name = $prop['VALUE'];
                    break;

                case '2':
                    $email = $prop['VALUE'];
                    break;

                case '3':
                    $phone = $prop['VALUE'];
                    break;

                case '7':
                    if (!empty($prop['VALUE'])) {
                        $comment .= 'Адрес доставки: ' . $prop['VALUE'];
                    }
                    break;
            }
        }
        $roistatData = array(
            'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : null,
            'key'     => 'OWM3NDdjYjgwOWQwMjc3NWNhY2FmYWQyMzMyMDVmNTU6MTk1MzIw',
            'title'   => "Заказ №{$orderId}",
            'phone'   => $phone,
            'name'    => $name,
            'email'   => $email,
            'is_skip_sending' => '1',
            'fields'  => array(
            ),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
    }
}

// ROISTAT END

AddEventHandler('main', 'OnBeforeUserLogin', array('CUserEx', 'OnBeforeUserLogin'));
AddEventHandler('main', 'OnBeforeUserRegister', array('CUserEx', 'OnBeforeUserRegister'));
AddEventHandler('main', 'OnBeforeEventSend', array('CUserEx', 'OnBeforeEventSend'));
class CUserEx
{
	static function OnBeforeUserLogin(&$fields) {
		$phone = Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($fields['LOGIN']);
		$user = \Bitrix\Main\UserPhoneAuthTable::getList($parameters = array(
			'filter'=>array('PHONE_NUMBER' =>$phone)
		));
		if($row = $user->fetch())
		{
			$rsUser = CUser::GetByID($row['USER_ID']);
			$arUser = $rsUser->Fetch();
			$fields['LOGIN'] = $arUser['LOGIN'];
		}
	}
	
	static function OnBeforeUserRegister(&$arFields) {
		$arFields['LOGIN'] = $arFields['PERSONAL_PHONE'];
	}
	
	static function OnBeforeEventSend(&$arFields, &$arTemplate) {
		//\Bitrix\Main\Diag\Debug::writeToFile(array('avto', date('Y.m.d H:i:s'), '$$arFields', $arFields, '$arTemplate', $arTemplate), null, '/local/send.php');
		if($arFields['EMAIL'] == '') {
			$arTemplate['EMAIL_TO'] = 'order@shtychki.by';
			$arFields['EMAIL'] = 'order@shtychki.by';
		}
		if($arTemplate['EVENT_NAME'] == 'USER_INFO') {
			$rsUser = CUser::GetByID($arFields['USER_ID']);
			$arUser = $rsUser->Fetch();
			
			$phone = $arUser['PERSONAL_PHONE'];
			$arTemplate['PERSONAL_PHONE'] = $phone;
			$arFields['PERSONAL_PHONE'] = $phone;
		}
		//\Bitrix\Main\Diag\Debug::writeToFile(array('avto', date('Y.m.d H:i:s'), '$$arFields', $arFields, '$arTemplate', $arTemplate, '$phone', $phone), null, '/local/send.php');
   }
}

CModule::AddAutoloadClasses("", array(
    '\Itcentre\CatalogPrice' => '/local/classes/CatalogPrice.php',
	'\Itcentre\SitemapFilter' => '/local/classes/SitemapFilter.php'
));

function debug($data) {
    global $USER;

    if($USER->IsAdmin() && $_GET["debug"] == "Y") {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

AddEventHandler('main', 'OnEpilog', 'orPagenMeta');

function orPagenMeta() {
    global $APPLICATION;

    if (!empty($_GET['PAGEN_1']) && intval($_GET['PAGEN_1']) > 1) {

        $page = intval($_GET['PAGEN_1']);

        //title
        $title = $APPLICATION->GetProperty('title');
        $APPLICATION->SetPageProperty('title', "{$title} — Страница {$page}");

        //description
        $description = $APPLICATION->GetProperty('description');
        $APPLICATION->SetPageProperty('description', "{$description} — Страница {$page}");
    }
}

/**
 * Очистка SEO мета-данных у элементов инфоблока
 */
function clearMetaElements($iblockId) {

    if(!CModule::IncludeModule('iblock')) {
        throw new Exception('Не подключен модуль инфоблока');
    }

    if(! $iblockId) {
        throw new Exception('Не указан ИД инфоблока');
    }

    $arFilter = array(
        'IBLOCK_ID' => $iblockId,
    );

    $res = CIBlockElement::GetList(false, $arFilter, array('IBLOCK_ID','ID'));

    while($el = $res->GetNext()):
        echo $arElementsID[] = $el['ID'];
    endwhile;

    foreach($arElementsID as $elementId):
        $ipropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates ($iblockId, $elementId); //еще раз уточняем ID инфоблока
        $ipropTemplates->delete();
    endforeach;

}

function clearMetaSections($iblockId) {

    if(!CModule::IncludeModule('iblock')) {
        throw new Exception('Не подключен модуль инфоблока');
    }

    if(! $iblockId) {
        throw new Exception('Не указан ИД инфоблока');
    }

    $arFilter = array(
        'IBLOCK_ID' => $iblockId,
    );

    $res = CIBlockSection::GetList(false, $arFilter, array('IBLOCK_ID','ID'));

    while($el = $res->GetNext()):
        echo $arSectionsID[] = $el['ID'];
    endwhile;

    foreach($arSectionsID as $sectionId):
        $ipropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates ($iblockId, $sectionId); //еще раз уточняем ID инфоблока
        $ipropTemplates->delete();
    endforeach;
}

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

function getelementByID($Id) {
    $res = CIBlockElement::GetByID($Id);
    if($ar_res = $res->GetNext())
        return $ar_res['NAME'];

//    $result = [];
//
//    while($arData = $rsData->Fetch()) {
//        $result[] = $arData;
//    }

//    return $rsData->Fetch();
}

function getHLelementByXML_ID($highloadBlockId, $xmlId) {
    \Bitrix\Main\Loader::includeModule("highloadblock");

    $hlbl = $highloadBlockId; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $rsData = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "ASC"),
        "filter" => array("UF_XML_ID" => $xmlId)  // Задаем параметры фильтра выборки
    ));

//    $result = [];
//
//    while($arData = $rsData->Fetch()) {
//        $result[] = $arData;
//    }

    return $rsData->Fetch();
}

// Получаем значение свойства типа список
function getListValueById($id)
{
   $UserField = CIBlockPropertyEnum::GetList(array(), array("ID" => $id));
   if($UserFieldAr = $UserField->GetNext())
   {
      return $UserFieldAr["VALUE"];
   }
   else return false;
}

/**
 * Заменяем og:title и og:description для страниц фильтров
 */
AddEventHandler("main", "OnEndBufferContent", "changeSeoItcentre");

function changeSeoItcentre(&$content) {
    if(isset($GLOBALS['NEW_META_ITCENTRE']) && !empty($GLOBALS['NEW_META_ITCENTRE'])) {
        $newMeta = $GLOBALS['NEW_META_ITCENTRE'];
        //og:title
        $content = preg_replace("#<meta property=\"og:title\" content=\"(.*)\" />#", "<meta property=\"og:title\" content=\"{$newMeta["CHANGED_TITLE"]}\" />", $content);
        //og:description
        $content = preg_replace("#<meta property=\"og:description\" content=\"(.*)\" />#", "<meta property=\"og:description\" content=\"{$newMeta["CHANGED_DESCRIPTION"]}\" />", $content);

        return $content;
    }
}

//Если страница фильтра неправильная или свойство не учавствует в фильтре
AddEventHandler("main", "OnEpilog", "errorFilterPage");
function errorFilterPage() {
    $page_404 = "/404.php";
    global $APPLICATION;

    if (strpos($APPLICATION->GetCurPage(), $page_404) === false && defined("ERROR_404") && ERROR_404 == "Y") {
        \Bitrix\Iblock\Component\Tools::process404(
            '404 Не найдено', // сообщение, если не показываем стандартную страницу
            true, // устанавливать ли константу ERROR_404
            true, // устанавливать ли статус страницы
            true, // показывать ли страницу 404
            false // страница 404, отличная от стандартной

        );
    }
}

Bitrix\Main\Loader::registerAutoLoadClasses(null, ['\lib\CMaxMod' => '/local/php_interface/lib/CMaxMod.php']);
Bitrix\Main\Loader::registerAutoLoadClasses(null, ['\lib\PhoneAuthMod' => '/local/php_interface/lib/phoneauth.php']);

function customMultiSort($array,$field) {
    $sortArr = array();
    foreach($array as $key=>$val){
        $sortArr[$key] = $val[$field];
    }
    array_multisort($sortArr,$array);
    return $array;
}

function sort_nested_arrays( $array, $args = array('DISABLED' => 'asc', 'VALUE' => 'asc') ){
    usort( $array, function( $a, $b ) use ( $args ){
        $res = 0;
        $a = (object) $a;
        $b = (object) $b;
        foreach( $args as $k => $v ){
            if( $a->$k == $b->$k ) continue;
            $res = ( $a->$k < $b->$k ) ? -1 : 1;
            if( $v=='desc' ) $res= -$res;
            break;
        }
        return $res;
    } );
    return $array;
}

function sortFuncMaxToMin($keys)
{
    if (is_array($keys))
    {
        return function ($a, $b) use ($keys)
        {
            foreach ($keys as $k) {
                if ($a[$k] != $b[$k]) {
                    return ($a[$k] < $b[$k]) ? 1 : -1;
                }
            }

            return 0;
        };
    }
    else
    {
        return function ($a, $b) use ($keys)
        {
            if ($a[$keys] == $b[$keys]) {
                return 0;
            }
            return ($a[$keys] < $b[$keys]) ? 1 : -1;
        };
    }
}

function generate_meta() {
    global $APPLICATION;
    $arMeta['changedH1'] = $APPLICATION->GetTitle(false);
    $arMeta['changedTitle'] = $APPLICATION->GetProperty('title');
    $arMeta['changedDescription'] = $APPLICATION->GetProperty('description');
    return $arMeta;
}

AddEventHandler('sale', 'OnOrderSave', 'OrderSave');
function OrderSave($orderID, $fields, $orderFields){
    if ($orderFields['ORDER_PROP'][1]) {
        $user = new CUser;
        $user->Update($fields['USER_ID'], array('LAST_NAME' => $orderFields['ORDER_PROP'][1]));
    }
}

AddEventHandler("main", "OnEpilog", "OnABCtoabc");
function OnABCtoabc(){
    $notBitrix = strpos($_SERVER['REQUEST_URI'], '/bitrix/');
    $notAjax = strpos($_SERVER['REQUEST_URI'], '/ajax/');
    $haveGet = strripos($_SERVER['REQUEST_URI'], "?") !== false;
    if ($haveGet) {
        $url = explode("?", $_SERVER['REQUEST_URI']);
        if (($_SERVER['REQUEST_URI'] != strtolower($url[0]) . "?" . $url[1]) && $notBitrix === false && $notAjax === false) {
            header('Location: //' . $_SERVER['HTTP_HOST'] . strtolower($url[0]) . "?" . $url[1], true, 301);
            exit();
        }
    } else {
        if ($_SERVER['REQUEST_URI'] != strtolower($_SERVER['REQUEST_URI']) && $notBitrix === false && $notAjax === false) {
            header('Location: //' . $_SERVER['HTTP_HOST'] . strtolower($_SERVER['REQUEST_URI']), true, 301);
            exit();
        }
    }
}

AddEventHandler("sale", "OnSaleOrderSaved", "orderSavedHook");

function orderSavedHook($e, $v) {
	$basketData = [];
	$order_data = [];   
	
	$order_id = $e->getId();
	$order = \Bitrix\Sale\Order::load($order_id);

    $order = \Bitrix\Sale\Order::load($order_id);


    $basket = $e->getBasket();
    $props = $e->getPropertyCollection()->getArray();
	//$delivery = $order->getFieldValues();
	$comment = $e->getField("USER_DESCRIPTION");
	$rsUser = CUser::GetByID($e->getUserId());
    $arUser = $rsUser->Fetch();
   
   if ($arUser['LID'] == "ff") {
	   $artFieldName = "ARTNUMBER";
   } else {
	   $artFieldName = "ARTICLE";
   }
   
   $order_data = [
      'order_id' => $order_id,
	  'state' => $order->getField('STATUS_ID'),
	  'site_id' => $order->getSiteId(),
      'user' => $arUser,
      'comment' => $comment,
      'locations' => $e->getDeliveryLocation(),
	  'phone' => $arUser['PERSONAL_PHONE'],
      'name' => $arUser['NAME']." ".$arUser['LAST_NAME'],
      'props' => $props,
      'payments' => $e->getPaySystemIdList(),
      'shipments' => $e->getDeliveryIdList()
   ];

    $order_data['shipment_cost'] = $order->getDeliveryPrice();

	foreach ($basket as $basketItem) {
        $itemProps = CCatalogProduct::GetByIDEx($basketItem->getProductId());
		$basketData[] = [
			'id' => $basketItem->getProductId(),
			'name' => $basketItem->getField('NAME'),
			'page' => $basketItem->getField('DETAIL_PAGE_URL'),
            'price' => $basketItem->getPrice(),
			'qnt' => $basketItem->getQuantity(),
			'total' => $basketItem->getFinalPrice(),
            'prices' => $itemProps['PRICES'],
            'XML_ID' => $itemProps['XML_ID'],
        ];
	};
   
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"https://evr.shtychki.by/bus_hook.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "key"=>"bzs21",
		'o' => json_encode([
			'id'=> $order_id, 
			'b'=> $basketData, 
			'o'=> $order_data,
			'trace'=> $_COOKIE['b__trace']
        ], JSON_UNESCAPED_UNICODE)
	]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	curl_close ($ch);

	return;
}