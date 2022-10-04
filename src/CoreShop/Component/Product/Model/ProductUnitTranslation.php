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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractTranslation;
use CoreShop\Component\Resource\Model\TimestampableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductUnitTranslation extends AbstractTranslation implements ProductUnitTranslationInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $fullLabel;

    /**
     * @var string
     */
    protected $fullPluralLabel;

    /**
     * @var string
     */
    protected $shortLabel;

    /**
     * @var string
     */
    protected $shortPluralLabel;

    public function getId()
    {
        return $this->id;
    }

    public function getFullLabel()
    {
        return $this->fullLabel;
    }

    /**
     * @return void
     */
    public function setFullLabel($fullLabel)
    {
        $this->fullLabel = $fullLabel;
    }

    public function getFullPluralLabel()
    {
        return $this->fullPluralLabel;
    }

    /**
     * @return void
     */
    public function setFullPluralLabel($fullPluralLabel)
    {
        $this->fullPluralLabel = $fullPluralLabel;
    }

    public function getShortLabel()
    {
        return $this->shortLabel;
    }

    /**
     * @return void
     */
    public function setShortLabel($shortLabel)
    {
        $this->shortLabel = $shortLabel;
    }

    public function getShortPluralLabel()
    {
        return $this->shortPluralLabel;
    }

    /**
     * @return void
     */
    public function setShortPluralLabel($shortPluralLabel)
    {
        $this->shortPluralLabel = $shortPluralLabel;
    }
}
