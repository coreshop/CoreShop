<!-- Main Container Starts -->
<div id="main-container-home" class="container">
    <div class="row">
        <?=$this->template("coreshop/helper/left.php")?>
        <!-- Primary Content Starts -->
        <div class="col-md-9">



            <?php echo $this->coreshopareablock("content"); ?>

            <?=$this->template("coreshop/product/helper/latest-products.php");?>

            <!-- Specials Products Starts -->
            <section class="products-list">
                <!-- Heading Starts -->
                <h2 class="product-head"><?=$this->translate("Specials Products")?></h2>
                <!-- Heading Ends -->
                <!-- Products Row Starts -->
                <div class="row">
                    <!-- Product #1 Starts -->
                    <div class="col-md-4 col-sm-6">
                        <?=$this->product("special-1")?>
                    </div>
                    <!-- Product #1 Ends -->
                    <!-- Product #2 Starts -->
                    <div class="col-md-4 col-sm-6">
                        <?=$this->product("special-2")?>
                    </div>
                    <!-- Product #2 Ends -->
                    <!-- Product #3 Starts -->
                    <div class="col-md-4 col-sm-6">
                        <?=$this->product("special-3")?>
                    </div>
                    <!-- Product #3 Ends -->
                </div>
                <!-- Products Row Ends -->
            </section>
            <!-- Specials Products Ends -->
        </div>
        <!-- Primary Content Ends -->
    </div>
</div>
<!-- Main Container Ends -->