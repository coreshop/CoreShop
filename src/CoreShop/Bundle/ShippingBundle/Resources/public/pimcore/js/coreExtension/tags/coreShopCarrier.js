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

pimcore.registerNS('pimcore.object.tags.coreShopCarrier');
pimcore.object.tags.coreShopCarrier = Class.create(coreshop.object.tags.select, {

    type: 'coreShopCarrier',
    storeName: 'coreshop_carriers',
    displayField: 'identifier'
});
