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

pimcore.registerNS('coreshop.notification.rule.conditions.abstractTransition');

coreshop.notification.rule.conditions.abstractTransition = Class.create(coreshop.rules.conditions.abstract, {
    getRepoName: function() {
        return '';
    },

    getForm: function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_to'),
                    name: 'transition',
                    value: this.data ? this.data.transition : [],
                    width: 250,
                    store: pimcore.globalmanager.get(this.getRepoName()),
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local',
                    displayField: 'name',
                    valueField: 'name'
                }
            ]
        });

        return this.form;
    }
});
