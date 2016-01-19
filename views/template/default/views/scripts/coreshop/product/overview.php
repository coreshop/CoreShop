<div class="container">
    <div class="row">
        <div class="row-same-height row-full-height">
            
            <div class="col-xs-12 col-sm-4 col-sm-height col-full-height col-top">
                <div class="col-inner background-sales">
                    <div class="col-content about">
                        <?=$this->template("helper/area.php", array("excludeContainer" => true))?>
                    </div>
                </div>
            </div>
            
            <div class="col-xs-12 col-sm-8 col-sm-height col-full-height col-top">
                <?php 
                
                    if ($this->document->getProperty("videoHeader") instanceof Asset_Video && $this->document->getProperty("header") instanceof Asset_Image) {
                        echo $this->template("helper/header/video.php");
                    } elseif ($this->document->getProperty("header") instanceof Asset_Image) {
                        echo $this->template("helper/header/image.php");
                    } 
                ?>
            </div>
            
        </div>
    </div>
</div>



<div class="container">
    <?php
        $products = CoreShop_Product::getAll();
        
        $products = array_chunk($products, 4);
        
        foreach($products as $row) {
            ?>
            <div class="row row-product">
                <?php
                foreach($row as $product) {
                    echo $this->template("coreshop/product/helper/product-preview.php", array("product" => $product));
                }
                ?>
            </div>
            <?php
        } 
    ?>
</div>