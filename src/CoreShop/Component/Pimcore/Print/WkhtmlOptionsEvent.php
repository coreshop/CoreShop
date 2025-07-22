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

namespace CoreShop\Component\Pimcore\Print;

use Symfony\Contracts\EventDispatcher\Event;

final class WkhtmlOptionsEvent extends Event
{
    protected string $options;

    public function __construct(
        public readonly PrintableInterface $printable,
    ) {
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
