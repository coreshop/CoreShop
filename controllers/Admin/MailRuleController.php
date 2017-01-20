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
 * Class CoreShop_Admin_MailRuleController
 */
class CoreShop_Admin_MailRuleController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_mail_rules';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Mail\Rule::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Model\Mail\Rule) {
            $count = \CoreShop\Model\Mail\Rule::getList()->getCount();

            $model->setSort($count+1);
        }
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     *
     * @param $data
     */
    protected function prepareSave(\CoreShop\Model\AbstractModel $model, $data)
    {
        if($model instanceof \CoreShop\Model\Mail\Rule) {
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
     * @param \CoreShop\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Model\Mail\Rule) {
            return $model->serialize();
        }

        return parent::getReturnValues($model);
    }

    public function getConfigAction()
    {
        $conditions = [];
        $actions = [];

        foreach (\CoreShop\Model\Mail\Rule::$availableTypes as $type) {
            $conditions[$type] = \CoreShop\Model\Mail\Rule::getConditionDispatcherForType($type)->getTypeKeys();
            $actions[$type] = \CoreShop\Model\Mail\Rule::getActionDispatcherForType($type)->getTypeKeys();
        }

        $this->_helper->json([
            'success' => true,
            'types' => \CoreShop\Model\Mail\Rule::$availableTypes,
            'conditions' => $conditions,
            'actions' => $actions,
        ]);
    }

    public function sortAction()
    {
        $rule = $this->getParam("rule");
        $toRule = $this->getParam("toRule");
        $position = $this->getParam("position");

        $rule = \CoreShop\Model\Mail\Rule::getById($rule);
        $toRule = \CoreShop\Model\Mail\Rule::getById($toRule);

        $direction = $rule->getSort() < $toRule->getSort() ? 'down' : 'up';

        if ($direction === 'down') {
            //Update all records in between and move one direction up.

            $fromSort = $rule->getSort()+1;
            $toSort = $toRule->getSort();

            if ($position === 'before') {
                $toSort -= 1;
            }

            $list = new \CoreShop\Model\Mail\Rule\Listing();
            $list->setCondition("sort >= ? AND sort <= ?", [$fromSort, $toSort]);

            foreach ($list->getData() as $newRule) {
                if($newRule instanceof \CoreShop\Model\Mail\Rule) {
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

            $list = new \CoreShop\Model\Mail\Rule\Listing();
            $list->setCondition("sort >= ? AND sort <= ?", [$fromSort, $toSort]);

            foreach ($list->getData() as $newRule) {
                if($newRule instanceof \CoreShop\Model\Mail\Rule) {
                    $newRule->setSort($newRule->getSort() + 1);
                    $newRule->save();
                }
            }

            $rule->setSort($fromSort);
            $rule->save();
        }

        $this->_helper->json(['success' => true]);
    }
}
