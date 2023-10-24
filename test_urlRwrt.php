<style>
    *{
        font-weight: bold;
        font-family: monospace;
        font-size: 1.1em;
    }
    b{
        margin: 4px;
        display: inline-block;
        width: 99px;
        border-bottom: 1px dotted;
        color: lightseagreen;
        user-select: none;
    }
</style>
<?php
include_once 'urlrewrite.php';

//$url = '/diski/type-stalnye/page-2/';
$url = '/catalog/chekhly-dlya-telefonov/';
//$url = '/diski/disla/';



foreach ($arUrlRewrite as $k => $v) {
    preg_match($v['CONDITION'], $url, $out);

    if (count($out)) {
        print_r('<b>\'KEY\'</b> ' . $k . '<br> <b>\'URI\'</b> ' . $url . '<br> <b>\'УСЛОВИЕ\'</b> ' . $v['CONDITION'] . '<br><b>\'ПРАВИЛО\'</b> ' . $v['RULE'] . '<br><b>\'ID\'</b> ' . $v['ID'] . '<br><b>\'ПУТЬ\'</b> ' . $v['PATH']);
        break;
    }
}




//// проверка ключа массива
//$coun = 0;
//for($i = 6500; $i<6983; $i++){
//    if(array_key_exists($i, $arUrlRewrite)){
//        $coun++;
//    }
//}
//echo $coun;

