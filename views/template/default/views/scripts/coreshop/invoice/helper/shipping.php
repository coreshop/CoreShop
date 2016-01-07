<div class="row">
    <div class="col-xs-12">
        <strong><?=$this->translate("Carrier")?></strong>
    </div>
    <div class="col-xs-12">
        <?php echo $this->order->getCarrier()->getName() ?>
    </div>
</div>