<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Bundle\PayumBundle\Model\GatewayConfig;
use CoreShop\Component\Payment\Model\PaymentProvider as BasePaymentProvider;
use CoreShop\Component\Store\Model\StoresAwareTrait;

class PaymentProvider extends BasePaymentProvider implements PaymentProviderInterface
{
    use StoresAwareTrait {
        __construct as storesAwareConstructor;
    }


    private $logo;

    /**
     * @var GatewayConfig
     */
    protected $gatewayConfig;

    public function __construct()
    {
        parent::__construct();

        $this->storesAwareConstructor();
    }

    /**
     * {@inheritdoc}
     */
    public function setGatewayConfig(GatewayConfig $gatewayConfig)
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @return GatewayConfig
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

    /**
     * {@inheritdoc}
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }
}
