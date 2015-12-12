<div class="container shop checkout checkout-step-5">
    
    <?=$this->partial("coreshop/helper/order-steps.php", array("step" => 5));?>
    
    <div class="row">
        <div class="col-xs-12 col-sm-10 col-sm-push-1 payment-options">
            <p><?=$this->translate("Thank you for your order. Your Order ID is:") ?> <?=$this->order->getId()?>
        </div>
    </div>

</div>