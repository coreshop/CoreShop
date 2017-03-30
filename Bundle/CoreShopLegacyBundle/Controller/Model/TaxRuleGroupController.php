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
 * Class TaxRuleGroupController
 *
 * @Route("/tax-rule-group")
 */
class TaxRuleGroupController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_tax_rules';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRuleGroup::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRuleGroup) {
            $model->setActive(1);
        }
    }

    /**
     * @Route("/save")
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $data = $request->get('data');
        $group = \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRuleGroup::getById($id);

        if ($data && $group instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRuleGroup) {
            $data = \Zend_Json::decode($request->get('data'));

            $group->setValues($data);
            $group->save();

            $taxRules = \Zend_Json::decode($request->get('taxRules'));
            $taxRulesUpdated = [];

            foreach ($taxRules as $taxRule) {
                $id = intval($taxRule['id']);
                $taxRuleObject = null;

                unset($taxRule['id']);

                if ($id) {
                    $taxRuleObject = \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRule::getById($id);
                }

                if (!$taxRuleObject instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRule) {
                    $taxRuleObject = \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRule::create();
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

            return $this->json(['success' => true, 'data' => $group]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @Route("/list-rules")
     */
    public function listRulesAction(Request $request)
    {
        $id = $request->get('id');
        $group = \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRuleGroup::getById($id);

        if ($group instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRuleGroup) {
            $rules = $group->getRules();

            return $this->json(['total' => count($rules), 'data' => $rules]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @Route("/add-rule")
     */
    public function addRuleAction(Request $request)
    {
        $id = $request->get('id');
        $group = \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRuleGroup::getById($id);

        if ($group instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRuleGroup) {
            $countryId = $request->get('countryId');
            $taxId = $request->get('taxId');
            $behavior = $request->get('behavior');

            $taxRule = \CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRule::create();
            $taxRule->setCountryId($countryId);
            $taxRule->setTaxId($taxId);
            $taxRule->setBehavior($behavior);
            $taxRule->setTaxRuleGroup($group);
            $taxRule->save();

            return $this->json(['success' => true, 'taxRule' => $taxRule]);
        } else {
            return $this->json(['success' => false]);
        }
    }
}
