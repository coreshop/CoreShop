<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

                if ((int)$unitDefinitionId === $range->getUnitDefinition()->getId()) {
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

    protected function getProductRepository(): StackRepository
    {
        return $this->get('coreshop.repository.stack.product');
    }
}
