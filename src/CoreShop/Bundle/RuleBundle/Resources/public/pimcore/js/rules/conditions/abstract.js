/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.rules.conditions');
pimcore.registerNS('coreshop.rules.conditions.abstract');

coreshop.rules.conditions.abstract = Class.create(coreshop.rules.abstract, {
    elementType: 'condition',

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
