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

namespace CoreShop\Component\Resource\Model;

trait ImmutableTrait
{
    /**
     * @var bool|null
     */
    protected $immutable = false;

    public function getImmutable(): ?bool
    {
        return $this->immutable;
    }

    public function setImmutable(?bool $immutable)
    {
        $this->immutable = $immutable;
    }

    public function isImmutable(): bool
    {
        return (bool) $this->immutable;
    }
}
