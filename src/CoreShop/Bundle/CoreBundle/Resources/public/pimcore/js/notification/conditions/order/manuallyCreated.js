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

pimcore.registerNS('coreshop.notification.rule.conditions.manuallyCreated');

coreshop.notification.rule.conditions.manuallyCreated = Class.create(coreshop.rules.conditions.abstract, {
    type: 'manuallyCreated',

    getForm: function () {
        this.form = new Ext.form.Panel({
            items: [
                {
                    xtype: 'checkbox',
                    fieldLabel: t('coreshop_condition_manuallyCreated'),
                    name: 'manuallyCreated',
                    checked: this.data ? this.data.manuallyCreated : false
                }
            ]
        });

        return this.form;
    }
});
