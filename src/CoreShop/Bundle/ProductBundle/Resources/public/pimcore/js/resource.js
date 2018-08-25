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

pimcore.registerNS('coreshop.product.resource');
coreshop.product.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.broker.fireEvent('resource.register', 'coreshop.product', this);
    },

    openResource: function (item) {
        if (item === 'product_price_rule') {
            this.openProductPriceRule();
        }
    },

    openProductPriceRule: function () {
        try {
            pimcore.globalmanager.get('coreshop_product_price_rule_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_product_price_rule_panel', new coreshop.product.pricerule.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.product.resource();
});