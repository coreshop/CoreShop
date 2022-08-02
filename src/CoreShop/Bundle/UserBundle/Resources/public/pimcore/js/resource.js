/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
