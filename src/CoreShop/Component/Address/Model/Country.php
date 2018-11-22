<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;
use Doctrine\Common\Collections\Collection;

class Country extends AbstractResource implements CountryInterface
{
    use ToggleableTrait;
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language = null)
    {
        return $this->getTranslation($language)->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name, $language = null)
    {
        $this->getTranslation($language, false)->setName($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddressFormat()
    {
        return $this->addressFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddressFormat($addressFormat)
    {
        $this->addressFormat = $addressFormat;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalutations()
    {
        return $this->salutations;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalutations($salutations)
    {
        $this->salutations = $salutations;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone(ZoneInterface $zone = null)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getZoneName()
    {
        return $this->getZone() instanceof ZoneInterface ? $this->getZone()->getName() : '';
    }

    /**
     * @param null $locale
     * @param bool $useFallbackTranslation
     *
     * @return CountryTranslationInterface
     */
    public function getTranslation($locale = null, $useFallbackTranslation = true)
    {
        /** @var CountryTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new CountryTranslation();
    }
}
