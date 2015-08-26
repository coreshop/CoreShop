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
        var me = this;

        var country = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_country_country'),
            typeAhead: true,
            value: this.data.currency,
            mode: 'local',
            listWidth: 100,
            width : 200,
            store: pimcore.globalmanager.get("coreshop_countries"),
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            hiddenName:'country',
            listeners: {
                beforerender: function () {
                    this.setValue(me.data.country);
                }
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