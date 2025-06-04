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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;

interface ProductUnitInterface extends ResourceInterface, TranslatableInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(string $name): void;

    public function getFullLabel(?string $language = null): ?string;

    public function setFullLabel(string $fullLabel, ?string $language = null): void;

    public function getFullPluralLabel(?string $language = null): ?string;

    public function setFullPluralLabel(string $fullPluralLabel, ?string $language = null): void;

    public function getShortLabel(?string $language = null): ?string;

    public function setShortLabel(string $shortLabel, ?string $language = null): void;

    public function getShortPluralLabel(?string $language = null): ?string;

    public function setShortPluralLabel(string $shortPluralLabel, ?string $language = null): void;
}
