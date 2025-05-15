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

pimcore.registerNS('coreshop.index.worker');
pimcore.registerNS('coreshop.index.worker.abstract');

coreshop.index.worker.abstract = Class.create({
    parent: null,

    initialize: function (parent) {
        this.parent = parent;
    },

    getForm: function (configuration) {
        return Ext.form.Panel({
            items: this.getFields(configuration)
        });
    },

    getFields: function (configuration) {
        return [];
    }
});
