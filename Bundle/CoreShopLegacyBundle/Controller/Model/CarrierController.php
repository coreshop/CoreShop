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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CarrierController
 *
 * @Route("/carrier")
 */
class CarrierController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_carriers';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier) {
            $model->setLabel($model->getName());
            $model->setGrade(1);
            $model->setIsFree(0);
            $model->setRangeBehaviour('largest');
        }
    }

    /**
     * @Route("/get-shipping-rule-groups")
     */
    public function getShippingRuleGroupsAction(Request $request)
    {
        $id = $request->get('carrier');
        $carrier = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier::getById($id);

        if ($carrier instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier) {
            $groups = $carrier->getShippingRuleGroups();

            return $this->json(['success' => true, 'total' => count($groups), 'data' => $groups]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @Route("/save")
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $data = $request->get('data');
        $carrier = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier::getById($id);

        if ($data && $carrier instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier) {
            $data = \Zend_Json::decode($request->get('data'));

            $oldGroups = $carrier->getShippingRuleGroups();

            foreach ($oldGroups as $group) {
                $group->delete();
            }

            $carrier->setValues($data['settings']);

            foreach ($data['groups'] as $group) {
                $obj = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRuleGroup::create();
                $obj->setCarrier($carrier);
                $obj->setPriority($group['priority']);
                $obj->setShippingRuleId($group['shippingRuleId']);
                $obj->save();
            }

            $carrier->save();

            return $this->json(['success' => true, 'data' => $carrier, 'shippingRuleGroups' => $carrier->getShippingRuleGroups()]);
        } else {
            return $this->json(['success' => false]);
        }
    }
}
