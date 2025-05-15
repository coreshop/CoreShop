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

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\Asset;

interface CarrierInterface extends ResourceInterface, TimestampableInterface, TranslatableInterface
{
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * @param null $language
     *
     * @return string
     */
    public function getDescription(?string $language = null);

    /**
     * @param null $language
     */
    public function setDescription(string $description, ?string $language = null);

    /**
     * @param null $language
     *
     * @return string
     */
    public function getTitle(?string $language = null);

    public function setTitle(string $title, ?string $language = null);

    /**
     * @return string
     */
    public function getTrackingUrl();

    /**
     * @param string $trackingUrl
     */
    public function setTrackingUrl($trackingUrl);

    /**
     * @return bool
     */
    public function getHideFromCheckout();

    /**
     * @param bool $hideFromCheckout
     */
    public function setHideFromCheckout($hideFromCheckout);

    /**
     * @return Asset|null
     */
    public function getLogo();

    /**
     * @param Asset $logo
     */
    public function setLogo($logo);

    /**
     * @return string|null
     */
    public function getTaxCalculationStrategy();

    /**
     * @param string $taxCalculationStrategy
     */
    public function setTaxCalculationStrategy($taxCalculationStrategy);

    /**
     * @return Collection|ShippingRuleGroupInterface[]
     */
    public function getShippingRules();

    /**
     * @return bool
     */
    public function hasShippingRules();

    public function addShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);

    public function removeShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);

    /**
     * @return bool
     */
    public function hasShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);
}
