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

namespace CoreShop\Behat\Model\Index;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class TestEnableIndex extends AbstractPimcoreModel implements IndexableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIndexable()
    {
        return $this->getEnabled() && $this->getPublished();
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
}
