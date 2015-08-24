/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.conditions.country");
pimcore.plugin.coreshop.pricerule.conditions.country = Class.create(pimcore.plugin.coreshop.pricerule.conditions.abstract, {

    type : 'country',

    getForm : function() {
        var country = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_country_country'),
            typeAhead: true,
            value: this.data.currency,
            mode: 'local',
            listWidth: 100,
            store: pimcore.globalmanager.get("coreshop_countries"),
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            hiddenName:'country',
            listeners: {
                change: function () {
                    this.forceReloadOnSave = true;
                }.bind(this),
                select: function () {
                    this.forceReloadOnSave = true;
                }.bind(this)
            }
        };


        this.form = new Ext.form.FieldSet({
            items : [
                country
            ]
        });

        return this.form;
    }
});