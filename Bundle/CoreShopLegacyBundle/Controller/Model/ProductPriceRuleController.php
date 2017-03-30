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
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductPriceRuleController
 *
 * @Route("/product-price-rule")
 */
class ProductPriceRuleController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_product_price_rules';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\PriceRule::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\PriceRule) {
            $model->setActive(0);
        }
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @param $data
     */
    protected function prepareSave(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model, $data) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\PriceRule) {
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
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\PriceRule) {
            return $model->serialize();
        }

        return parent::getReturnValues($model);
    }

    /**
     * @Route("/get-config")
     */
    public function getConfigAction()
    {
        return $this->json([
            'success' => true,
            'conditions' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\PriceRule::getConditionDispatcher()->getTypeKeys(),
            'actions' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\PriceRule::getActionDispatcher()->getTypeKeys()
        ]);
    }
}
