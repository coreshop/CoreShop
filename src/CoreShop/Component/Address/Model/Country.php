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

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class Country extends AbstractResource implements CountryInterface, \Stringable
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
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * @var Collection|StateInterface[]
     */
    protected $states;

    /**
     * @var string
     */
    protected $addressFormat = '';

    /**
     * @var array
     */
    protected $salutations = [];

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function __toString(): string
    {
        return (string)$this->getName();
    }

    public function getId()
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

    public function getName($language = null)
    {
        return $this->getTranslation($language)->getName();
    }

    public function setName($name, $language = null)
    {
        $this->getTranslation($language, false)->setName($name);

        return $this;
    }

    public function getAddressFormat()
    {
        return $this->addressFormat;
    }

    public function setAddressFormat($addressFormat)
    {
        $this->addressFormat = $addressFormat;

        return $this;
    }

    public function getSalutations()
    {
        return $this->salutations;
    }

    public function setSalutations($salutations)
    {
        $this->salutations = $salutations;

        return $this;
    }

    public function getZone()
    {
        return $this->zone;
    }

    public function setZone(ZoneInterface $zone = null)
    {
        $this->zone = $zone;

        return $this;
    }

    public function getZoneName()
    {
        return $this->getZone() instanceof ZoneInterface ? $this->getZone()->getName() : '';
    }

    public function getTranslation(?string $locale = null, bool $useFallbackTranslation = true): CountryTranslationInterface
    {
        /** @var CountryTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    protected function createTranslation(): CountryTranslationInterface
    {
        return new CountryTranslation();
    }
}
