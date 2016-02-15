<div class="container shop checkout checkout-step-4">
    
    <?=$this->partial("coreshop/helper/order-steps.php", array("step" => 4));?>

    <?php if($this->message) {
        ?>
        <div class="alert alert-danger">
            <?=$this->translate($this->message)?>
        </div>
        <?php
    } ?>

    <form action="<?=$this->url(array("action" => "shipping", "lang" => $this->language), "coreshop_checkout", true)?>" method="post">
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->translate("Shipping")?></h3>
            </div>
            <div class="panel-body delivery-options">
                <table class="table table-unstyled delivery-option">
                    <?php $i = 0; ?>
                    <?php foreach($this->carriers as $carrier) { ?>
                            <tr>

                                <td class="delivery-option-radio">
                                    <input class="delivery_option_radio" type="radio" name="carrier" value="<?=$carrier->getId()?>"  <?php echo $i === 0 ? 'checked="checked"' : ''?>>
                                </td>
                                <td class="delivery-option-image col-xs-3">
                                    <?php if($carrier->getImage() instanceof \Pimcore\Model\Asset\Image) {
                                        echo $carrier->getImage()->getThumbnail("coreshop_carrier")->getHtml(array("class" => "img-responsive"));
                                    } ?>
                                </td>
                                <td class="delivery-option-text">
                                    <strong><?=$carrier->getName()?></strong> <?=$carrier->getLabel()?>
                                </td>
                                <td class="delivery-option-price">
                                    <?=\CoreShop\Tool::formatPrice($this->cart->getShippingCostsForCarrier($carrier))?>
                                </td>
                            </tr>
                        <?php $i++; ?>
                    <?php } ?>
                </table>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input name="termsAndConditions" type="checkbox"> <?=$this->translate("I'v read and agreed on ")?> <a id="openTerms" data-toggle="modal" data-target="#termsAndConditions"><?=$this->translate("Terms & Conditions")?></a>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <a href="<?=$this->url(array("lang" => $this->language, "action" => "address"), "coreshop_checkout", true)?>" class="btn btn-default pull-left">
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

<div class="modal fade" id="termsAndConditions" tabindex="-1" role="dialog" aria-labelledby="termsAndConditionLabel">
    <div class="modal-dialog" role="document" style="z-index:1040;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="termsAndConditionLabel"><?=$this->translate("Terms and Conditions")?></h4>
            </div>
            <div class="modal-body">
                <?=$this->inc("/" . $this->language . "/shop/snippet/terms")?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>