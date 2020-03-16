<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\VoucherGeneratorType;
use CoreShop\Bundle\OrderBundle\Form\Type\VoucherType;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Order\Generator\CartPriceRuleVoucherCodeGenerator;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCode;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartPriceRuleController extends ResourceController
{
    public function getConfigAction(ViewHandlerInterface $viewHandler): Response
    {
        $actions = $this->getConfigActions();
        $conditions = $this->getConfigConditions();

        return $viewHandler->handle(['actions' => array_keys($actions), 'conditions' => array_keys($conditions)]);
    }

    public function getVoucherCodesAction(Request $request, ViewHandlerInterface $viewHandler): Response
    {
        $id = $request->get('cartPriceRule');
        $cartPriceRule = $this->repository->find($id);

        if (!$cartPriceRule instanceof CartPriceRuleInterface) {
            throw new NotFoundHttpException();
        }

        return $viewHandler->handle(['total' => count($cartPriceRule->getVoucherCodes()), 'data' => $cartPriceRule->getVoucherCodes(), 'success' => true], ['group' => 'Detailed']);
    }

    public function createVoucherCodeAction(
        Request $request,
        FormFactoryInterface $formFactory,
        FactoryInterface $cartPriceRuleVoucherCodeFactory,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $form = $formFactory->createNamed('', VoucherType::class);
        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            $resource = $form->getData();

            $codeCheck = $this->manager
                ->getRepository('CoreShop\Component\Order\Model\CartPriceRuleVoucherCode')
                ->findOneBy(['code' => $resource->getCode()]);

            if ($codeCheck instanceof CartPriceRuleVoucherCode) {
                return $viewHandler->handle(['success' => false, 'message' => 'voucher code already exists']);
            }

            /** @var CartPriceRuleVoucherCodeInterface $codeObject */
            $codeObject = $cartPriceRuleVoucherCodeFactory->createNew();
            $codeObject->setCode($resource->getCode());
            $codeObject->setCreationDate(new \DateTime());
            $codeObject->setUsed(false);
            $codeObject->setUses(0);
            $codeObject->setCartPriceRule($resource->getCartPriceRule());

            $this->manager->persist($codeObject);
            $this->manager->flush();

            return $viewHandler->handle(['success' => true]);
        }

        return $viewHandler->handle(['success' => false]);
    }

    public function generateVoucherCodesAction(
        Request $request,
        FormFactoryInterface $formFactory,
        CartPriceRuleVoucherCodeGenerator $generator,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $form = $formFactory->createNamed('', VoucherGeneratorType::class);

        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            $resource = $form->getData();

            $codes = $generator->generateCodes($resource);

            foreach ($codes as $code) {
                $this->manager->persist($code);
            }

            $this->manager->flush();

            return $viewHandler->handle(['success' => true]);
        }

        return $viewHandler->handle(['success' => false]);
    }

    public function exportVoucherCodesAction(Request $request, ViewHandlerInterface $viewHandler): Response
    {
        $id = $request->get('cartPriceRule');
        $priceRule = $this->repository->find($id);

        if ($priceRule instanceof CartPriceRuleInterface) {
            $fileName = $priceRule->getName() . '_vouchercodes';
            $csvData = [];

            $csvData[] = implode(',', [
                'code',
                'creationDate',
                'used',
                'uses',
            ]);

            foreach ($priceRule->getVoucherCodes() as $code) {
                $data = [
                    'code' => $code->getCode(),
                    'creationDate' => $code->getCreationDate() instanceof \DateTime ? $code->getCreationDate()->getTimestamp() : '',
                    'used' => $code->getUsed(),
                    'uses' => $code->getUses(),
                ];

                $csvData[] = implode(';', $data);
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

    public function deleteVoucherCodeAction(
        Request $request,
        CartPriceRuleVoucherRepositoryInterface $repository,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $cartPriceRuleId = $request->get('cartPriceRule');
        $id = $request->get('id');
        $cartPriceRule = $this->repository->find($cartPriceRuleId);

        if (!$cartPriceRule instanceof CartPriceRuleInterface) {
            throw new NotFoundHttpException();
        }

        $code = $repository->find(['id' => $id]);

        if ($code instanceof CartPriceRuleVoucherCode) {
            $repository->remove($code);

            return $viewHandler->handle(['success' => true, 'id' => $id]);
        }

        return $viewHandler->handle(['success' => false]);
    }

    /**
     * @return mixed
     */
    protected function getConfigActions()
    {
        return $this->getParameter('coreshop.cart_price_rule.actions');
    }

    /**
     * @return mixed
     */
    protected function getConfigConditions()
    {
        return $this->getParameter('coreshop.cart_price_rule.conditions');
    }
}
