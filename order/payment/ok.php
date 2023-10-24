<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Успешная оплата");
CModule::IncludeModule("sale");
$arOrder = CSaleOrder::GetByID(htmlspecialcharsbx($_REQUEST['wsb_order_num']));
$res = CSaleBasket::GetList(array(), array("ORDER_ID" => $_REQUEST['wsb_order_num']));
$products=array();
while ($arItem = $res->Fetch()) {
$products[] = array(
	'name' => $arItem['NAME'],
	'id' => $arItem['PRODUCT_ID'],
	'price' => $arItem['PRICE'],
	'quantity' => $arItem['QUANTITY']
);
}
?>
<div style="text-align: center;">
<img alt="Screenshot_6.png" src="/upload/medialibrary/332/33269d47fb193bb014acd1335358108e.png" title="Screenshot_6.png"><br>
</div>
<p style="text-align: center;"></p>
<? if ($_REQUEST['wsb_order_num']) { ?>
<div style="text-align: center;">Платёж <?=htmlspecialcharsbx($_REQUEST['wsb_order_num'])?> на сумму <?=$arOrder['PRICE'].' р.';?> успешно обработан</div>
<? } ?>
<div class="order-items center">
<p>Состав заказа:</p>
<? foreach ($products as $product) { ?>
<p><?=$product['quantity'] . ' x ' . $product['name'] . ' - ' . number_format($product['price'], 0) . ' р.'; ?></p>
<? } ?>
</div>
<div class="center" style="text-align: center;">
<span><a class="btn btn-default btn-wsb" href="/">Вернуться на Главную</a></span>
<span><a class="btn btn-default btn-wsb" href="/personal/orders/">Мои заказы</a></span>
</div>
<!--<p style="text-align: center;">Нажмите <a href="https://shtychki.by/">меня</a>&nbsp;что-бы перейти обратно на сайт.</p>-->
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>