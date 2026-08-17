/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
