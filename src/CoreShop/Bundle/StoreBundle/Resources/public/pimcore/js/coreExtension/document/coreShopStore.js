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

pimcore.registerNS('pimcore.document.editables.coreshop_store');
pimcore.document.editables.coreshop_store = Class.create(coreshop.document.editable.select, {

    getType: function() {
        return 'coreshop_store';
    }
});
