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

document.addEventListener(pimcore.events.pimcoreReady, (e) => {
    if (coreshop.menu.coreshop.coreshop) {
        new coreshop.menu.coreshop.coreshop();
    }
});