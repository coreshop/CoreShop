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

pimcore.registerNS('coreshop.index.interpreters.objectProperty');

coreshop.index.interpreters.objectProperty = Class.create(coreshop.index.interpreters.abstract, {

    getLayout: function (record, interpreterConfig) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_index_interpreter_property'),
                name: 'property',
                length: 255,
                value: interpreterConfig ? interpreterConfig.property : null,
                allowBlank: false
            }
        ];
    }

});
