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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Model\DataObject;

class ProductValidationController extends AdminController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function validateUnitDefinitionDeletionAction(Request $request)
    {
        $message = null;
        $success = true;
        $status = 'unlocked';
        $objectId = $request->get('id', null);
        $unitDefinitionId = $request->get('unitDefinitionId', null);

        if (is_null($unitDefinitionId)) {
            return new JsonResponse([
                'success' => false,
                'message' => sprintf('%s is not a valid unit definition id.', $unitDefinitionId)
            ]);
        }

        /** @var DataObject\Concrete $object */
        $object = $this->getProductRepository()->find($objectId);

        if (!$object instanceof ProductInterface) {
            return new JsonResponse([
                'success' => false,
                'message' => sprintf('%s is not a valid product', $objectId)
            ]);
        }

        $hasQuantityPriceRules = is_array($object->getQuantityPriceRules()) && count($object->getQuantityPriceRules()) > 0;

        if ($hasQuantityPriceRules === false) {
            return new JsonResponse([
                'success' => $success,
                'message' => $message,
                'status'  => $status
            ]);
        }

        foreach ($object->getQuantityPriceRules() as $quantityPriceRule) {

            $ranges = $quantityPriceRule->getRanges();
            if (!$ranges instanceof Collection) {
                continue;
            }

            foreach ($ranges as $index => $range) {

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
            'status'  => $status
        ]);
    }

    /**
     * @return StackRepository
     */
    protected function getProductRepository()
    {
        return $this->get('coreshop.repository.stack.product');
    }

}
