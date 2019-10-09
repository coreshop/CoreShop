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
    public function getDescription($language = null);

    /**
     * @param string $description
     * @param null $language
     */
    public function setDescription($description, $language = null);

    /**
     * @param null $language
     *
     * @return string
     */
    public function getTitle($language = null);

    /**
     * @param string $title
     * @param null $language
     */
    public function setTitle($title, $language = null);

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
     * @return Asset|null
     */
    public function getLogo();

    /**
     * @param Asset $logo
     */
    public function setLogo($logo);

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
