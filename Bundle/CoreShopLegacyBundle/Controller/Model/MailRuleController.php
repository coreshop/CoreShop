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
 * Class MailRuleController
 *
 * @Route("/mail-rule")
 */
class MailRuleController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_mail_rules';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule) {
            $count = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule::getList()->getCount();

            $model->setSort($count+1);
        }
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     *
     * @param $data
     */
    protected function prepareSave(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model, $data)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule) {
            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $model->setValues($data['settings']);

            $actionInstances = $model->prepareActions($actions);
            $conditionInstances = $model->prepareConditions($conditions);

            $model->setActions($actionInstances);
            $model->setConditions($conditionInstances);
        }
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule) {
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
        $conditions = [];
        $actions = [];

        foreach (\CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule::getTypes() as $type) {
            $conditions[$type] = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule::getConditionDispatcherForType($type)->getTypeKeys();
            $actions[$type] = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule::getActionDispatcherForType($type)->getTypeKeys();
        }

        return $this->json([
            'success' => true,
            'types' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule::getTypes(),
            'conditions' => $conditions,
            'actions' => $actions,
        ]);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/sort")
     */
    public function sortAction(Request $request)
    {
        $rule = $request->get("rule");
        $toRule = $request->get("toRule");
        $position = $request->get("position");

        $rule = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule::getById($rule);
        $toRule = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule::getById($toRule);

        $direction = $rule->getSort() < $toRule->getSort() ? 'down' : 'up';

        if ($direction === 'down') {
            //Update all records in between and move one direction up.

            $fromSort = $rule->getSort()+1;
            $toSort = $toRule->getSort();

            if ($position === 'before') {
                $toSort -= 1;
            }

            $list = new \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule\Listing();
            $list->setCondition("sort >= ? AND sort <= ?", [$fromSort, $toSort]);

            foreach ($list->getData() as $newRule) {
                if($newRule instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule) {
                    $newRule->setSort($newRule->getSort() - 1);
                    $newRule->save();
                }
            }

            $rule->setSort($toSort);
            $rule->save();
        } else {
            //Update all records in between and move one direction down.

            $fromSort = $toRule->getSort();
            $toSort = $rule->getSort();

            $list = new \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule\Listing();
            $list->setCondition("sort >= ? AND sort <= ?", [$fromSort, $toSort]);

            foreach ($list->getData() as $newRule) {
                if($newRule instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule) {
                    $newRule->setSort($newRule->getSort() + 1);
                    $newRule->save();
                }
            }

            $rule->setSort($fromSort);
            $rule->save();
        }

        return $this->json(['success' => true]);
    }
}
