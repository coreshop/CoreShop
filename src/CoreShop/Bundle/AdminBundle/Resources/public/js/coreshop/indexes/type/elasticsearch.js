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

pimcore.registerNS('pimcore.plugin.coreshop.indexes.type');

pimcore.plugin.coreshop.indexes.type.elasticsearch = Class.create(pimcore.plugin.coreshop.indexes.type.abstract, {
    getFields: function (config) {
        return {
            xtype: 'fieldset',
            autoHeight: true,
            labelWidth: 350,
            defaultType: 'textfield',
            defaults: {width: '100%'},
            items: [{
                xtype: 'textfield',
                name: 'hosts',
                fieldLabel: 'hosts',
                value: config.hosts ? config.hosts : ""
            }]
        };
    }
});