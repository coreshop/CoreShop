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

pimcore.registerNS('coreshop.zone.item');
coreshop.zone.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_zone',

    routing: {
        save: 'coreshop_zone_save'
    },

    getFormPanelItems: function () {
        return [
            {
                fieldLabel: t('name'),
                name: 'name',
                value: this.data.name
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('active'),
                name: 'active',
                checked: this.data.active
            }
        ];
    },

    getSaveData: function () {
        var values = this.formPanel.getForm().getFieldValues();

        if (!values['active']) {
            delete values['active'];
        }

        return values;
    }
});
