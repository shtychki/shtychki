<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Ошибка оплаты");
?>
<p class="center">
	<img alt="depositphotos_173222848-stock-photo-cancel-icon-cyan-blue-square.jpg" src="/upload/medialibrary/928/928e86a3812ff55d088c94c6d723dde4.jpg" title="depositphotos_173222848-stock-photo-cancel-icon-cyan-blue-square.jpg">
</p>
<p class="center">
	Ошибка при оплате счета <?=htmlspecialcharsbx($_REQUEST['wsb_order_num'])?>
	К сожалению, платёж не был произведен. Деньги не были переведены.
</p>
<div class="center">
<span><a class="btn btn-default btn-wsb" href="/">Вернуться на Главную</a></span>
<span><a class="btn btn-default btn-wsb" href="/order/payment/?ORDER_ID=<?=htmlspecialcharsbx($_REQUEST['wsb_order_num'])?>&PAYMENT_ID=<?=htmlspecialcharsbx($_REQUEST['wsb_order_num'])?>/1">Повторить платёж</a></span>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>