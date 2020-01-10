<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;

interface ProductUnitInterface extends ResourceInterface, TranslatableInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $identifier
     */
    public function setName(string $identifier);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getFullLabel($language = null);

    /**
     * @param string $fullLabel
     * @param string $language
     */
    public function setFullLabel($fullLabel, $language = null);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getFullPluralLabel($language = null);

    /**
     * @param string $fullPluralLabel
     * @param string $language
     */
    public function setFullPluralLabel($fullPluralLabel, $language = null);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getShortLabel($language = null);

    /**
     * @param string $shortLabel
     * @param string $language
     */
    public function setShortLabel($shortLabel, $language = null);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getShortPluralLabel($language = null);

    /**
     * @param string $shortPluralLabel
     * @param string $language
     */
    public function setShortPluralLabel($shortPluralLabel, $language = null);
}
