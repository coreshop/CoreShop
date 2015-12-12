<div id="main-container" class="container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
        <li class="active"><a href="<?=$this->url(array("lang" => $this->language, "action" => "login"), "coreshop_user")?>"><?=$this->translate("Login")?></a></li>
    </ol>

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
                <?=$this->message?>
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
                        <form class="form-inline" role="form" method="post" action="<?=$this->url(array("lang" => $this->language, "action" => "login"), "coreshop_user")?>">
                            <input type="hidden" name="_redirect" value="<?=$this->url(array("lang" =>  $this->language), "coreshop_index")?>" />

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
                        <a href="<?=$this->url(array("lang" => $this->language, "action" => "register"), "coreshop_user")?>" class="btn btn-black">
                            <?=$this->translate("Register")?>
                        </a>
                    </div>
                </div>
                <!-- Account Panel Ends -->
            </div>
        </div>
    </section>
    <!-- Login Form Section Ends -->
</div>
<!-- Main Container Ends -->