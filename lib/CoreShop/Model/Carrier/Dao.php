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

namespace CoreShop\Model\Carrier;

use CoreShop\Model\Dao\AbstractDao;
use Pimcore\Model\Asset;

class Dao extends AbstractDao
{
    protected $tableName = 'coreshop_carriers';

    /**
     * @param array $data
     */
    protected function assignVariablesToModel($data)
    {
        parent::assignVariablesToModel($data);

        foreach ($data as $key=>$value) {
            if ($key == "image") {
                $asset = Asset::getById($value);

                if ($asset instanceof Asset) {
                    $this->model->setImage($asset->getFullPath());
                }
            }
        }
    }
}
