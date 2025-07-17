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

namespace CoreShop\Component\PayumPayment\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use Payum\Core\Model\GatewayConfig as BaseGatewayConfig;

/**
 * @psalm-suppress MissingConstructor
 */
class GatewayConfig extends BaseGatewayConfig implements ResourceInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
