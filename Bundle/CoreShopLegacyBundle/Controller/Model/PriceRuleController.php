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
 * Class PriceRuleController
 *
 * @Route("/price-rule")
 */
class PriceRuleController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_price_rules';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule) {
            $model->setActive(0);
            $model->setHighlight(0);
            $model->setUsagePerVoucherCode(0);
            $model->setUseMultipleVoucherCodes(false);
        }
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule) {
            return $model->serialize();
        }

        return parent::getReturnValues($model);
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @param $data
     */
    protected function prepareSave(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model, $data) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule) {
            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $conditionInstances = $model->prepareConditions($conditions);
            $actionInstances = $model->prepareActions($actions);

            $model->setValues($data['settings']);
            $model->setActions($actionInstances);
            $model->setConditions($conditionInstances);
        }
    }

    /**
     * @Route("/get-config")
     */
    public function getConfigAction(Request $request)
    {
        return $this->json([
            'success' => true,
            'conditions' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule::getConditionDispatcher()->getTypeKeys(),
            'actions' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule::getActionDispatcher()->getTypeKeys()
        ]);
    }

    /**
     * @Route("/get-voucher-codes")
     */
    public function getVoucherCodesAction(Request $request)
    {
        $id = $request->get('id');
        $priceRule = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule::getById($id);

        if ($priceRule instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule) {
            $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule\VoucherCode::getList();
            $list->setLimit($request->get('limit', 30));
            $list->setOffset($request->get('page', 1) - 1);

            if ($request->get('filter', null)) {
                $conditionFilters = [];

                $conditionFilters[] = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Service::getFilterCondition($request->get('filter'), '\CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule\VoucherCode');
                $conditionFilters[] = "priceRuleId = ?";

                if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                    $list->setCondition(implode(' AND ', $conditionFilters), [$priceRule->getId()]);
                }
            } else {
                $list->setCondition("priceRuleId = ?", [$priceRule->getId()]);
            }

            $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($request->request->all());

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

            return $this->json(['success' => true, 'data' => $list->getData(), "total" => $list->getTotalCount()]);
        }

        return $this->json(['success' => false]);
    }

    /**
     * @Route("/generate-voucher-codes")
     */
    public function generateVoucherCodesAction(Request $request)
    {
        $amount = $request->get("amount");
        $length = $request->get("length");
        $format = $request->get("format");
        $prefix = $request->get("prefix", "");
        $suffix = $request->get("suffix", "");
        $hyphensOn = $request->get("hyphensOn", 0);
        $id = $request->get('id');
        $priceRule = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule::getById($id);

        if ($priceRule instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule) {
            \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule\VoucherCode\Service::generateCodes($priceRule, $amount, $length, $format, $hyphensOn, $prefix, $suffix);

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false]);
    }

    /**
     * @Route("/export-voucher-codes")
     */
    public function exportVoucherCodesAction(Request $request)
    {
        $id = $request->get('id');
        $priceRule = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule::getById($id);

        if ($priceRule instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule) {
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
