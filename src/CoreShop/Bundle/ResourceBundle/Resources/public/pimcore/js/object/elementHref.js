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

pimcore.registerNS('coreshop.object.elementHref');
coreshop.object.elementHref = Class.create(pimcore.object.tags.href, {
    getLayoutEdit: function ($super) {
        var me = this,
            element = $super();

        if (this.data) {
            if (!this.data.path) {
                this.component.setValue(this.data.id);
            }
        }

        this.component.setReadOnly(true);

        this.component.getModelData = function (includeEmptyText, /*private*/
                                                isSubmitting) {
            var data = null;
            // Note that we need to check if this operation is being called from a Submit action because displayfields aren't
            // to be submitted,  but they can call this to get their model data.
            if (!this.disabled && (this.submitValue || !isSubmitting)) {
                data = {};
                data[this.getFieldIdentifier()] = me.getValue();
            }
            return data;
        };

        return element;
    },

    requestNicePathData: function () {
        if (this.data.id) {
            coreshop.helpers.requestNicePathData([this.data], function (responseData) {
                if (typeof responseData[this.data.id] !== "undefined") {
                    this.component.setValue(responseData[this.data.id]);
                }
            }.bind(this));
        }
    },

    getValue: function () {
        return this.data.id;
    }
});
