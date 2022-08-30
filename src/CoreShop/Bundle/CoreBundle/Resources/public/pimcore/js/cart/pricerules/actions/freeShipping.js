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
