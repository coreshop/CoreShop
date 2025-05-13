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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\TranslationInterface;

interface ProductUnitTranslationInterface extends ResourceInterface, TimestampableInterface, TranslationInterface
{
    public function getId(): ?int;

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
