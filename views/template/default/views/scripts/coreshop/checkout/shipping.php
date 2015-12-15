<div class="container shop checkout checkout-step-4">
    
    <?=$this->partial("coreshop/helper/order-steps.php", array("step" => 4));?>

    <form action="<?=$this->url(array("action" => "shipping", "lang" => $this->language), "coreshop_checkout")?>" method="post">
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->translate("Shipping")?></h3>
            </div>
            <div class="panel-body delivery-options">
                <table class="table table-unstyled delivery-option">
                <?php foreach($this->carriers as $carrier) { ?>
                        <tr>

                            <td class="delivery-option-radio">
                                <input class="delivery_option_radio" type="radio" name="carrier" value="<?=$carrier->getId()?>">
                            </td>
                            <td class="delivery-option-image col-xs-3">
                                <?php if($carrier->getImage()) { ?>
                                    <img src="<?=$carrier->getImage()?>" class="img-responsive" alt="<?=$carrier->getName()?>">
                                <?php } ?>
                            </td>
                            <td class="delivery-option-text">
                                <strong><?=$carrier->getName()?></strong> <?=$carrier->getLabel()?>
                            </td>
                            <td class="delivery-option-price">
                                <?=\CoreShop\Tool::formatPrice($carrier->getDeliveryPrice($this->cart))?>
                            </td>
                        </tr>

                <?php } ?>
                </table>

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