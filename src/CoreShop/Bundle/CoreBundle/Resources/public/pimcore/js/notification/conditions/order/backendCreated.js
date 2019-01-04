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

pimcore.registerNS('coreshop.notification.rule.conditions.backendCreated');

coreshop.notification.rule.conditions.backendCreated = Class.create(coreshop.rules.conditions.abstract, {
    type: 'backendCreated',

    getForm: function () {
        this.form = new Ext.form.Panel({
            items: [
                {
                    xtype: 'checkbox',
                    fieldLabel: t('coreshop_condition_backendCreated'),
                    name: 'backendCreated',
                    checked: this.data ? this.data.backendCreatedbackendCreated : false
                }
            ]
        });

        return this.form;
    }
});
