<?php
if(!$_SERVER["DOCUMENT_ROOT"])
    $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/ext_www/shtychki.by';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

$iblock_id = 119;

//$resEl = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $iblock_id, "=PROPERTY_ID" => false), false, array(), array("ID", "NAME", "PROPERTY_ID"));
$resEl = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $iblock_id), false, array(), array("ID", "NAME", "PROPERTY_ID"));
$i = 0;
$j = 0;
while ($obEl = $resEl->GetNext()) {
	if (stristr($obEl["NAME"], ' (', true) != '') {
		$name = stristr($obEl["NAME"], ' (', true);
		$arrEl = CIBlockElement::GetList(array("ID"=>"ASC"), array("IBLOCK_ID" => $iblock_id, "?NAME" => $name), false, array(), array("ID", "NAME", "PROPERTY_ID"));
		$arrId = array();
		$arrIdCheck = array();
		while ($el = $arrEl->GetNext()) {
			if ($name == stristr($el["NAME"], ' (', true)) {
				$arrId[] = $el["ID"];
				//if ($el["PROPERTY_ID_VALUE"] == '') {
					\CIBlockElement::SetPropertyValuesEx($el["ID"], $iblock_id, ["ID" => $arrId[0]]);
//					$fd = fopen($_SERVER["DOCUMENT_ROOT"] . "/local/cron/property_id.txt","a");
//					fwrite($fd, "Обращение к файлу - ".date("d.m.Y H:i")." - ".$el["ID"].' - '.$el["NAME"].' '.$arrId[0]."\r\n");
//					fclose($fd);
					$j++;
				//}
			}
		}
	}
	$i++;
}
echo ("Обработано позиций: " . $j);