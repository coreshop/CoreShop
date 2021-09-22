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

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\Asset;

interface CarrierInterface extends ResourceInterface, TimestampableInterface, TranslatableInterface
{
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
     * @param string $description
     * @param null $language
     */
    public function setDescription(string $description, ?string $language = null);

    /**
     * @param null $language
     *
     * @return string
     */
    public function getTitle(?string $language = null);

    /**
     * @param string $title
     * @param string|null $language
     */
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
    public function getIsFree();

    /**
     * @param bool $isFree
     */
    public function setIsFree($isFree);

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

    /**
     * @param ShippingRuleGroupInterface $shippingRuleGroup
     */
    public function addShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);

    /**
     * @param ShippingRuleGroupInterface $shippingRuleGroup
     */
    public function removeShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);

    /**
     * @param ShippingRuleGroupInterface $shippingRuleGroup
     *
     * @return bool
     */
    public function hasShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);
}
