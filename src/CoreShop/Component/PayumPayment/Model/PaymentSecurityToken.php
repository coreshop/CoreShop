<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\PayumPayment\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Security\Util\Random;

/**
 * @psalm-suppress MissingConstructor
 */
class PaymentSecurityToken implements ResourceInterface, TokenInterface
{
    use SetValuesTrait;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var mixed
     */
    protected $details;

    /**
     * @var string
     */
    protected $afterUrl;

    /**
     * @var string
     */
    protected $targetUrl;

    /**
     * @var string
     */
    protected $gatewayName;

    public function __construct(
        ) {
        $this->hash = Random::generateToken();
    }

    public function getId(): ?int
    {
        return null;
    }

    public function setDetails($details)
    {
        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }

    public function getAfterUrl()
    {
        return $this->afterUrl;
    }

    public function setAfterUrl($afterUrl)
    {
        $this->afterUrl = $afterUrl;
    }

    public function getGatewayName()
    {
        return $this->gatewayName;
    }

    public function setGatewayName($gatewayName)
    {
        $this->gatewayName = $gatewayName;
    }
}
