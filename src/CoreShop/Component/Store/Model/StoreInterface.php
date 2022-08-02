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

namespace CoreShop\Component\Store\Model;

use CoreShop\Component\Currency\Model\CurrencyAwareInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface StoreInterface extends ResourceInterface, TimestampableInterface, CurrencyAwareInterface
{
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
