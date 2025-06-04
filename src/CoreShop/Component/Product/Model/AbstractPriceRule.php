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

use CoreShop\Component\Resource\Model\TranslatableTrait;
use CoreShop\Component\Rule\Model\RuleTrait;

abstract class AbstractPriceRule implements PriceRuleInterface
{
    use RuleTrait  {
        initializeRuleCollections as private initializeRules;
    }
    use TranslatableTrait {
        initializeTranslationCollection as private initializeTranslationsCollection;

        getTranslation as private doGetTranslation;
    }

    /**
     * @var int|null
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

    public function __construct(
        ) {
        $this->initializeRules();
        $this->initializeTranslationsCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    public function getLabel(?string $language = null)
    {
        return $this->getTranslation($language)->getLabel();
    }

    public function setLabel(string $label, ?string $language = null)
    {
        $this->getTranslation($language)->setLabel($label);
    }

    public function getStopPropagation()
    {
        return $this->stopPropagation;
    }

    public function setStopPropagation($stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
    }

    public function getTranslation(?string $locale = null, bool $useFallbackTranslation = true): PriceRuleTranslationInterface
    {
        /** @var ProductPriceRuleTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }
}
