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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface ProductUnitTranslationInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getFullLabel();

    /**
     * @param string $fullLabel
     */
    public function setFullLabel($fullLabel);

    /**
     * @return string
     */
    public function getFullPluralLabel();

    /**
     * @param string $fullPluralLabel
     */
    public function setFullPluralLabel($fullPluralLabel);

    /**
     * @return string
     */
    public function getShortLabel();

    /**
     * @param string $shortLabel
     */
    public function setShortLabel($shortLabel);

    /**
     * @return string
     */
    public function getShortPluralLabel();

    /**
     * @param string $shortPluralLabel
     */
    public function setShortPluralLabel($shortPluralLabel);
}
