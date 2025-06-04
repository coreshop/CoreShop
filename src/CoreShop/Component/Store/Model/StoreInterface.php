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

namespace CoreShop\Component\Store\Model;

use CoreShop\Component\Currency\Model\CurrencyAwareInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface StoreInterface extends ResourceInterface, TimestampableInterface, CurrencyAwareInterface
{
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param string $template
     */
    public function setTemplate($template);

    /**
     * @return bool
     */
    public function getIsDefault();

    /**
     * @param bool $isDefault
     */
    public function setIsDefault($isDefault);

    /**
     * @return int
     */
    public function getSiteId();

    /**
     * @param int $siteId
     */
    public function setSiteId($siteId);
}
