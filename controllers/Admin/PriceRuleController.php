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
 * Class CoreShop_Admin_PriceRuleController
 */
class CoreShop_Admin_PriceRuleController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_price_rules';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Cart\PriceRule::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Model\Cart\PriceRule) {
            $model->setActive(0);
            $model->setHighlight(0);
            $model->setUsagePerVoucherCode(0);
            $model->setUseMultipleVoucherCodes(false);
        }
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Model\Cart\PriceRule) {
            return $model->serialize();
        }

        return parent::getReturnValues($model);
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $data
     */
    protected function prepareSave(\CoreShop\Model\AbstractModel $model, $data) {
        if($model instanceof \CoreShop\Model\Cart\PriceRule) {
            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $conditionInstances = $model->prepareConditions($conditions);
            $actionInstances = $model->prepareActions($actions);

            $model->setValues($data['settings']);
            $model->setActions($actionInstances);
            $model->setConditions($conditionInstances);
        }
    }

    public function getConfigAction()
    {
        $this->_helper->json([
            'success' => true,
            'conditions' => \CoreShop\Model\Cart\PriceRule::getConditionDispatcher()->getTypeKeys(),
            'actions' => \CoreShop\Model\Cart\PriceRule::getActionDispatcher()->getTypeKeys()
        ]);
    }

    public function getVoucherCodesAction()
    {
        $id = $this->getParam('id');
        $priceRule = \CoreShop\Model\Cart\PriceRule::getById($id);

        if ($priceRule instanceof \CoreShop\Model\Cart\PriceRule) {
            $list = \CoreShop\Model\Cart\PriceRule\VoucherCode::getList();
            $list->setLimit($this->getParam('limit', 30));
            $list->setOffset($this->getParam('page', 1) - 1);

            if ($this->getParam('filter', null)) {
                $conditionFilters = [];

                $conditionFilters[] = \CoreShop\Model\Service::getFilterCondition($this->getParam('filter'), '\CoreShop\Model\Cart\PriceRule\VoucherCode');
                $conditionFilters[] = "priceRuleId = ?";

                if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                    $list->setCondition(implode(' AND ', $conditionFilters), [$priceRule->getId()]);
                }
            } else {
                $list->setCondition("priceRuleId = ?", [$priceRule->getId()]);
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

            $this->_helper->json(['success' => true, 'data' => $list->getData(), "total" => $list->getTotalCount()]);
        }

        $this->_helper->json(['success' => false]);
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
        $priceRule = \CoreShop\Model\Cart\PriceRule::getById($id);

        if ($priceRule instanceof \CoreShop\Model\Cart\PriceRule) {
            \CoreShop\Model\Cart\PriceRule\VoucherCode\Service::generateCodes($priceRule, $amount, $length, $format, $hyphensOn, $prefix, $suffix);

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }

    public function exportVoucherCodesAction()
    {
        $id = $this->getParam('id');
        $priceRule = \CoreShop\Model\Cart\PriceRule::getById($id);

        if ($priceRule instanceof \CoreShop\Model\Cart\PriceRule) {
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
