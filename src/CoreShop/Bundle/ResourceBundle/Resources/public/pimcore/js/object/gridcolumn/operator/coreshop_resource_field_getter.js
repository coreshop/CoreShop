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

pimcore.registerNS("pimcore.object.gridcolumn.operator.coreshop_resource_field_getter");

pimcore.object.gridcolumn.operator.coreshop_resource_field_getter = Class.create(pimcore.object.gridcolumn.operator.objectfieldgetter, {
    operatorGroup: "extractor",
    type: "operator",
    class: "coreshop_resource_field_getter",
    iconCls: "pimcore_icon_operator_object_field_getter",
    defaultText: "CoreShop Resource Field Getter",
    group: "getter"
});