<div id="main-container" class="container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index")?>"><?=$this->translate("Home")?></a></li>
        <li><a href="<?=$this->url(array("lang" => $this->langauge, "action" => "index"), "coreshop_checkout")?>"><?=$this->translate("Checkout")?></a></li>
        <li class="active"><?=$this->translate("Error")?></li>
    </ol>
    <!-- Breadcrumb Ends -->
    <!-- Main Heading Starts -->
    <h2 class="main-heading text-center">
        <?=$this->translate("Error")?>
    </h2>
    <!-- Main Heading Ends -->
    <!-- Content Starts -->
    <div class="content-box text-center">
        <h4 class="special-heading"><?=$this->translate("oops !")?></h4>
        <h5>
            <?=$this->translate("We are sooo sorry, something went wrong...")?>
        </h5>
        <br>
        <p>
            <a href="<?=$this->url(array(), "coreshop_index")?>" class="btn btn-black text-uppercase"><?=$this->translate("Back to Home")?></a>
        </p>
    </div>
    <!-- Content Ends -->
</div>