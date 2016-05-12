<!-- CORESHOP DEBUG -->
<script type="text/javascript" src="/plugins/CoreShop/static/js/frontend/debug.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/CoreShop/static/css/debug.css" />

<div id="coreshop-debug">

    <div class="coreshop-debug-panel">
        <div class="coreshop-debug-panel-heading">
            <h3 class="coreshop-debug-panel-title">CoreShop <?=$this->translate("coreshop_debug")?></h3>
            <span class="coreshop-debug-clickable coreshop-debug-panel-collapsed"><i class="glyphicon glyphicon-chevron-up"></i></span>
        </div>
        <div class="coreshop-debug-panel-body" style="display:none">
            <table class="coreshop-debug-table">
                <tr>
                    <td><?=$this->translate("coreshop_country")?></td>
                    <td><?=\CoreShop\Tool::getCountry()->getName() ?> (<?=\CoreShop\Tool::getCountry()->getId()?>)</td>
                </tr>
                <tr>
                    <td><?=$this->translate("coreshop_currency")?></td>
                    <td><?=\CoreShop\Tool::getCurrency()->getName() ?> (<?=\CoreShop\Tool::getCurrency()->getId()?>)</td>
                </tr>
            </table>
        </div>
    </div>
</div>