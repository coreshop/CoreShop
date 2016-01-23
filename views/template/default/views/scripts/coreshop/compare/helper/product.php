<?php
    $href = $this->url(array("name" => $this->product->getName(), "product" => $this->product->getId(), "lang" => $this->language), "coreshop_detail");
    $uniqid = uniqid() . "-product-image-" . $this->product->getId();
?>
<div class="product-col">

    <div class="image">
        <?php if($this->product->getImage() instanceof \Pimcore\Model\Asset\Image) { ?>
            <?php if($this->product->getIsNew()) { ?>
                <div class="image-new-badge"></div>
            <?php } ?>

            <a href="<?=$href?>">
                <?php
                    echo $this->product->getImage()->getThumbnail("coreshop_productList")->getHtml(array("class" => "img-responsive", "alt" => $this->product->getName(), "id" => $uniqid));
                ?>
            </a>

            <hr/>
        <?php } ?>
    </div>
    <div class="caption">
        <h4><a href="<?=$href?>"><?=$this->product->getName()?></a></h4>
        <div class="description">
            <?=$this->product->getShortDescription()?>
        </div>

        <br>
        <table class="table">
            <?php if($this->product->getAvailableForOrder()) { ?>
                <tr>
                    <td>Price</td>
                    <td align="right"><?=\CoreShop\Tool::formatPrice($this->product->getPrice())?></td>
                </tr>
            <?php } ?>

            <tr>
                <td>Depth</td>
                <td align="right"><?=$this->product->getDepth();?> cm</td>
            </tr>
            <tr>
                <td>Width</td>
                <td align="right"><?=$this->product->getWidth();?> cm</td>
            </tr>
            <tr>
                <td>Height</td>
                <td align="right"><?=$this->product->getHeight();?> cm</td>
            </tr>

            <tr>
                <td>Custom Val 1</td>
                <td align="right">--</td>
            </tr>
            <tr>
                <td>Custom Val 2</td>
                <td align="right">--</td>
            </tr>
        </table>

    </div>

    <button type="button" title="<?=$this->translate("remove from compare list")?>" class="btn btn-compare-remove" data-id="<?=$this->product->getId()?>" data-original-title="Remove from List">
        <i class="fa fa-times"></i> <?=$this->translate("remove from compare list")?>
    </button>

</div>