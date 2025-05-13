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

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class State extends AbstractResource implements StateInterface, \Stringable
{
    use ToggleableTrait;
    use TimestampableTrait;
    use TranslatableTrait {
        TranslatableTrait::__construct as private initializeTranslationsCollection;

        TranslatableTrait::getTranslation as private doGetTranslation;
    }

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $isoCode;

    /**
     * @var CountryInterface
     */
    protected $country;

    public function __construct(
        ) {
        $this->initializeTranslationsCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getName(), $this->getId());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsoCode()
    {
        return $this->isoCode;
    }

    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    public function getName(?string $language = null)
    {
        return $this->getTranslation($language)->getName();
    }

    public function setName($name, ?string $language = null)
    {
        $this->getTranslation($language, false)->setName($name);

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(CountryInterface $country)
    {
        $this->country = $country;

        return $this;
    }

    public function getCountryName()
    {
        return $this->getCountry() instanceof CountryInterface ? $this->getCountry()->getName() : '';
    }

    public function getTranslation(?string $locale = null, bool $useFallbackTranslation = true): StateTranslationInterface
    {
        /** @var StateTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    protected function createTranslation(): StateTranslationInterface
    {
        return new StateTranslation();
    }
}
