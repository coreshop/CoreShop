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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\Filter;

use CoreShop\Model\Dao\AbstractDao;
use Pimcore\Tool\Serialize;

/**
 * Class Dao
 * @package CoreShop\Model\Product\Filter
 */
class Dao extends AbstractDao
{
    protected static $tableName = 'coreshop_product_filters';

    /**
     * Assign variables to model.
     *
     * @param array $data
     */
    protected function assignVariablesToModel($data)
    {
        parent::assignVariablesToModel($data);

        foreach ($data as $key => $value) {
            if ($key == 'filters') {
                $this->model->setFilters(Serialize::unserialize($value));
            } elseif ($key == 'preConditions') {
                $this->model->setPreConditions(Serialize::unserialize($value));
            } elseif ($key == 'similarities') {
                $this->model->setSimilarities(Serialize::unserialize($value));
            }
        }
    }
}
