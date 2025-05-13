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

namespace CoreShop\Bundle\OrderBundle\Event;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class WkhtmlOptionsEvent extends Event
{
    protected string $options;

    public function __construct(
        protected OrderDocumentInterface $orderDocument,
    ) {
    }

    public function getOrderDocument(): OrderDocumentInterface
    {
        return $this->orderDocument;
    }

    public function getOptions(): string
    {
        return $this->options;
    }

    public function setOptions(string $options): void
    {
        $this->options = $options;
    }
}
