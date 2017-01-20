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
 * Class CoreShop_Admin_CarrierShippingRuleController
 */
class CoreShop_Admin_CarrierShippingRuleController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_carriers';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Carrier\ShippingRule::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $data
     */
    protected function prepareSave(\CoreShop\Model\AbstractModel $model, $data) {
        if($model instanceof \CoreShop\Model\Carrier\ShippingRule) {
            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $actionInstances = $model->prepareActions($actions);
            $conditionInstances = $model->prepareConditions($conditions);

            $model->setValues($data['settings']);
            $model->setActions($actionInstances);
            $model->setConditions($conditionInstances);
        }
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Model\Carrier\ShippingRule) {
            return $model->serialize();
        }

        return parent::getReturnValues($model);
    }

    public function getConfigAction()
    {
        $this->_helper->json([
            'success' => true,
            'conditions' => \CoreShop\Model\Carrier\ShippingRule::getConditionDispatcher()->getTypeKeys(),
            'actions' => \CoreShop\Model\Carrier\ShippingRule::getActionDispatcher()->getTypeKeys(),
        ]);
    }

    public function getUsedByCarriersAction()
    {
        $id = $this->getParam('id');
        $shippingRule = \CoreShop\Model\Carrier\ShippingRule::getById($id);

        if ($shippingRule instanceof \CoreShop\Model\Carrier\ShippingRule) {
            $list = \CoreShop\Model\Carrier\ShippingRuleGroup::getList();
            $list->setCondition("shippingRuleId = ?", [$id]);
            $list->load();

            $carriers = [];

            foreach ($list->getData() as $group) {
                if ($group instanceof \CoreShop\Model\Carrier\ShippingRuleGroup) {
                    $carrier = $group->getCarrier();

                    if ($carrier instanceof \CoreShop\Model\Carrier) {
                        $carriers[] = [
                            "id" => $carrier->getId(),
                            "name" => $carrier->getName()
                        ];
                    }
                }
            }

            $this->_helper->json(['success' => true, 'carriers' => $carriers]);
        }

        $this->_helper->json(['success' => false]);
    }
}
