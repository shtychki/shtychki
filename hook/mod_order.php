<?php
    ini_set("log_errors", 1);
    ini_set("error_log", "err_log_171.log");

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

	$key = $_GET['key'];
	if ("qwe123" != $key) die;
	
    $order_id = $_GET['id'];
    $new_state = $_GET['state'];
    
    if (CModule::IncludeModule('sale'))
    {
        CSaleOrder::StatusOrder($order_id, $new_state);
    }

    echo "OK";