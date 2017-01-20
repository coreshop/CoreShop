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

use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_ZoneController
 */
class CoreShop_Admin_ZoneController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_zones';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Zone::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Model\Zone) {
            $model->setActive(false);
        }
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $config
     * @return mixed
     */
    protected function prepareTreeNodeConfig(\CoreShop\Model\AbstractModel $model, $config)
    {
        if($model instanceof \CoreShop\Model\Zone) {
            $config['active'] = intval($model->getActive());
        }

        return $config;
    }
}
