<div class="row">
    <div class="col-xs-12">
        <strong><?=$this->translate("Payment")?></strong>
    </div>
    <div class="col-xs-12">
        <?php echo $this->order->getPaymentProvider(); ?>
    </div>
</div>