<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface CarrierTranslationInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @deprecated getLabel is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use getTitle instead
     *
     * @return string
     */
    public function getLabel();

    /**
     * @deprecated setLabel is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use getTitle instead
     *
     * @param string $label
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $label
     */
    public function setTitle($label);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);
}
