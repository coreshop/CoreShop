<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;

class PaymentProviderSupportsEvent extends Event
{
    /**
     * @var PaymentProviderInterface
     */
    private $paymentProvider;

    /**
     * @var ResourceInterface
     */
    private $subject;

    /**
     * @var bool
     */
    private $supported = true;

    /**
     * @param PaymentProviderInterface $paymentProvider
     * @param ResourceInterface        $subject
     */
    public function __construct(PaymentProviderInterface $paymentProvider, ResourceInterface $subject = null)
    {
        $this->paymentProvider = $paymentProvider;
        $this->subject = $subject;
    }

    /**
     * @return PaymentProviderInterface
     */
    public function getPaymentProvider()
    {
        return $this->paymentProvider;
    }

    /**
     * @return ResourceInterface
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return bool
     */
    public function isSupported()
    {
        return $this->supported;
    }

    /**
     * @param bool $supported
     */
    public function setSupported(bool $supported)
    {
        $this->supported = $supported;
    }
}
