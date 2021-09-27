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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;

interface ProductUnitInterface extends ResourceInterface, TranslatableInterface, TimestampableInterface
{
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
