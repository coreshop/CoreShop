<!-- Latest Products Starts -->
<section class="product-carousel">
<!-- Heading Starts -->
    <h2 class="product-head"><?=$this->translate("Latest Products")?></h2>
<!-- Heading Ends -->
<!-- Products Row Starts -->
    <?php
        $products = CoreShop\Model\Product::getLatest();
    ?>
    <div class="row">
        <div class="col-xs-12">
        <!-- Product Carousel Starts -->
            <div id="owl-product" class="owl-carousel">
            <!-- Product #1 Starts -->
                <?php foreach($products as $product) { ?>
                <div class="item">
                    <?=$this->template("coreshop/product/helper/product-preview.php", array("product" => $product))?>
                </div>
                <?php } ?>
            </div>
        <!-- Product Carousel Ends -->
        </div>
    </div>
<!-- Products Row Ends -->
</section>
<!-- Latest Products Ends -->