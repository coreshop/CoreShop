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

abstract class AbstractPriceRule implements PriceRuleInterface
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

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @var bool
     */
    protected $stopPropagation = false;

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
     * {@inheritdoc}
     */
    public function getStopPropagation()
    {
        return $this->stopPropagation;
    }

    /**
     * {@inheritdoc}
     */
    public function setStopPropagation($stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
    }

    /**
     * @param null $locale
     * @param bool $useFallbackTranslation
     *
     * @return PriceRuleTranslationInterface
     */
    public function getTranslation($locale = null, $useFallbackTranslation = true)
    {
        /** @var ProductPriceRuleTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }
}
