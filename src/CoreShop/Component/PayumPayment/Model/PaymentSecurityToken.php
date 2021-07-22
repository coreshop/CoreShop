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

namespace CoreShop\Component\PayumPayment\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Security\Util\Random;
use Payum\Core\Storage\IdentityInterface;

class PaymentSecurityToken implements ResourceInterface, TokenInterface
{
    use SetValuesTrait;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var IdentityInterface
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

    public function __construct()
    {
        $this->hash = Random::generateToken();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->hash;
    }

    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * {@inheritdoc}
     *
     * @return IdentityInterface|null
     */
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
