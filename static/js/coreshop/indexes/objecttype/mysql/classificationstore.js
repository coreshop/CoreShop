/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.plugin.coreshop.indexes.objecttype.mysql.classificationstore");

pimcore.plugin.coreshop.indexes.objecttype.mysql.classificationstore = Class.create(pimcore.plugin.coreshop.indexes.objecttype.mysql.abstract, {
    getObjectTypeItems : function(record) {
        var fields = pimcore.plugin.coreshop.indexes.objecttype.mysql.abstract.prototype.getObjectTypeItems.call(this, record);

        //TODO: Make combobox and query ClassDefinition
        //TODO: classificationStoreField would be needed in every Index-Type: Maybe include some helper js?
        fields.push(new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_classificationstore'),
            name : 'classificationStoreField',
            length : 255,
            width : 200,
            value : record.data.classificationStoreField
        }));

        return fields;
    }
});