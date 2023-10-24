<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата заказа");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/custom_pay.css');
// use \Bitrix\Sale;
// $order_id = $_REQUEST['ORDER_ID'];
// if (!empty($order_id)){
	// $orderObj = Sale\Order::load($_REQUEST['ORDER_ID']);
	// $paymentCollection = $orderObj->getPaymentCollection();
	// $payment = $paymentCollection[0];
	// $service = Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
	// $context = \Bitrix\Main\Application::getInstance()->getContext();
	// $service->initiatePay($payment, $context->getRequest());
// } else {
	// echo "Не известный заказ";
// }

?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.payment",
	"",
	Array(
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>