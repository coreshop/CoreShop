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

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\Asset;

interface PaymentProviderInterface extends
    ResourceInterface,
    ToggleableInterface,
    TranslatableInterface,
    TimestampableInterface
{
    public function getId(): ?int;

    /**
     * @return mixed
     */
    public function getIdentifier();

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * @return string
     */
    public function getTitle(?string $language = null);

    /**
     * @param string $title
     */
    public function setTitle($title, ?string $language = null);

    /**
     * @return string
     */
    public function getDescription(?string $language = null);

    /**
     * @param string $description
     */
    public function setDescription($description, ?string $language = null);

    /**
     * @return string
     */
    public function getInstructions(?string $language = null);

    /**
     * @param string $instructions
     */
    public function setInstructions($instructions, ?string $language = null);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return Asset|null
     */
    public function getLogo();

    /**
     * @param Asset $logo
     */
    public function setLogo($logo);

    /**
     * @return Collection|PaymentProviderRuleGroupInterface[]
     */
    public function getPaymentProviderRules();

    /**
     * @return bool
     */
    public function hasPaymentProviderRules();

    /**
     * @return bool
     */
    public function hasPaymentProviderRule(PaymentProviderRuleGroupInterface $paymentProviderRuleGroup);

    public function addPaymentProviderRule(PaymentProviderRuleGroupInterface $paymentProviderRuleGroup);

    public function removePaymentProviderRule(PaymentProviderRuleGroupInterface $paymentProviderRuleGroup);
}
