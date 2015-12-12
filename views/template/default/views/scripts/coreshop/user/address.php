<div id="main-container" class="container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
        <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "action" => "profile"), "coreshop_user")?>"><?=$this->translate("My Profile")?></a></li>
        <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "action" => "addresses"), "coreshop_user")?>"><?=$this->translate("Adresses")?></a></li>
        <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "action" => "address"), "coreshop_user")?>"><?=$this->translate("Add a new address")?></a></li>
    </ol>

    <!-- Breadcrumb Ends -->
    <!-- Main Heading Starts -->
    <h2 class="main-heading text-center">
        <?=$this->translate("Address")?> <br />
        <span><?=$this->translate("Add a new address")?></span>
    </h2>
    <!-- Main Heading Ends -->
    <!-- Registration Section Starts -->
    <section class="registration-area">
        <div class="row">
            <div class="col-sm-12">
                <!-- Registration Block Starts -->
                <div class="panel panel-smart">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=$this->translate("Personal Information")?></h3>
                    </div>
                    <div class="panel-body">
                        <!-- Registration Form Starts -->

                        <form role="form" action="<?=$this->url(array("lang" => $this->language, "action" => "address"), "coreshop_user")?>" class="form-horizontal" role="form" id="shop-register-form" method="post">
        
                            <?php if($this->redirect) { ?>
                            <input type="hidden" name="_redirect" value="<?=$this->redirect?>" />
                            <?php } ?>
        
                            <?=$this->template("coreshop/user/helper/address.php")?>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-black">
                                        <?php
                                        if($this->isNew) {
                                            echo $this->translate("Add");
                                        } else {
                                            echo $this->translate("Save");
                                        }

                                        ?>
                                    </button>
                                </div>
                            </div>
                            <!-- Password Area Ends -->
                        </form>
                        <!-- Registration Form Starts -->
                    </div>
                </div>
                <!-- Registration Block Ends -->
            </div>
        </div>
    </section>
    <!-- Registration Section Ends -->
</div>
<!-- Main Container Ends -->