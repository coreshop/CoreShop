<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\TranslatableTrait;
use CoreShop\Component\Rule\Model\RuleTrait;

class ProductPriceRule implements ProductPriceRuleInterface
{
    use RuleTrait;
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
    protected $description;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($language = null)
    {
        return $this->getTranslation($language)->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label, $language = null)
    {
        $this->getTranslation($language)->setLabel($label);
    }

    /**
     * @param null $locale
     * @param bool $useFallbackTranslation
     *
     * @return ProductPriceRuleTranslationInterface
     */
    public function getTranslation($locale = null, $useFallbackTranslation = true)
    {
        /** @var ProductPriceRuleTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new ProductPriceRuleTranslation();
    }
}
