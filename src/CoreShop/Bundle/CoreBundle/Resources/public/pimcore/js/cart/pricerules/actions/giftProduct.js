/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.cart.pricerules.actions.giftProduct');
coreshop.cart.pricerules.actions.giftProduct = Class.create(coreshop.rules.actions.abstract, {
    type: 'giftProduct',

    getForm: function () {
        this.product = new coreshop.object.elementHref({
            id: this.data ? this.data.product : null,
            type: 'object',
            subtype: coreshop.class_map['coreshop.product']
        }, {
            objectsAllowed: true,
            classes: [{
                classes: coreshop.class_map['coreshop.product']
            }],
            name: 'product',
            title: t('coreshop_action_giftProduct')
        });

        this.form = new Ext.form.Panel({
            items: [
                this.product.getLayoutEdit()
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            product: this.product.getValue()
        };
    }
});
