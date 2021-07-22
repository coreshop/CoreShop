<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;

class TaxRate extends AbstractResource implements TaxRateInterface
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
     * @var float
     */
    protected $rate = 0;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->getName('en'), $this->getId());
    }

    public function getName($language = null)
    {
        return $this->getTranslation($language)->getName();
    }

    public function setName($name, $language = null)
    {
        $this->getTranslation($language, false)->setName($name);
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @param null $locale
     * @param bool $useFallbackTranslation
     *
     * @return TaxRateTranslation
     */
    public function getTranslation($locale = null, $useFallbackTranslation = true)
    {
        /** @var TaxRateTranslation $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    protected function createTranslation()
    {
        return new TaxRateTranslation();
    }
}
