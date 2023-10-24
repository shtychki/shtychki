<?
if(!$_SERVER["DOCUMENT_ROOT"])
    $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/ext_www/shtychki.by';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");

$blk_id = 119;

$res = CIblockElement::GetList([], ["IBLOCK_ID" => $blk_id], false, false, ["ID", "IBLOCK_ID", "DETAIL_TEXT_TYPE"]);
while ($ob = $res->GetNext()) {
    //$PROP_ELEMENT_ADD = [
        //"DETAIL_TEXT_TYPE" => 'html',
		//"DETAIL_TEXT_TYPE" => "html",
		//"DETAIL_TEXT" => html_entity_decode($ob["DETAIL_TEXT"]),
    //];
	echo '<pre>';
	print_r($ob);
	echo '</pre>';
	//CIblockElement::SetPropertyValuesEx($ob["ID"], $blk_id, $PROP_ELEMENT_ADD);
	
	/*
	if ($ob["DETAIL_TEXT_TYPE"] !== "html") {
		$el = new CIBlockElement;
		$arLoadProductArray = Array(
			"DETAIL_TEXT_TYPE" => "html",
		);
		$up_el = $el->Update($ob["ID"], $arLoadProductArray);
	}
	*/
}