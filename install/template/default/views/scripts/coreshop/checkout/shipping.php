<div class="container shop checkout checkout-step-4">
    
    <?=$this->partial("coreshop/helper/order-steps.php", array("step" => 4));?>

    <form action="<?=$this->url(array("action" => "shipping", "lang" => $this->language), "coreshop_checkout")?>" method="post">
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->translate("Shipping")?></h3>
            </div>
            <div class="panel-body delivery-options">
                <?php foreach($this->provider as $provider) { ?>

                    <table class="table table-unstyled delivery-option">

                        <tr>

                            <td class="delivery-option-radio">
                                <input class="delivery_option_radio" type="radio" name="delivery_provider[<?=$provider->getIdentifier()?>]" checked="checked">
                            </td>
                            <td class="delivery-option-image col-xs-3">
                                <?php if($provider->getImage()) { ?>
                                    <img src="<?=$provider->getImage()?>" class="img-responsive" alt="<?=$provider->getName()?>">
                                <?php } ?>
                            </td>
                            <td class="delivery-option-text">
                                <strong><?=$provider->getName()?></strong> <?=$provider->getDescription()?>
                            </td>
                            <td class="delivery-option-price">
                                <?=\CoreShop\Tool::formatPrice($provider->getShipping($this->cart))?>
                            </td>
                        </tr>

                    </table>

                <?php } ?>

                <div class="row">
                    <div class="col-xs-12">
                        <a href="<?=$this->url(array("lang" => $this->language, "action" => "address"), "coreshop_checkout")?>" class="btn btn-default pull-left">
                            <?=$this->translate("Back")?>
                        </a>

                        <button type="submit" class="btn btn-white btn-borderd pull-right">
                            <?=$this->translate("Proceed to checkout")?>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </form>
</div>