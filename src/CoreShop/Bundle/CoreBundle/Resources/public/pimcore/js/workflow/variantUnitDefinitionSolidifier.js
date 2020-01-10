/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */
pimcore.registerNS('coreshop.product.workflow');
pimcore.registerNS('coreshop.product.workflow.variantUnitDefinitionSolidifier');
coreshop.product.workflow.variantUnitDefinitionSolidifier = Class.create({

    panel: null,

    object: null,

    data: null,

    initialize: function (object, panel) {

        this.panel = panel;
        this.object = object;
        this.data = object.data;

        Ext.Ajax.request({
            url: '/admin/coreshop/product-variant-solidifier/check/' + this.object.general.o_id,
            method: 'GET',
            success: function (response) {
                var res = Ext.decode(response.responseText);
                if (res.success === true) {
                    this.checkStatus(res);
                } else {
                    Ext.Msg.alert(t('error'), res.message);
                }
            }.bind(this)
        });
    },

    checkStatus: function (response) {

        if (response.errorStatus !== false) {
            Ext.MessageBox.alert(t('error'), t('coreshop_solidify_variant_unit_definition_data_' + response.errorStatus));
            return;
        }

        Ext.MessageBox.confirm(t('info'), t('coreshop_solidify_variant_unit_definition_data_' + response.strategy), function (buttonValue) {
            if (buttonValue === 'yes') {
                this.applySolidification();
            }
        }.bind(this));
    },

    applySolidification: function () {

        this.panel.setLoading(t('loading'));

        Ext.Ajax.request({
            url: '/admin/coreshop/product-variant-solidifier/apply/' + this.object.general.o_id,
            method: 'PUT',
            success: function (response) {

                var res = Ext.decode(response.responseText),
                    affectedVariantIds = [];

                if (res.success === true) {
                    affectedVariantIds = res.affectedVariants;
                    this.cleanUp(affectedVariantIds);
                    this.panel.setLoading(false);
                    Ext.Msg.alert(t('success'), t('coreshop_solidify_variant_unit_definition_data_succesfully_applied').format(affectedVariantIds.length));
                } else {
                    this.panel.setLoading(false);
                    Ext.Msg.alert(t('error'), res.message);
                }
            }.bind(this),
            failure: function (response) {
                this.panel.setLoading(false);
            }.bind(this)
        });

    },

    cleanUp: function (affectedVariantIds) {

        if (!Ext.isArray(affectedVariantIds)) {
            return;
        }

        Ext.Array.each(affectedVariantIds, function (id) {
            var tabId = 'object_' + id,
                panel = Ext.getCmp(tabId);
            if (panel) {
                pimcore.helpers.closeObject(id);
            }
        });
    }
});
