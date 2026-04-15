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

pimcore.registerNS('coreshop.filter.conditions.select_from_multiselect');

coreshop.filter.conditions.select_from_multiselect = Class.create(coreshop.filter.conditions.select, {
    type: 'select_from_multiselect',
});
