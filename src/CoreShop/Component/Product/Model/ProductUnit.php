<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;

class ProductUnit extends AbstractResource implements ProductUnitInterface
{
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
    protected $name;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullLabel($language = null)
    {
        return $this->getTranslation($language)->getFullLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function setFullLabel($fullLabel, $language = null)
    {
        $this->getTranslation($language, false)->setFullLabel($fullLabel);
    }

    /**
     * {@inheritdoc}
     */
    public function getFullPluralLabel($language = null)
    {
        return $this->getTranslation($language)->getFullPluralLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function setFullPluralLabel($fullPluralLabel, $language = null)
    {
        $this->getTranslation($language, false)->setFullPluralLabel($fullPluralLabel);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortLabel($language = null)
    {
        return $this->getTranslation($language)->getShortLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function setShortLabel($shortLabel, $language = null)
    {
        $this->getTranslation($language, false)->setShortLabel($shortLabel);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortPluralLabel($language = null)
    {
        return $this->getTranslation($language)->getShortPluralLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function setShortPluralLabel($shortPluralLabel, $language = null)
    {
        $this->getTranslation($language, false)->setShortPluralLabel($shortPluralLabel);
    }

    /**
     * @param null $locale
     * @param bool $useFallbackTranslation
     *
     * @return ProductUnitTranslationInterface
     */
    public function getTranslation($locale = null, $useFallbackTranslation = true)
    {
        /** @var ProductUnitTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s (%d)', $this->getName(), $this->getId());
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new ProductUnitTranslation();
    }
}
