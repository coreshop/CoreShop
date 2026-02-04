
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

pimcore.registerNS('coreshop.messenger.resource');

if (coreshop.resource !== undefined) {
    coreshop.messenger.resource = Class.create(coreshop.resource, {
        initialize: function () {
            coreshop.broker.fireEvent('resource.register', 'coreshop.messenger', this);
        },

        openResource: function (item) {
            if (item === 'list') {
                this.openList();
            }
        },

        openList: function () {
            try {
                pimcore.globalmanager.get('coreshop_messenger_list').activate();
            } catch (e) {
                pimcore.globalmanager.add('coreshop_messenger_list', new coreshop.messenger.list());
            }
        },
    });

    coreshop.broker.addListener('pimcore.ready', function () {
        new coreshop.messenger.resource();
    });
}