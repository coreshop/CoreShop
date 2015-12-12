<table class="columns twelve">
    <thead>
    <tr>
        <th><?=$this->translate("Produkt")?></th>
        <th><?=$this->translate("Menge")?></th>
        <th class="text-center"><?=$this->translate("Preis")?></th>
        <th class="text-center"><?=$this->translate("Gesamt")?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($this->order->getItems() as $item) { ?>

        <?
        $href = $this->url(array("lang" => $this->language, "product" => $item->getProduct()->getId(), "name" => $item->getProduct()->getName()), "coreshop_detail");
        ?>
        <tr class="cart-item cart-item-<?=$item->getId()?>">
            <td class="col-sm-9">
                <a href="<?=$href?>"><?=$item->getProduct()->getName()?></a>
            </td>
            <td class="col-sm-1 cart-item-amount" style="text-align: center">
                <span class=""><?=$item->getAmount()?></span>
            </td>
            <td class="col-sm-1 text-center cart-item-price">
                <strong><?=\CoreShop\Tool::formatPrice($item->getProduct()->getProductPrice())?></strong>
            </td>
            <td class="col-sm-1 text-right cart-item-total-price">
                <strong><?=\CoreShop\Tool::formatPrice($item->getAmount() * $item->getProduct()->getProductPrice())?></strong>
            </td>
        </tr>
        <tr>
            <td>
                <?php foreach($item->getExtraInformation() as $extraInformation) {
                    if($extraInformation instanceof \CoreShop\Objectbrick\Data\CartItem) {
                        echo $extraInformation->render();
                    }
                } ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php } ?>
    <?/* <tr>
        <td>   </td>
        <td>   </td>
        <td><h5><?=$this->translate("Zwischensumme")?></h5></td>
        <td class="text-right"><h5><strong class="cart-sub-total"><?=\CoreShop\Tool::formatPrice($this->order->getSubtotal())?></strong></h5></td>
    </tr>*/?>
    <?php
    $shipping = $this->order->getShipping();
    ?>
    <?php if($shipping > 0) { ?>
        <tr>
            <td>   </td>
            <td>   </td>
            <td><?=$this->translate("Versandkosten") ?></td>
            <td class="text-right"><strong><?=\CoreShop\Tool::formatPrice($shipping)?></strong></td>
        </tr>
    <?php } ?>
    <tr>
        <td>   </td>
        <td>   </td>
        <td><?=$this->translate("Gesamt")?></td>
        <td class="text-right"><strong class="cart-total-price"><?=\CoreShop\Tool::formatPrice($this->order->getTotal())?></strong></td>
    </tr>
    </tbody>
</table>