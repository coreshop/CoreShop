<?php
//TODO: finish View
$class = Object_Class::getByName("CoreShopUser");

$postValue = function ($name) {
    if (isset($_POST[$name])) {
        return $_POST[$name];
    }

    $methodName = "get" . ucfirst($name);

    if(method_exists($this->session->user, $methodName))
    {
        return $this->session->user->$methodName();
    }

    return;
};
?>
<div id="main-container" class="container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
        <li><a href="<?=$this->url(array("lang" => $this->language, "action" => "profile"), "coreshop_user")?>"><?=$this->translate("My Profile")?></a></li>
        <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "action" => "settings"), "coreshop_user")?>"><?=$this->translate("Settings")?></a></li>
    </ol>
    <!-- Breadcrumb Ends -->
    <!-- Main Heading Starts -->
    <h2 class="main-heading text-center">
        <?=$this->translate("Settings")?>
    </h2>
    <!-- Main Heading Ends -->
    <section class="registration-area">
        <div class="row">
            <div class="col-sm-12">
                <?php if($this->message) { ?>
                    <div class="alert alert-danger" role="alert"><?=$this->message?></div>
                <?php } ?>

                <?php if($this->success) { ?>
                    <div class="alert alert-success" role="alert"><?=$this->translate("Successfully saved.")?></div>
                <?php } ?>

                <!-- Registration Block Starts -->
                <div class="panel panel-smart">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=$this->translate("Personal Information")?></h3>
                    </div>
                    <div class="panel-body">
                        <!-- Registration Form Starts -->
                        <form class="form-horizontal" role="form" id="shop-settings-form" action="<?=$this->url(array("lang" => $this->language, "action" => "settings"), "coreshop_user")?>" method="post">

                            <input type="hidden" name="browserName" id="browserName" />
                            <input type="hidden" name="majorVersion" id="majorVersion" />
                            <input type="hidden" name="fullVersion" id="fullVersion" />
                            <input type="hidden" name="appName" id="appName" />
                            <input type="hidden" name="userAgent" id="userAgent" />
                            <input type="hidden" name="os" id="os" />

                            <!-- Personal Information Starts -->
                            <div class="form-group">
                                <label for="firstname" class="col-sm-3 control-label"><?=$this->translate("First Name")?> :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="<?=$this->translate("First Name")?>" value="<?=$postValue("firstname")?>">
                                </div>
                                <div data-for="firstname" class="col-sm-push-3 col-sm-9"></div>
                            </div>
                            <div class="form-group">
                                <label for="lastname" class="col-sm-3 control-label"><?=$this->translate("Last Name")?> :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="<?=$this->translate("Last Name")?>" value="<?=$postValue("lastname")?>">
                                </div>
                                <div data-for="lastname" class="col-sm-push-3 col-sm-9"></div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label"><?=$this->translate("Email")?> :</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="<?=$this->translate("Email")?>" value="<?=$postValue("email")?>">
                                </div>
                                <div data-for="email" class="col-sm-push-3 col-sm-9"></div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label"><?=$this->translate("Password")?> :</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="<?=$this->translate("Password")?>">
                                </div>
                                <div data-for="password" class="col-sm-push-3 col-sm-9"></div>
                            </div>
                            <div class="form-group">
                                <label for="inputRePassword" class="col-sm-3 control-label"><?=$this->translate("Re-Password")?> :</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="repassword" name="repassword" placeholder="<?=$this->translate("Re-Password")?>">
                                </div>
                                <div data-for="repassword" class="col-sm-push-3 col-sm-9"></div>
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

                            <h3 class="panel-heading inner">
                                <?=$this->translate("Newsletter")?>
                            </h3>

                            <div class="form-group">
                                <span class="col-sm-3 control-label"><?=$this->translate("Newsletter")?> :</span>
                                <div class="col-sm-9">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="newsletterConfirmed" id="newsletterConfirmed" value="on" <?=$postValue("newsletterConfirmed") ? "checked" : ""?>>
                                            <?=$this->translate("Subscribe")?>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="newsletterConfirmed" id="newsletterConfirmed" value="off" <?=$postValue("newsletterConfirmed") ? "" : "checked"?>>
                                            <?=$this->translate("Unsubscribe")?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-black">
                                        <?=$this->translate("Save")?>
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
</div>