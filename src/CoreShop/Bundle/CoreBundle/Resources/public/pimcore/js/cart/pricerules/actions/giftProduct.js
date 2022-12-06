/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.cart.pricerules.actions.giftProduct');
coreshop.cart.pricerules.actions.giftProduct = Class.create(coreshop.rules.actions.abstract, {
    type: 'giftProduct',

    getForm: function () {
        this.product = new coreshop.object.elementHref({
            id: this.data ? this.data.product : null,
            type: 'object',
        }, {
            objectsAllowed: true,
            classes: this.getFormattedStackClasses(coreshop.stack.coreshop.purchasable),
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
