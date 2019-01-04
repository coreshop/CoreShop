<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Model\Index;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class TestIndexFields extends AbstractPimcoreModel implements IndexableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIndexable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexableEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexableName($language)
    {
        return $this->getName($language);
    }

    /**
     * @return int
     */
    public function getIntegerValue()
    {
        return 123;
    }

    /**
     * @return string
     */
    public function getStringValue()
    {
        return 'CoreShop';
    }

    /**
     * @return bool
     */
    public function getBooleanValue()
    {
        return false;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeValue()
    {
        return new \DateTime();
    }

    /**
     * @return float
     */
    public function getDecimalValue()
    {
        return 123.123;
    }

    /**
     * @return string
     */
    public function getTextValue()
    {
        return 'CoreShop <3 Pimcore';
    }
}
