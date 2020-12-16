<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PaymentBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;

class PaymentProvider extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopPaymentProvider';

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.payment_provider');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.payment_provider.class');
    }

    protected function getInterface(): string
    {
        return '\\' . PaymentProviderInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}
