<?php

declare(strict_types=1);

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

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface PaymentProviderRuleGroupInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    /**
     * @return PaymentProviderInterface|null
     */
    public function getPaymentProvider();

    public function setPaymentProvider(?PaymentProviderInterface $paymentProvider);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return bool
     */
    public function getStopPropagation();

    /**
     * @param bool $stopPropagation
     */
    public function setStopPropagation($stopPropagation);

    /**
     * @return PaymentProviderRuleInterface
     */
    public function getPaymentProviderRule();

    public function setPaymentProviderRule(PaymentProviderRuleInterface $paymentProviderRule);
}
