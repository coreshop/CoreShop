<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\VoucherGeneratorType;
use CoreShop\Bundle\OrderBundle\Form\Type\VoucherType;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Order\Generator\CartPriceRuleVoucherCodeGenerator;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCode;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartPriceRuleController extends ResourceController
{
    public function listVoucherRulesAction(): JsonResponse
    {
        /**
         * @var CartPriceRuleRepositoryInterface $repository
         */
        $repository = $this->repository;

        $data = $repository->findVoucherRules();

        return $this->viewHandler->handle($data, ['group' => 'List']);
    }

    public function getConfigAction(Request $request): JsonResponse
    {
        $actions = $this->getConfigActions();
        $conditions = $this->getConfigConditions();

        $itemActions = $this->getCartItemConfigActions();
        $itemConditions = $this->getCartItemConfigConditions();

        return $this->viewHandler->handle([
            'actions' => array_keys($actions),
            'conditions' => array_keys($conditions),
            'itemActions' => array_keys($itemActions),
            'itemConditions' => array_keys($itemConditions),
        ]);
    }

    public function getCartItemConfigAction(Request $request): JsonResponse
    {
        $itemActions = $this->getCartItemConfigActions();
        $itemConditions = $this->getCartItemConfigConditions();

        return $this->viewHandler->handle([
            'actions' => array_keys($itemActions),
            'conditions' => array_keys($itemConditions),
        ]);
    }

    public function getVoucherCodesAction(Request $request, CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherCodeRepository): JsonResponse
    {
        $id = $this->getParameterFromRequest($request, 'cartPriceRule');
        $cartPriceRule = $this->repository->find($id);

        if (!$cartPriceRule instanceof CartPriceRuleInterface) {
            throw new NotFoundHttpException();
        }

        $data = $cartPriceRuleVoucherCodeRepository->findAllPaginator(
            $cartPriceRule,
            (int) $this->getParameterFromRequest($request, 'start', 0),
            (int) $this->getParameterFromRequest($request, 'limit', 50),
        );

        return $this->viewHandler->handle(
            [
                'total' => count($data),
                'data' => iterator_to_array($data->getIterator()),
                'success' => true,
            ],
            [
                'group' => 'Detailed',
            ],
        );
    }

    public function createVoucherCodeAction(Request $request, FormFactoryInterface $formFactory, CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherCodeRepository, FactoryInterface $cartPriceRuleVoucherCodeFactory): JsonResponse
    {
        $form = $formFactory->createNamed('', VoucherType::class);
        $handledForm = $form->handleRequest($request);
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            $resource = $form->getData();

            $codeCheck = $cartPriceRuleVoucherCodeRepository->findOneBy(['code' => $resource->getCode()]);

            if ($codeCheck instanceof CartPriceRuleVoucherCode) {
                return $this->viewHandler->handle(['success' => false, 'message' => 'voucher code already exists']);
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

            return $this->viewHandler->handle(['success' => true]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    public function generateVoucherCodesAction(Request $request, FormFactoryInterface $formFactory, CartPriceRuleVoucherCodeGenerator $cartPriceRuleVoucherCodeGenerator): JsonResponse
    {
        $form = $formFactory->createNamed('', VoucherGeneratorType::class);

        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            $resource = $form->getData();

            $codes = $cartPriceRuleVoucherCodeGenerator->generateCodes($resource);

            foreach ($codes as $code) {
                $this->manager->persist($code);
            }
            $this->manager->flush();

            return $this->viewHandler->handle(['success' => true]);
        }

        $errors = $this->formErrorSerializer->serializeErrorFromHandledForm($handledForm);

        return $this->viewHandler->handle(['success' => false, 'message' => implode(\PHP_EOL, $errors)]);
    }

    public function exportVoucherCodesAction(Request $request, CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherCodeRepository): void
    {
        $id = $this->getParameterFromRequest($request, 'cartPriceRule');
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

            $codes = $cartPriceRuleVoucherCodeRepository->findAllPaginator(
                $priceRule,
                (int) $this->getParameterFromRequest($request, 'start', 0),
                (int) $this->getParameterFromRequest($request, 'limit', 50),
            );

            foreach ($codes as $code) {
                $data = [
                    'code' => $code->getCode(),
                    'creationDate' => $code->getCreationDate() instanceof \DateTime ? $code->getCreationDate()->getTimestamp() : '',
                    'used' => $code->getUsed(),
                    'uses' => $code->getUses(),
                ];

                $csvData[] = implode(',', $data);
            }

            $csv = implode(\PHP_EOL, $csvData);

            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header("Content-Disposition: attachment; filename=\"$fileName.csv\"");
            ini_set('display_errors', 'off'); //to prevent warning messages in csv
            echo "\xEF\xBB\xBF";
            echo $csv;
            die();
        }

        exit;
    }

    public function deleteVoucherCodeAction(Request $request, CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherCodeRepository): JsonResponse
    {
        $cartPriceRuleId = $this->getParameterFromRequest($request, 'cartPriceRule');
        $id = $this->getParameterFromRequest($request, 'id');
        $cartPriceRule = $this->repository->find($cartPriceRuleId);

        if (!$cartPriceRule instanceof CartPriceRuleInterface) {
            throw new NotFoundHttpException();
        }

        $code = $cartPriceRuleVoucherCodeRepository->find(['id' => $id]);

        if ($code instanceof CartPriceRuleVoucherCode) {
            $cartPriceRuleVoucherCodeRepository->remove($code);

            return $this->viewHandler->handle(['success' => true, 'id' => $id]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @return array<string, string>
     */
    protected function getConfigActions(): array
    {
        return $this->getParameter('coreshop.cart_price_rule.actions');
    }

    /**
     * @return array<string, string>
     */
    protected function getConfigConditions(): array
    {
        return $this->getParameter('coreshop.cart_price_rule.conditions');
    }

    /**
     * @return array<string, string>
     */
    protected function getCartItemConfigActions(): array
    {
        return $this->getParameter('coreshop.cart_item_price_rule.actions');
    }

    /**
     * @return array<string, string>
     */
    protected function getCartItemConfigConditions(): array
    {
        return $this->getParameter('coreshop.cart_item_price_rule.conditions');
    }
}
