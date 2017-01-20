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
 * Class CoreShop_Admin_TaxController
 */
class CoreShop_Admin_TaxController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_taxes';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Tax::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Model\Tax) {
            $model->setRate(0);
            $model->setActive(1);
        }
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $config
     * @return mixed
     */
    protected function prepareTreeNodeConfig(\CoreShop\Model\AbstractModel $model, $config)
    {
        if($model instanceof \CoreShop\Model\Tax) {
            $config['rate'] = $model->getRate();
        }

        return $config;
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     *
     * @return array
     */
    protected function getReturnValues(\CoreShop\Model\AbstractModel $model)
    {
        $values = parent::getReturnValues($model);

        if($model instanceof \CoreShop\Model\Tax) {
            $values['title'] = $model->getName();
        }

        return $values;
    }
}
