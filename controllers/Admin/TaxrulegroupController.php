<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\TaxRuleGroup;
use Pimcore\Controller\Action\Admin;
use Pimcore\Tool as PimTool;

class CoreShop_Admin_TaxrulegroupController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array("list");
        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_tax_rules");
        }
    }

    public function listAction()
    {
        $list = new TaxRuleGroup\Listing();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $group) {
                $data[] = $this->getTreeNodeConfig($group);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(TaxRuleGroup $group)
    {
        $tmp = array(
            "id" => $group->getId(),
            "text" => $group->getName(),
            "elementType" => "tax",
            "qtipCfg" => array(
                "title" => "ID: " . $group->getId()
            ),
            "name" => $group->getName()
        );

        $tmp["leaf"] = true;
        $tmp["iconCls"] = "coreshop_icon_tax_rule_groups";
        $tmp["allowChildren"] = false;

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam("name");

        if (strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        } else {
            $group = new TaxRuleGroup();
            $group->setName($name);
            $group->setActive(1);
            $group->save();

            $this->_helper->json(array("success" => true, "data" => $group));
        }
    }

    public function getAction()
    {
        $id = $this->getParam("id");
        $group = TaxRuleGroup::getById($id);

        if ($group instanceof TaxRuleGroup) {
            $this->_helper->json(array("success" => true, "data" => $group->getObjectVars()));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $group = TaxRuleGroup::getById($id);

        if ($data && $group instanceof TaxRuleGroup) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $group->setValues($data);
            $group->save();

            $taxRules = \Zend_Json::decode($this->getParam("taxRules"));
            $taxRulesUpdated = array();

            foreach ($taxRules as $taxRule) {
                $id = intval($taxRule['id']);
                $taxRuleObject = null;

                unset($taxRule['id']);

                if ($id) {
                    $taxRuleObject = \CoreShop\Model\TaxRule::getById($id);
                }

                if (!$taxRuleObject instanceof \CoreShop\Model\TaxRule) {
                    $taxRuleObject = new \CoreShop\Model\TaxRule();
                }

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

            $this->_helper->json(array("success" => true, "data" => $group));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam("id");
        $group = TaxRuleGroup::getById($id);

        if ($group instanceof TaxRuleGroup) {
            $group->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }


    public function listRulesAction()
    {
        $id = $this->getParam("id");
        $group = TaxRuleGroup::getById($id);

        if ($group instanceof TaxRuleGroup) {
            $rules = $group->getRules();

            $this->_helper->json(array("total" => count($rules), "data" => $rules));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function addRuleAction()
    {
        $id = $this->getParam("id");
        $group = TaxRuleGroup::getById($id);

        if ($group instanceof TaxRuleGroup) {
            $countryId = $this->getParam("countryId");
            $taxId = $this->getParam("taxId");
            $behavior = $this->getParam("behavior");

            $taxRule = new \CoreShop\Model\TaxRule();
            $taxRule->setCountryId($countryId);
            $taxRule->setTaxId($taxId);
            $taxRule->setBehavior($behavior);
            $taxRule->setTaxRuleGroup($group);
            $taxRule->save();

            $this->_helper->json(array("success" => true, "taxRule" => $taxRule));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }
}
