<?
if(!$_SERVER["DOCUMENT_ROOT"])
    $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/ext_www/shtychki.by';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");

$blk_id = 119;

$res = CIblockElement::GetList([], ["IBLOCK_ID" => $blk_id], false, false, ["ID", "IBLOCK_ID", "PROPERTY_ASKARON_MOYSKLAD_CODE"]);
while ($ob = $res->GetNext()) {
    $PROP_ELEMENT_ADD = [
        "CML2_ARTICLE" => $ob['PROPERTY_ASKARON_MOYSKLAD_CODE_VALUE'],
    ];
	CIblockElement::SetPropertyValuesEx($ob["ID"], $blk_id, $PROP_ELEMENT_ADD);
}