/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.tier_pricing.specific_tier_price.actions.abstract');
coreshop.tier_pricing.specific_tier_price.actions.abstract = Class.create({
    type: null,
    range: null,

    initialize: function(range) {
        this.range = range;
    },

    getGridColumns: function() {
        return [];
    },

    beforeEdit: function(record, editor, context) {
        return true;
    },

    storeChange: function(model, field) {

    },

    defaultValues: function(lastEntry) {
        return {};
    },

    adjustRangeStoreData: function(entry) {

    },

    getData: function(record) {
        return {}
    }
});
