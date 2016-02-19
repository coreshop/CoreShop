<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Library\Deposit;

class Compare extends Deposit
{

    /**
     * @var int
     */
    protected $maxCompareElements = 3;

    public function __construct()
    {
        $this->setNamespace('compare')->setLimit($this->maxCompareElements);
    }

    /**
     * @param int $maxElements
     */
    public function setMaxCompareElements(  $maxElements = 3 )
    {
        $this->setLimit( $maxElements );

    }
    /**
     * Calculates the total for the CartItem
     *
     * @return mixed
     */
    public function getCompareList()
    {
        return $this->toArray();
    }

}
