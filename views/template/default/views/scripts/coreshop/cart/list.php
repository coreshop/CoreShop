<div id="main-container" class="container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
        <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "action" => "list"), "coreshop_cart")?>"><?=$this->translate("Shopping Cart")?></a></li>
    </ol>

    <?=$this->partial("coreshop/helper/order-steps.php", array("step" => 1));?>

    <!-- Breadcrumb Ends -->
    <!-- Main Heading Starts -->
    <h2 class="main-heading text-center">
        <?=$this->translate("Shopping Cart")?>
    </h2>
    <!-- Main Heading Ends -->
    <!-- Shopping Cart Table Starts -->
    <?php if(count($this->cart->getItems()) > 0) {
        echo $this->template("coreshop/cart/helper/cart.php", array("edit" => true));
    } else { ?>
        <p><?=$this->translate("Your Shopping Cart is empty")?></p>
    <?php } ?>

    <div class="row">
        <div class="col-xs-12">
            <a href="<?=$this->url(array("lang" => $this->language), "coreshop_index")?>" class="btn btn-default pull-left">
                <span class="hidden-xs">Continue Shopping</span>
                <span class="visible-xs">Continue</span>
            </a>
            <a href="<?=$this->url(array("lang" => $this->language, "action" => "index"), "coreshop_checkout")?>" class="btn btn-default pull-right">
                Checkout
            </a>
        </div>
    </div>

</div>