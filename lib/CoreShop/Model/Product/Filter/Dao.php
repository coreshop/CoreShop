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

namespace CoreShop\Model\Product\Filter;

use CoreShop\Model\Dao\AbstractDao;

class Dao extends AbstractDao
{
    protected $tableName = 'coreshop_product_filters';

    /**
     * @param array $data
     */
    protected function assignVariablesToModel($data)
    {
        parent::assignVariablesToModel($data);

        foreach ($data as $key=>$value) {
            if ($key == "filters") {
                $this->model->setFilters(unserialize($value));
            } elseif ($key == "preConditions") {
                $this->model->setPreConditions(unserialize($value));
            }
        }
    }
}
