/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.worker.type.elasticsearch');
coreshop.index.worker.elasticsearch = Class.create(coreshop.index.worker.abstract, {
    getFields: function (config) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: 'Hosts (Comma separated)',
                name: 'hosts',
                value: config.hosts,
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Username',
                name: 'username',
                value: config.username,
                allowBlank: true,
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Password',
                name: 'password',
                value: config.password,
                allowBlank: true,
            },
        ];
    }
});
