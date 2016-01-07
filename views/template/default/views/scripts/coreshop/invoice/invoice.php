<?php

$this->billingAddress = $this->order->getCustomerBillingAddress();
$this->shippingAddress = $this->order->getCustomerShippingAddress();
$this->billingAndShippingEqual = $this->order->isShippingAndBillingAddressEqual();

if(!$this->billingAddress || !$this->shippingAddress) {
    die("no shipping or billing address definied");
}


?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
        <![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Invoice <?=$this->order->getOrderNumber()?></title>

        <!-- Google Web Fonts -->
        <link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:300italic,400italic,700italic,400,300,700" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Oswald:400,700,300" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400" rel="stylesheet" type="text/css">

        <!-- CSS Files -->
        <link href="<?=CORESHOP_TEMPLATE_RESOURCES?>css/owl.carousel.css" rel="stylesheet">
        <link href="<?=CORESHOP_TEMPLATE_RESOURCES?>css/invoice.css" rel="stylesheet">
    </head>
    <body class="lang-<?=$this->language?>">
        <h1><?=$this->translate("Invoice")?> #<?=$this->order->getOrderNumber()?></h1>
        <?php $this->template("helper/address.php")?>
        <?php $this->template("helper/items.php")?>

        <div class="row">
            <div class="col-xs-4">
                <?php $this->template("helper/shipping.php")?>
            </div>
            <div class="col-xs-4">
                <?php $this->template("helper/payment.php")?>
            </div>
        </div>
    </body>
</html>