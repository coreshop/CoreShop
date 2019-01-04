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

pimcore.registerNS('coreshop.index.interpreters.objectproperty');

coreshop.index.interpreters.objectproperty = Class.create(coreshop.index.interpreters.abstract, {

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
