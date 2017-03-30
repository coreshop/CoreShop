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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule\VoucherCode;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Dao\AbstractDao;
use Pimcore\Date;

/**
 * Class Dao
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule\VoucherCode
 */
class Dao extends AbstractDao
{
    /**
     * Mysql table name.
     *
     * @var string
     */
    protected static $tableName = 'coreshop_voucher_codes';

    /**
     * Assign Variables to Model.
     *
     * @param array $data
     */
    protected function assignVariablesToModel($data)
    {
        parent::assignVariablesToModel($data);

        foreach ($data as $key => $value) {
            if ($key == 'creationDate') {
                $value = new Date($value);

                $this->model->setCreationDate($value);
            }
        }
    }
}
