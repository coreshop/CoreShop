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

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;

interface TaxRateInterface extends ResourceInterface, TranslatableInterface, TimestampableInterface, ToggleableInterface
{
    /**
     * @return string
     */
    public function getName(?string $language = null);

    public function setName(string $name, ?string $language = null);

    /**
     * @return float
     */
    public function getRate();

    /**
     * @param float $rate
     */
    public function setRate($rate);
}
