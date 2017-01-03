<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Messaging\Thread;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Messaging\Thread;

/**
 * Class State
 * @package CoreShop\Model\Messaging\Thread
 */
class State extends AbstractModel
{
    /**
     * @var bool
     */
    protected static $isMultiShop = true;

    /**
     * @var array
     */
    protected $localizedValues = ['name'];

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $color;

    /**
     * @var bool
     */
    public $finished;

    /**
     * @var int[]
     */
    public $shopIds;

    /**
     * Return Threads List.
     *
     * @return \CoreShop\Model\Listing\AbstractListing
     *
     * @throws \CoreShop\Exception
     */
    public function getThreadsList()
    {
        $list = Thread::getList();
        $list->setCondition('statusId = ?', [$this->getId()]);

        return $list;
    }

    /**
     * @param string $language language
     *
     * @return string
     */
    public function getName($language = null)
    {
        return $this->getLocalizedFields()->getLocalizedValue('name', $language);
    }

    /**
     * @param string $name
     * @param string $language language
     */
    public function setName($name, $language = null)
    {
        $this->getLocalizedFields()->setLocalizedValue('name', $name, $language);
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return bool
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param bool $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * @return \int[]
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }

    /**
     * @param \int[] $shopIds
     */
    public function setShopIds($shopIds)
    {
        $this->shopIds = $shopIds;
    }
}
