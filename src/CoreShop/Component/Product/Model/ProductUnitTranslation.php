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

use CoreShop\Component\Resource\Model\AbstractTranslation;
use CoreShop\Component\Resource\Model\TimestampableTrait;

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

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullLabel()
    {
        return $this->fullLabel;
    }

    /**
     * {@inheritdoc}
     */
    public function setFullLabel($fullLabel)
    {
        $this->fullLabel = $fullLabel;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullPluralLabel()
    {
        return $this->fullPluralLabel;
    }

    /**
     * {@inheritdoc}
     */
    public function setFullPluralLabel($fullPluralLabel)
    {
        $this->fullPluralLabel = $fullPluralLabel;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortLabel()
    {
        return $this->shortLabel;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function setShortPluralLabel($shortPluralLabel)
    {
        $this->shortPluralLabel = $shortPluralLabel;
    }
}
