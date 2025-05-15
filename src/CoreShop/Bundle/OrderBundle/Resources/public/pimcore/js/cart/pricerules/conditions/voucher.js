/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.cart.pricerules.conditions.voucher');
coreshop.cart.pricerules.conditions.voucher = Class.create(coreshop.rules.conditions.abstract, {
    type: 'voucher',

    getForm: function () {

        this.form = new Ext.form.Panel({
            items: [{
                fieldLabel: t('coreshop_action_voucher_max_usage_per_code'),
                xtype: 'numberfield',
                name: 'maxUsagePerCode',
                value: this.data.maxUsagePerCode
            },{
                fieldLabel: t('coreshop_action_voucher_max_usage_per_user'),
                xtype: 'numberfield',
                name: 'maxUsagePerUser',
                value: this.data.maxUsagePerUser
            },
            {
                fieldLabel: t('coreshop_action_voucher_only_one_per_cart'),
                xtype: 'checkbox',
                name: 'onlyOnePerCart',
                value: this.data.onlyOnePerCart
            }]
        });

        return this.form;
    },

    getValues: function () {
        return this.form.getForm().getValues();
    }
});
