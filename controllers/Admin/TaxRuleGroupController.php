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
 * Class CoreShop_Admin_TaxRuleGroupController
 */
class CoreShop_Admin_TaxRuleGroupController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_tax_rules';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\TaxRuleGroup::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Model\TaxRuleGroup) {
            $model->setActive(1);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $group = \CoreShop\Model\TaxRuleGroup::getById($id);

        if ($data && $group instanceof \CoreShop\Model\TaxRuleGroup) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $group->setValues($data);
            $group->save();

            $taxRules = \Zend_Json::decode($this->getParam('taxRules'));
            $taxRulesUpdated = [];

            foreach ($taxRules as $taxRule) {
                $id = intval($taxRule['id']);
                $taxRuleObject = null;

                unset($taxRule['id']);

                if ($id) {
                    $taxRuleObject = \CoreShop\Model\TaxRule::getById($id);
                }

                if (!$taxRuleObject instanceof \CoreShop\Model\TaxRule) {
                    $taxRuleObject = \CoreShop\Model\TaxRule::create();
                }

                $taxRuleObject->setStateId($taxRule['stateId'] ? $taxRule['stateId'] : 0);
                $taxRuleObject->setCountryId($taxRule['countryId']);
                $taxRuleObject->setTaxId($taxRule['taxId']);
                $taxRuleObject->setBehavior($taxRule['behavior']);
                $taxRuleObject->setTaxRuleGroup($group);
                $taxRuleObject->save();

                $taxRulesUpdated[] = $taxRuleObject->getId();
            }

            $taxRules = $group->getRules();

            foreach ($taxRules as $rule) {
                if (!in_array($rule->getId(), $taxRulesUpdated)) {
                    $rule->delete();
                }
            }

            $this->_helper->json(['success' => true, 'data' => $group]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function listRulesAction()
    {
        $id = $this->getParam('id');
        $group = \CoreShop\Model\TaxRuleGroup::getById($id);

        if ($group instanceof \CoreShop\Model\TaxRuleGroup) {
            $rules = $group->getRules();

            $this->_helper->json(['total' => count($rules), 'data' => $rules]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function addRuleAction()
    {
        $id = $this->getParam('id');
        $group = \CoreShop\Model\TaxRuleGroup::getById($id);

        if ($group instanceof \CoreShop\Model\TaxRuleGroup) {
            $countryId = $this->getParam('countryId');
            $taxId = $this->getParam('taxId');
            $behavior = $this->getParam('behavior');

            $taxRule = \CoreShop\Model\TaxRule::create();
            $taxRule->setCountryId($countryId);
            $taxRule->setTaxId($taxId);
            $taxRule->setBehavior($behavior);
            $taxRule->setTaxRuleGroup($group);
            $taxRule->save();

            $this->_helper->json(['success' => true, 'taxRule' => $taxRule]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }
}
