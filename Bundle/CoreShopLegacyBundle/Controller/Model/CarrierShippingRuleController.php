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
 * Class CarrierShippingRuleController
 *
 * @Route("/carrier-shipping-rule")
 */
class CarrierShippingRuleController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_carriers';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @param $data
     */
    protected function prepareSave(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model, $data) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule) {
            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $actionInstances = $model->prepareActions($actions);
            $conditionInstances = $model->prepareConditions($conditions);

            $model->setValues($data['settings']);
            $model->setActions($actionInstances);
            $model->setConditions($conditionInstances);

            \Pimcore\Cache::clearTag('coreshop_product_price');
        }
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule) {
            return $model->serialize();
        }

        return parent::getReturnValues($model);
    }

    /**
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-config")
     */
    public function getConfigAction()
    {
        return $this->json([
            'success' => true,
            'conditions' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule::getConditionDispatcher()->getTypeKeys(),
            'actions' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule::getActionDispatcher()->getTypeKeys(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-used-by-carriers")
     */
    public function getUsedByCarriersAction(Request $request)
    {
        $id = $request->get('id');
        $shippingRule = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule::getById($id);

        if ($shippingRule instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule) {
            $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRuleGroup::getList();
            $list->setCondition("shippingRuleId = ?", [$id]);
            $list->load();

            $carriers = [];

            foreach ($list->getData() as $group) {
                if ($group instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRuleGroup) {
                    $carrier = $group->getCarrier();

                    if ($carrier instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier) {
                        $carriers[] = [
                            "id" => $carrier->getId(),
                            "name" => $carrier->getName()
                        ];
                    }
                }
            }

            return $this->json(['success' => true, 'carriers' => $carriers]);
        }

        return $this->json(['success' => false]);
    }
}
