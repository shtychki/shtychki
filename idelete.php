<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
set_time_limit(0);
ini_set('max_execution_time', '3600');
ini_set('memory_limit', '512M');
$c_elem = 0;
$c_sect = 0;
CModule::IncludeModule('iblock');
CModule::IncludeModule('blog');
CModule::IncludeModule('user');
global $USER;
if (!$USER->IsAdmin()){
exit;
}
$del_action = $_REQUEST["del"];
$IBLOCK_ID = intval($_REQUEST["iblock_id"]);
echo '<form name="del" action="">';
if(empty($del_action) && empty($result)) {
$res = CIBlock::GetList(
    Array(), 
    Array(
        // 'TYPE'=>'catalog', 
        'SITE_ID'=>SITE_ID, 
        'ACTIVE'=>'Y', 
        "CNT_ACTIVE"=>"Y", 
    ), true
);
echo '<input name="del" value="Y" type="hidden"><select name="iblock_id">';
while($ar_res = $res->Fetch())
{
    echo '<option value="'.$ar_res['ID'].'">['.$ar_res['ID'].'] '.$ar_res['NAME'].'</option>';
}
echo '</select>';
echo '<br><input name="submit" type="submit" value="очистить инфоблок">';
}
if(!empty($del_action) && empty($result)) {
    // $IBLOCK_ID = $del_action;
    $arFilterSect = Array(
        "IBLOCK_ID"=>$IBLOCK_ID,
        // "ID"=>88421,//N
        // "ACTIVE" => "Y",
    );
    $res = CIBlockElement::GetList(Array("ID"=>"ASC"), $arFilterSect, $filterFields);
    while ($ar_fields = $res->GetNext()) {//del elements / удаление элементов
        // для красоты можно добавить транзакции ($DB)
        // $DB->StartTransaction();
        if(!CIBlockElement::Delete($ar_fields["ID"]))
        {
            $strWarning .= 'Error!';
            // $DB->Rollback();
        }
        // else
            // $DB->Commit();
        // echo $ar_fields["NAME"];exit;
        $c_elem ++;
    }
    $arFilter = Array('IBLOCK_ID'=>$IBLOCK_ID);
    $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
    // $db_list->NavStart(20);
    // echo $db_list->NavPrint($arIBTYPE["SECTION_NAME"]);
    while($ar_result = $db_list->GetNext())
    {// del sections / удаление разделов
        // echo $ar_result["NAME"];
        // $DB->StartTransaction();
        if(!CIBlockSection::Delete($ar_result["ID"]))
        {
            $strWarning .= 'Error.';
            // $DB->Rollback();
        }
        // else
            // $DB->Commit();
        // echo $ar_result["NAME"];exit;
        $c_sect ++;
    }
    echo 'Удалено элементов: '.$c_elem.', секций: '.$c_sect;
}
if(empty($del_action) && !empty($result)) {
echo 'result';
}
echo '</form>';?>