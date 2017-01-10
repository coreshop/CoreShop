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

namespace CoreShop\Model\Index;

use CoreShop\Model;

/**
 * Class Config
 * @package CoreShop\Model\Index
 */
class Config
{
    /**
     * @var Model\Index\Config\Column[]
     */
    public $columns;

    /**
     * @return Model\Index\Config\Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param Model\Index\Config\Column[] $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'type') {
                continue;
            }

            $setter = 'set'.ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}
