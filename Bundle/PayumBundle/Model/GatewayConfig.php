<?php

namespace CoreShop\Bundle\PayumBundle\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;
use Payum\Core\Model\GatewayConfig as BaseGatewayConfig;
use CoreShop\Component\Resource\Model\ResourceInterface;

class GatewayConfig extends BaseGatewayConfig implements ResourceInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
