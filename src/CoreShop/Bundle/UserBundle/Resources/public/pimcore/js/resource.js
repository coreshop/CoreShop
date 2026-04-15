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

pimcore.registerNS('coreshop.user.resource');
coreshop.user.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.broker.fireEvent('resource.register', 'coreshop.user', this);
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.user.resource();
});
