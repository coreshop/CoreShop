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

pimcore.registerNS('coreshop.index.getters.brick');

coreshop.index.getters.brick = Class.create(coreshop.index.getters.abstract, {

    getLayout: function (record) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_index_field_brickfield'),
                name: 'brickField',
                length: 255,
                value: record.data.getterConfig ? record.data.getterConfig.brickField : null,
                allowBlank: false
            }
        ];
    }

});
