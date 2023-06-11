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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepositoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\DataObject;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class ProductValidationController extends AdminController
{
    public function validateUnitDefinitionDeletionAction(Request $request): Response
    {
        $message = null;
        $success = true;
        $status = 'unlocked';
        $objectId = $this->getParameterFromRequest($request, 'id', null);
        $unitDefinitionId = $this->getParameterFromRequest($request, 'unitDefinitionId', null);

        if (null === $unitDefinitionId) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Unit definition ID is null.',
            ]);
        }

        /** @var DataObject\Concrete $object */
        $object = $this->getProductRepository()->find($objectId);

        if (!$object instanceof ProductInterface) {
            return new JsonResponse([
                'success' => false,
                'message' => sprintf('%s is not a valid product', $objectId),
            ]);
        }

        $hasQuantityPriceRules = count($object->getQuantityPriceRules()) > 0;

        if ($hasQuantityPriceRules === false) {
            return new JsonResponse([
                'success' => $success,
                'message' => $message,
                'status' => $status,
            ]);
        }

        foreach ($object->getQuantityPriceRules() as $quantityPriceRule) {
            $ranges = $quantityPriceRule->getRanges();
            if (!$ranges instanceof Collection) {
                continue;
            }

            foreach ($ranges as $range) {
                if (!$range instanceof QuantityRangeInterface) {
                    continue;
                }

                if (!$range->getUnitDefinition() instanceof ProductUnitDefinitionInterface) {
                    continue;
                }

                if ((int) $unitDefinitionId === $range->getUnitDefinition()->getId()) {
                    $status = 'locked';

                    break 2;
                }
            }
        }

        return new JsonResponse([
            'success' => $success,
            'message' => $message,
            'status' => $status,
        ]);
    }

    protected function getProductRepository(): StackRepositoryInterface
    {
        return $this->container->get('coreshop.repository.stack.product');
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            new SubscribedService('coreshop.repository.stack.product', StackRepositoryInterface::class, attributes: new Autowire('coreshop.repository.stack.product')),
        ];
    }
}
