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

namespace CoreShop\Model\Index\Config\Column\Mysql\Objectbricks;

use CoreShop\Model\Index\Config\Column\Mysql\Objectbricks as DefaultObjectbricks;

class CoreShopDimensionTest extends DefaultObjectbricks {

    public $brickField2;

    /**
     * @return mixed
     */
    public function getBrickField2()
    {
        return $this->brickField2;
    }

    /**
     * @param mixed $brickField2
     */
    public function setBrickField2($brickField2)
    {
        $this->brickField2 = $brickField2;
    }
}