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

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductUnit extends AbstractResource implements ProductUnitInterface, \Stringable
{
    use TimestampableTrait;
    use TranslatableTrait {
        TranslatableTrait::__construct as private initializeTranslationsCollection;

        TranslatableTrait::getTranslation as private doGetTranslation;
    }

    /**
     * @var int
     */
    protected $id;

    protected ?string $name = null;

    public function __construct(
        ) {
        $this->initializeTranslationsCollection();
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFullLabel(?string $language = null): ?string
    {
        return $this->getTranslation($language)->getFullLabel();
    }

    public function setFullLabel(string $fullLabel, ?string $language = null): void
    {
        $this->getTranslation($language, false)->setFullLabel($fullLabel);
    }

    public function getFullPluralLabel(?string $language = null): ?string
    {
        return $this->getTranslation($language)->getFullPluralLabel();
    }

    public function setFullPluralLabel(string $fullPluralLabel, ?string $language = null): void
    {
        $this->getTranslation($language, false)->setFullPluralLabel($fullPluralLabel);
    }

    public function getShortLabel(?string $language = null): ?string
    {
        return $this->getTranslation($language)->getShortLabel();
    }

    public function setShortLabel(string $shortLabel, ?string $language = null): void
    {
        $this->getTranslation($language, false)->setShortLabel($shortLabel);
    }

    public function getShortPluralLabel(?string $language = null): ?string
    {
        return $this->getTranslation($language)->getShortPluralLabel();
    }

    public function setShortPluralLabel(string $shortPluralLabel, ?string $language = null): void
    {
        $this->getTranslation($language, false)->setShortPluralLabel($shortPluralLabel);
    }

    public function getTranslation(?string $locale = null, bool $useFallbackTranslation = true): ProductUnitTranslationInterface
    {
        /** @var ProductUnitTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    public function __toString(): string
    {
        return sprintf('%s (%d)', $this->getName(), $this->getId());
    }

    protected function createTranslation(): ProductUnitTranslationInterface
    {
        return new ProductUnitTranslation();
    }
}
