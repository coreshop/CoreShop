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
 * Class CoreShop_Admin_CarrierController
 */
class CoreShop_Admin_CarrierController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_carriers';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Carrier::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Model\Carrier) {
            $model->setLabel($model->getName());
            $model->setGrade(1);
            $model->setIsFree(0);
            $model->setRangeBehaviour('largest');
        }
    }

    public function getShippingRuleGroupsAction()
    {
        $id = $this->getParam('carrier');
        $carrier = \CoreShop\Model\Carrier::getById($id);

        if ($carrier instanceof \CoreShop\Model\Carrier) {
            $groups = $carrier->getShippingRuleGroups();

            $this->_helper->json(['success' => true, 'total' => count($groups), 'data' => $groups]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $carrier = \CoreShop\Model\Carrier::getById($id);

        if ($data && $carrier instanceof \CoreShop\Model\Carrier) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $oldGroups = $carrier->getShippingRuleGroups();

            foreach ($oldGroups as $group) {
                $group->delete();
            }

            $carrier->setValues($data['settings']);

            foreach ($data['groups'] as $group) {
                $obj = CoreShop\Model\Carrier\ShippingRuleGroup::create();
                $obj->setCarrier($carrier);
                $obj->setPriority($group['priority']);
                $obj->setShippingRuleId($group['shippingRuleId']);
                $obj->save();
            }

            $carrier->save();

            $this->_helper->json(['success' => true, 'data' => $carrier, 'shippingRuleGroups' => $carrier->getShippingRuleGroups()]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }
}
