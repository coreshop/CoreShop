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
 * Originally derived from pimcore/google-marketing-bundle (POCL).
 */

namespace CoreShop\Bundle\TrackingBundle\GoogleMarketing\Code;

final class CodeBlock
{
    /**
     * @var array<int, string>
     */
    private array $parts;

    /**
     * @param array<int, string> $parts
     */
    public function __construct(array $parts = [])
    {
        $this->parts = $parts;
    }

    /**
     * @param array<int, string> $parts
     */
    public function setParts(array $parts): void
    {
        $this->parts = $parts;
    }

    /**
     * @return array<int, string>
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param array<int, string>|string $parts
     */
    public function append(array|string $parts): void
    {
        $parts = (array) $parts;

        foreach ($parts as $part) {
            $this->parts[] = $part;
        }
    }

    /**
     * @param array<int, string>|string $parts
     */
    public function prepend(array|string $parts): void
    {
        $parts = (array) $parts;
        $parts = array_reverse($parts);

        foreach ($parts as $part) {
            array_unshift($this->parts, $part);
        }
    }

    public function asString(): string
    {
        return trim(implode("\n", $this->parts));
    }

    public function __toString(): string
    {
        return $this->asString();
    }
}
