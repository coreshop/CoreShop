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

pimcore.registerNS('coreshop.rules.conditions');
pimcore.registerNS('coreshop.rules.conditions.abstract');

coreshop.rules.conditions.abstract = Class.create(coreshop.rules.abstract, {
    elementType: 'condition',

    getFormattedStackClasses: function (stackClasses) {
        var classes = [];
        if (Ext.isArray(stackClasses)) {
            Ext.Array.each(stackClasses, function (cClass) {
                classes.push({classes: cClass});
            });
        }
        return classes;
    },

    getForm: function () {

        this.form = Ext.create('Ext.form.FieldContainer', {
            items: [
                {
                    xtype: 'displayfield',
                    submitValue: false,
                    value: t('coreshop_condition_no_configuration'),
                    cls: 'description',
                    anchor: '100%',
                    width: '100%',
                    style: 'font-style:italic;background:#f5f5f5;padding:0 10px;',
                    getValue: function () {
                        return undefined;
                    }
                }
            ]
        });

        return this.form;
    }
});
