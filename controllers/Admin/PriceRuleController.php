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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Model\Cart\PriceRule;
use CoreShop\Controller\Action\Admin;
use Pimcore\Tool as PimTool;

/**
 * Class CoreShop_Admin_PriceRuleController
 */
class CoreShop_Admin_PriceRuleController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_price_rules');
        }
    }

    public function listAction()
    {
        $list = PriceRule::getList();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $pricerule) {
                $data[] = $this->getTreeNodeConfig($pricerule);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig($priceRule)
    {
        $tmpPriceRule = array(
            'id' => $priceRule->getId(),
            'text' => $priceRule->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$priceRule->getId(),
            ),
            'name' => $priceRule->getName(),
        );

        return $tmpPriceRule;
    }

    public function getConfigAction()
    {
        $this->_helper->json(array(
            'success' => true,
            'conditions' => PriceRule::$availableConditions,
            'actions' => PriceRule::$availableActions,
        ));
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $priceRule = new PriceRule();
            $priceRule->setName($name);
            $priceRule->setActive(0);
            $priceRule->setHighlight(0);
            $priceRule->setUsagePerVoucherCode(0);
            $priceRule->setUseMultipleVoucherCodes(false);
            $priceRule->save();

            $this->_helper->json(array('success' => true, 'data' => $priceRule));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $priceRule = PriceRule::getById($id);

        if ($priceRule instanceof PriceRule) {
            $this->_helper->json(array('success' => true, 'data' => $priceRule->getObjectVars()));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $priceRule = PriceRule::getById($id);

        if ($data && $priceRule instanceof PriceRule) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $actionNamespace = 'CoreShop\\Model\\PriceRule\\Action\\';
            $conditionNamespace = 'CoreShop\\Model\\PriceRule\\Condition\\';

            $conditionInstances = $priceRule->prepareConditions($conditions, $conditionNamespace);
            $actionInstances = $priceRule->prepareActions($actions, $actionNamespace);

            $priceRule->setValues($data['settings']);
            $priceRule->setActions($actionInstances);
            $priceRule->setConditions($conditionInstances);
            $priceRule->save();

            $this->_helper->json(array('success' => true, 'data' => $priceRule));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $priceRule = PriceRule::getById($id);

        if ($priceRule instanceof PriceRule) {
            $priceRule->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }

    public function getVoucherCodesAction()
    {
        $id = $this->getParam('id');
        $priceRule = PriceRule::getById($id);

        if ($priceRule instanceof PriceRule) {
            $list = \CoreShop\Model\Cart\PriceRule\VoucherCode::getList();
            $list->setLimit($this->getParam('limit', 30));
            $list->setOffset($this->getParam('page', 1) - 1);

            if ($this->getParam('filter', null)) {
                $conditionFilters[] = \CoreShop\Model\Service::getFilterCondition($this->getParam('filter'), '\CoreShop\Model\Cart\PriceRule\VoucherCode');
                $conditionFilters[] = "priceRuleId = ?";

                if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                    $list->setCondition(implode(' AND ', $conditionFilters), array($priceRule->getId()));
                }
            } else {
                $list->setCondition("priceRuleId = ?", array($priceRule->getId()));
            }

            $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($this->getAllParams());

            $order = 'DESC';
            $orderKey = 'id';

            if ($sortingSettings['order']) {
                $order = $sortingSettings['order'];
            }
            if (strlen($sortingSettings['orderKey']) > 0) {
                $orderKey = $sortingSettings['orderKey'];
            }

            $list->setOrder($order);
            $list->setOrderKey($orderKey);

            $this->_helper->json(array('success' => true, 'data' => $list->getData(), "total" => $list->getTotalCount()));
        }

        $this->_helper->json(array('success' => false));
    }

    public function generateVoucherCodesAction()
    {
        $amount = $this->getParam("amount");
        $length = $this->getParam("length");
        $format = $this->getParam("format");
        $prefix = $this->getParam("prefix", "");
        $suffix = $this->getParam("suffix", "");
        $hyphensOn = $this->getParam("hyphensOn", 0);
        $id = $this->getParam('id');
        $priceRule = PriceRule::getById($id);

        if ($priceRule instanceof PriceRule) {
            PriceRule\VoucherCode\Service::generateCodes($priceRule, $amount, $length, $format, $hyphensOn, $prefix, $suffix);

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }

    public function exportVoucherCodesAction()
    {
        $id = $this->getParam('id');
        $priceRule = PriceRule::getById($id);

        if ($priceRule instanceof PriceRule) {
            $fileName = $priceRule->getName() . "_vouchercodes";
            $csvData = [];

            $csvData[] = implode(",", [
                "code",
                "creationDate",
                "used",
                "uses"
            ]);

            foreach ($priceRule->getVoucherCodes() as $code) {
                $data = [
                    "code" => $code->getCode(),
                    "creationDate" => $code->getCreationDate(),
                    "used" => $code->getUsed(),
                    "uses" => $code->getUses()
                ];

                $csvData[] = implode(";", $data);
            }

            $csv = implode(PHP_EOL, $csvData);

            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header("Content-Disposition: attachment; filename=\"$fileName.csv\"");
            ini_set('display_errors', false); //to prevent warning messages in csv
            echo "\xEF\xBB\xBF";
            echo $csv;
            die();
        }

        exit;
    }
}
