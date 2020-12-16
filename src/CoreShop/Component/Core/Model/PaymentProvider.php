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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Payment\Model\PaymentProvider as BasePaymentProvider;
use CoreShop\Component\Store\Model\StoresAwareTrait;
use Payum\Core\Model\GatewayConfigInterface;

class PaymentProvider extends BasePaymentProvider implements PaymentProviderInterface
{
    use StoresAwareTrait {
        __construct as storesAwareConstructor;
    }

    /**
     * @var GatewayConfigInterface
     */
    protected $gatewayConfig;

    public function __construct()
    {
        parent::__construct();

        $this->storesAwareConstructor();
    }

    public function setGatewayConfig(GatewayConfigInterface $gatewayConfig)
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @return GatewayConfigInterface
     */
    public function getGatewayConfig()
    {
        return $this->gatewayConfig;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s', $this->getIdentifier());
    }
}
