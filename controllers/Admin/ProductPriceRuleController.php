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
 * Class CoreShop_Admin_ProductPriceRuleController
 */
class CoreShop_Admin_ProductPriceRuleController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_product_price_rules';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Product\PriceRule::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Model\Product\PriceRule) {
            $model->setActive(0);
        }
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $data
     */
    protected function prepareSave(\CoreShop\Model\AbstractModel $model, $data) {
        if($model instanceof \CoreShop\Model\Product\PriceRule) {
            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $conditionInstances = $model->prepareConditions($conditions);
            $actionInstances = $model->prepareActions($actions);

            $model->setValues($data['settings']);
            $model->setActions($actionInstances);
            $model->setConditions($conditionInstances);


            \Pimcore\Cache::clearTag('coreshop_product_price');
        }
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Model\Product\PriceRule) {
            return $model->serialize();
        }

        return parent::getReturnValues($model);
    }

    public function getConfigAction()
    {
        $this->_helper->json([
            'success' => true,
            'conditions' => \CoreShop\Model\Product\PriceRule::getConditionDispatcher()->getTypeKeys(),
            'actions' => \CoreShop\Model\Product\PriceRule::getActionDispatcher()->getTypeKeys()
        ]);
    }
}
