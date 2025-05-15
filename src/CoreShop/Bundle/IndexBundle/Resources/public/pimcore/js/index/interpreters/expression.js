/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.index.interpreters.expression');

coreshop.index.interpreters.expression = Class.create(coreshop.index.interpreters.abstract, {

    getLayout: function (record, interpreterConfig) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_index_interpreter_expression'),
                name: 'expression',
                length: 255,
                value: interpreterConfig ? interpreterConfig.expression : null,
                allowBlank: false
            }
        ];
    }

});
