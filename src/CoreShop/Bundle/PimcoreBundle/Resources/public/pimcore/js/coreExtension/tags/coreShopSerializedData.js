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

pimcore.registerNS('coreshop.object.tags.coreShopSerializedData');
coreshop.object.tags.coreShopSerializedData = Class.create(pimcore.object.tags.abstract, {

    allowEmpty: false,

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;
        this.fieldConfig.width = 350;
    },

    getLayoutEdit: function () {

        this.component = new Ext.panel.Panel({
            html: 'nothing to see here'
        });

        return this.component;
    }
});
