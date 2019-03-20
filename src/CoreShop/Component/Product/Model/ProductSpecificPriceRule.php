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

class ProductSpecificPriceRule implements ProductSpecificPriceRuleInterface
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
     * @var int
     */
    protected $product;

    /**
     * @var bool
     */
    protected $inherit = false;

    /**
     * @var int
     */
    protected $priority = 0;

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
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInherit()
    {
        return $this->inherit;
    }

    /**
     * {@inheritdoc}
     */
    public function setInherit($inherit)
    {
        $this->inherit = $inherit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

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
     * @return ProductSpecificPriceRuleTranslationInterface
     */
    public function getTranslation($locale = null, $useFallbackTranslation = true)
    {
        /** @var ProductSpecificPriceRuleTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new ProductSpecificPriceRuleTranslation();
    }
}
