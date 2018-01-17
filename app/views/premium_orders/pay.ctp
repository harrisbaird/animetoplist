<?php
$myPrivateKey = APP . 'vendors/certs/my.paypal.pem';
$myPublicCert = APP . 'vendors/certs/my.paypal.pub.pem';
$paypalPublicCert = APP . 'vendors/certs/paypal.pub.pem';

$this->PaypalEwp->setTempFileDirectory('/tmp');
$this->PaypalEwp->setCertificate($myPublicCert, $myPrivateKey);
$this->PaypalEwp->setCertificateID('R2LNFYGXMCSNY');
$this->PaypalEwp->setPayPalCertificate($paypalPublicCert);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title></title>
</head>
<body>
<?php
	$paypalData = array(
		'cmd' => '_xclick',
		'business'=> Configure::read('App.premium.email'),
		'notify_url' => Router::url(array('plugin' => 'paypal_ipn', 'controller' => 'instant_payment_notifications', 'action' => 'process'), true),
		'return' => Router::url(array('controller' => 'premium_orders', 'action' => 'complete'), true),
		'cancel_return' => Router::url(array('controller' => 'users', 'action' => 'profile'), true),
		'item_name' => 'Premium membership',
		'item_number' => $data['PremiumOrder']['id'],
		'amount' => $data['Order']['total'],
		'currency_code' => 'USD',
		'no_note' => '1',
		'no_shipping' => '1'
	);


	$encrypted = $this->PaypalEwp->getEncryptedString($paypalData);
	
?>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="form">
<input type="hidden" name="cmd" value="_xclick">
<?php foreach($paypalData as $key=>$value): ?>
<input type="hidden" name="<?php echo $key?>" value="<?php echo $value; ?>">
<?php endforeach; ?>
</form>

<?php $this->Html->scriptStart(array('safe' => false)); ?>
document.form.submit();
<?php echo $this->Html->scriptEnd(); ?>
</body>
</html>
