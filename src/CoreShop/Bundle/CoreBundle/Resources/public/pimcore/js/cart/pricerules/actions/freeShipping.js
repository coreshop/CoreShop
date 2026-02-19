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

pimcore.registerNS('coreshop.cart.pricerules.actions.freeShipping');
coreshop.cart.pricerules.actions.freeShipping = Class.create(coreshop.rules.actions.abstract, {
    type: 'freeShipping',

    getForm: function () {

        this.form = new Ext.form.Panel({
            type: 'FreeShipping',
            forceLayout: true
        });

        return this.form;
    }
});
