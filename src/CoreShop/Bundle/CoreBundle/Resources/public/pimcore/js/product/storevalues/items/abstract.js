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

pimcore.registerNS('coreshop.product.storeValues.items');
pimcore.registerNS('coreshop.product.storeValues.items.abstract');

coreshop.product.storeValues.items.abstract = Class.create({

    builder: null,

    initialize: function (builder) {
        this.builder = builder;
    },

    getForm: function () {
        // needs to be modified by 3rd party.
        return [];
    },

    onUnitDefinitionsReadyOrChange: function (data) {
        // keep it for 3rd party modifiers.
    },

    getDataValue: function (key) {

        var data, values;

        data = this.builder.data !== null && Ext.isObject(this.builder.data) ? this.builder.data : null;
        if (data === null) {
            return null;
        }

        values = data.values !== null && Ext.isObject(data.values) ? data.values : null;
        if (values === null) {
            return null;
        }

        if (values.hasOwnProperty(key)) {
            return values[key];
        }

        return null;
    }
});
