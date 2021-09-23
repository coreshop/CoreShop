<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ShippingBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Shipping\Model\CarrierInterface;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class Carrier extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopCarrier';

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.carrier');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.carrier.class');
    }

    protected function getInterface(): string
    {
        return '\\' . CarrierInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}
