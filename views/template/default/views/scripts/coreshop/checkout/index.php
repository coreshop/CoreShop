<div id="main-container" class="container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
        <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "action" => "index"), "coreshop_checkout", true)?>"><?=$this->translate("Login")?></a></li>
    </ol>

    <?=$this->partial("coreshop/helper/order-steps.php", array("step" => 2));?>

    <!-- Breadcrumb Ends -->
    <!-- Main Heading Starts -->
    <h2 class="main-heading text-center">
        <?=$this->translate("Login or create new account")?>
    </h2>
    <!-- Main Heading Ends -->
    <!-- Login Form Section Starts -->
    <section class="login-area">
        <div class="row">
            <div class="col-xs-12">
                <?php if($this->message) { ?>
                    <div class="alert alert-danger">
                        <?=$this->message?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <!-- Login Panel Starts -->
                <div class="panel panel-smart">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=$this->translate("Login")?></h3>
                    </div>
                    <div class="panel-body">
                        <p>
                            <?=$this->translate("Please login using your existing account")?>
                        </p>
                        <!-- Login Form Starts -->
                        <form class="form-inline" role="form" method="post" action="<?=$this->url(array("lang" => $this->language, "action" => "login", true), "coreshop_user")?>">
                            <input type="hidden" name="_redirect" value="<?=$this->url(array("lang" =>  $this->language, "action" => "address"), "coreshop_checkout")?>" />
                            <input type="hidden" name="_base" value="<?=$this->url(array("lang" =>  $this->language, "action" => "index"), "coreshop_checkout")?>" />

                            <div class="form-group">
                                <label class="sr-only" for="email"><?=$this->translate("Email")?></label>
                                <input type="text" class="form-control" name="email" id="email" placeholder="<?=$this->translate("Email")?>">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="password"><?=$this->translate("Password")?></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="<?=$this->translate("Password")?>">
                            </div>
                            <button type="submit" class="btn btn-black">
                                Login
                            </button>
                        </form>
                        <!-- Login Form Ends -->
                    </div>
                </div>
                <!-- Login Panel Ends -->
            </div>
            <div class="col-sm-6">
                <!-- Account Panel Starts -->
                <div class="panel panel-smart">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <?=$this->translate("Create new account")?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <p>
                            <?=$this->translate("Registration allows you to avoid filling in billing and shipping forms every time you checkout on this website")?>
                        </p>
                        <a href="<?=$this->url(array("lang" => $this->language, "action" => "register"), "coreshop_checkout", true)?>" class="btn btn-black">
                            <?=$this->translate("Register")?>
                        </a>
                    </div>
                </div>
                <!-- Account Panel Ends -->
            </div>
        </div>
    </section>
    <?php if(\CoreShop\Config::isGuestCheckoutActivated()) { ?>
        <!-- Login Form Section Ends -->
        <?php
        $class = Pimcore\Model\Object\ClassDefinition::getByName("CoreShopUser");

        $postValue = function ($name) {
            if (isset($_POST[$name])) {
                return $_POST[$name];
            }

            return null;
        };
        ?>

        <h2 class="main-heading text-center">
            <?=$this->translate("Checkout as Guest")?>
        </h2>
        <section class="guest-area">
            <?php if($this->error) { ?>
            <div class="alert alert-danger">
                <?=$this->error?>
            </div>
            <?php } ?>

            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-smart">
                        <div class="panel-heading hidden-xs">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h3 class="panel-title"><?=$this->translate("Personal Information")?></h3>
                                </div>
                                <div class="col-sm-6">
                                    <h3 class="panel-title"><?=$this->translate("Address")?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="shop-register-form" action="<?=$this->url(array("lang" => $this->language, "action" => "register"), "coreshop_user", true)?>" method="post">

                                <input type="hidden" name="browserName" id="browserName" />
                                <input type="hidden" name="majorVersion" id="majorVersion" />
                                <input type="hidden" name="fullVersion" id="fullVersion" />
                                <input type="hidden" name="appName" id="appName" />
                                <input type="hidden" name="userAgent" id="userAgent" />
                                <input type="hidden" name="os" id="os" />
                                <input type="hidden" name="_redirect" value="<?=$this->url(array("lang" => $this->language, "action" => "shipping"), "coreshop_checkout", true)?>" />
                                <input type="hidden" name="_error" value="<?=$this->url(array("lang" => $this->language, "action" => "index"), "coreshop_checkout", true)?>" />

                                <input type="hidden" name="isGuest" id="isGuest" value="1" />

                                <div class="row">
                                    <div class="col-xs-12 col-sm-6">
                                        <h3 class="panel-heading inner visible-xs">
                                            <?=$this->translate("Personal Information")?>
                                        </h3>

                                        <!-- Personal Information Starts -->
                                        <div class="form-group">
                                            <label for="firstname" class="col-sm-3 control-label"><?=$this->translate("First Name")?> :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="<?=$this->translate("First Name")?>">
                                            </div>
                                            <div data-for="firstname" class="col-sm-push-3 col-sm-9"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lastname" class="col-sm-3 control-label"><?=$this->translate("Last Name")?> :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="<?=$this->translate("Last Name")?>">
                                            </div>
                                            <div data-for="lastname" class="col-sm-push-3 col-sm-9"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="col-sm-3 control-label"><?=$this->translate("Email")?> :</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="email" name="email" placeholder="<?=$this->translate("Email")?>">
                                            </div>
                                            <div data-for="email" class="col-sm-push-3 col-sm-9"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="gender" class="col-sm-3 control-label"><?=$this->translate("Gender")?></label>
                                            <div class="col-sm-9">
                                                <select name="gender" class="form-control" title="<?=$this->translate("Geschlecht")?>">
                                                    <option></option>
                                                    <?php
                                                    $fd = $class->getFieldDefinition("gender");
                                                    $options = $fd->getOptions();
                                                    $value = $postValue("gender");
                                                    ?>
                                                    <?php foreach ($options as $option) { ?>
                                                        <?php if ($option['key']) { ?>
                                                            <option value="<?=$option['value']?>" <?=$value == $option['value'] ? "selected" : ""?>><?=$this->translate($option['key'])?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div data-for="gender" class="col-sm-push-3 col-sm-9"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">

                                        <h3 class="panel-heading inner visible-xs">
                                            <?=$this->translate("Address")?>
                                        </h3>

                                        <?=$this->template("coreshop/user/helper/address.php") ?>
                                    </div>

                                    <div class="col-xs-12">
                                        <h3 class="panel-heading inner">
                                            <?=$this->translate("Newsletter")?>
                                        </h3>

                                        <div class="form-group">
                                            <span class="col-sm-3 control-label"><?=$this->translate("Newsletter")?> :</span>
                                            <div class="col-sm-9">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
                                                        <?=$this->translate("Subscribe")?>
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="option1">
                                                        <?=$this->translate("Unsubscribe")?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-9">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox"> <?=$this->translate("I'v read and agreed on Conditions")?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-9">
                                            <button type="submit" class="btn btn-black">
                                                <?=$this->translate("Register")?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>
</div>
<!-- Main Container Ends -->