<div id="main-container" class="container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="<?=$this->url(array("lang" => $this->language), "coreshop_index", true)?>"><?=$this->translate("Home")?></a></li>
        <li class="active"><?=$this->translate("Contact Us")?></li>
    </ol>
    <!-- Breadcrumb Ends -->
    <!-- Main Heading Starts -->
    <h2 class="main-heading text-center">
        Contact Us
    </h2>
    <!-- Main Heading Ends -->
    <!-- Google Map Starts -->
    <div id="map-wrapper">
        <div id="map-block"></div>
    </div>
    <!-- Google Map Ends -->
    <!-- Starts -->
    <div class="row">
        <!-- Contact Details Starts -->
        <div class="col-sm-4">
            <div class="panel panel-smart">
                <div class="panel-heading">
                    <h3 class="panel-title"><?=$this->translate("Contact Details")?></h3>
                </div>
                <div class="panel-body">
                    <ul class="list-unstyled contact-details">
                        <li class="clearfix">
                            <i class="fa fa-home pull-left"></i>
                                <span class="pull-left">
                                    <?=$this->textarea("companyDetails", array("width" => 200, "height" => 100))?>
                                </span>
                        </li>
                        <li class="clearfix">
                            <i class="fa fa-phone pull-left"></i>
                                <span class="pull-left">
                                    <?=$this->textarea("companyPhone", array("width" => 200, "height" => 100))?>
                                </span>
                        </li>
                        <li class="clearfix">
                            <i class="fa fa-envelope-o pull-left"></i>
                                <span class="pull-left">
                                    <?=$this->textarea("companyMail", array("width" => 200, "height" => 100))?>
                                </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Contact Details Ends -->
        <!-- Contact Form Starts -->
        <div class="col-sm-8">
            <div class="panel panel-smart">
                <div class="panel-heading">
                    <h3 class="panel-title"><?=$this->translate("Send us a mail (PS: not working now ;) )")?></h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">
                                <?=$this->translate("Name")?>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" placeholder="<?=$this->translate("Name")?>" data-cip-id="name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">
                                <?=$this->translate("Email")?>
                            </label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="email" placeholder="<?=$this->translate("Email")?>" data-cip-id="email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject" class="col-sm-2 control-label">
                                <?=$this->translate("Subject")?>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="subject" id="subject" placeholder="<?=$this->translate("Subject")?>" data-cip-id="subject">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message" class="col-sm-2 control-label">
                                <?=$this->translate("Message")?>
                            </label>
                            <div class="col-sm-10">
                                <textarea name="message" id="message" class="form-control" rows="5" placeholder="<?=$this->translate("Message")?>"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-black text-uppercase"><?=$this->translate("Submit")?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Contact Form Ends -->
    </div>
    <!-- Ends -->
</div>