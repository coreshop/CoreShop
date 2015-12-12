<div class="row bs-wizard" style="border-bottom:0;">
    <div class="col-xs-3 col-sm-2 col-sm-offset-1 bs-wizard-step <?=$this->step >= 1 ? ($this->step == 1 ? "active" : "complete") : ""?>">
        <div class="text-center bs-wizard-stepnum"><?=$this->translate("Overview")?></div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center"></div>
    </div>

    <div class="col-xs-2 bs-wizard-step <?=$this->step >= 2 ? ($this->step == 2 ? "active" : "complete") : "disabled"?>"><!-- complete -->
        <div class="text-center bs-wizard-stepnum"><?=$this->translate("Login")?></div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center"></div>
    </div>

    <div class="col-xs-2 bs-wizard-step <?=$this->step >= 3 ? ($this->step == 3 ? "active" : "complete") : "disabled"?>"><!-- complete -->
        <div class="text-center bs-wizard-stepnum"><?=$this->translate("Address")?></div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center"></div>
    </div>

    <div class="col-xs-3 col-sm-2 bs-wizard-step <?=$this->step >= 4 ? ($this->step == 4 ? "active" : "complete") : "disabled"?>"><!-- active -->
        <div class="text-center bs-wizard-stepnum"><?=$this->translate("Shipping")?></div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center"></div>
    </div>
    
    <div class="col-xs-2 bs-wizard-step <?=$this->step >= 5 ? ($this->step == 5 ? "active" : "complete") : "disabled"?>"><!-- active -->
        <div class="text-center bs-wizard-stepnum"><?=$this->translate("Payment")?></div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center"></div>
    </div>
</div>