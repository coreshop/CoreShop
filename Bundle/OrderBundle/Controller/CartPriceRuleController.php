<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartPriceRuleController extends ResourceController
{
    public function getConfigAction(Request $request)
    {
        $actions = $this->getConfigActions();
        $conditions = $this->getConfigConditions();

        return $this->viewHandler->handle(['actions' => array_keys($actions), 'conditions' => array_keys($conditions)]);
    }

    public function getVoucherCodesAction(Request $request) {
        $id = $request->get('id');
        $cartPriceRule = $this->repository->find($id);

        if (!$cartPriceRule instanceof CartPriceRuleInterface) {
            throw new NotFoundHttpException();
        }

        return $this->viewHandler->handle(['total' => count($cartPriceRule->getVoucherCodes()), 'data' => $cartPriceRule->getVoucherCodes(), 'success' => true], ['group' => 'Detailed']);
    }

    public function generateVoucherCodesAction(Request $request) {
        $amount = $request->get("amount");
        $length = $request->get("length");
        $format = $request->get("format");
        $prefix = $request->get("prefix", "");
        $suffix = $request->get("suffix", "");
        $hyphensOn = $request->get("hyphensOn", 0);
        $id = $request->get('id');
        $priceRule = $this->repository->find($id);

        if ($priceRule instanceof CartPriceRuleInterface) {
            $codes = $this->getVoucherCodeGenerator()->generateCodes($priceRule, $amount, $length, $format, $hyphensOn, $prefix, $suffix);

            foreach ($codes as $code) {
                $this->entityManager->persist($code);
            }
            $this->entityManager->flush();

            return $this->viewHandler->handle(['success' => true]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    public function exportVoucherCodesAction(Request $request) {
        $id = $request->get('id');
        $priceRule = $this->repository->find($id);

        if ($priceRule instanceof CartPriceRuleInterface) {
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
                    "creationDate" => $code->getCreationDate() instanceof \DateTime ? $code->getCreationDate()->getTimestamp() : '',
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

    protected function getVoucherCodeGenerator() {
        return $this->get('coreshop.generator.cart_price_rule_voucher_codes');
    }

    protected function getConfigActions()
    {
        return $this->getParameter('coreshop.cart_price_rule.actions');
    }

    protected function getConfigConditions()
    {
        return $this->getParameter('coreshop.cart_price_rule.conditions');
    }
}
