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
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\Cloner\ProductClonerInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Model\DataObject;

class ProductVariantUnitSolidifierController extends AdminController
{
    const STATUS_ERROR_NO_VARIANTS = 'error_no_variants';

    const STATUS_ERROR_NO_UNIT_DEFINITIONS = 'error_nno_unit_definitions';

    const DISPATCH_STRATEGY_ONLY_UNIT_DEFINITIONS = 'strategy_only_unit_definitions';

    const DISPATCH_STRATEGY_UNIT_DEFINITIONS_AND_QPR = 'strategy_only_unit_definitions_and_qpr';

    /**
     * @param Request $request
     * @param int     $objectId
     *
     * @return JsonResponse
     */
    public function checkStatusAction(Request $request, $objectId)
    {
        /** @var DataObject\Concrete $object */
        $object = $this->getProductRepository()->find($objectId);

        if (!$object instanceof ProductInterface) {
            return new JsonResponse([
                'success' => false,
                'message' => sprintf('%s is not a valid product', $objectId)
            ]);
        }

        $strategy = null;
        $errorStatus = false;

        $variants = $object->getChildren([AbstractObject::OBJECT_TYPE_VARIANT], true);

        if (count($variants) === 0) {
            $errorStatus = self::STATUS_ERROR_NO_VARIANTS;
        } elseif ($object->hasUnitDefinitions() === false) {
            $errorStatus = self::STATUS_ERROR_NO_UNIT_DEFINITIONS;
        }

        if ($errorStatus === false) {
            $strategy = self::DISPATCH_STRATEGY_ONLY_UNIT_DEFINITIONS;
            if (is_array($object->getQuantityPriceRules()) && count($object->getQuantityPriceRules()) > 0) {
                $strategy = self::DISPATCH_STRATEGY_UNIT_DEFINITIONS_AND_QPR;
            }
        }

        return new JsonResponse([
            'success'     => true,
            'errorStatus' => $errorStatus,
            'strategy'    => $strategy
        ]);
    }

    /**
     * @param Request $request
     * @param int     $objectId
     *
     * @return JsonResponse
     */
    public function applyAction(Request $request, $objectId)
    {
        $success = true;
        $message = null;

        /** @var DataObject\Concrete $object */
        $object = $this->getProductRepository()->find($objectId);

        if (!$object instanceof ProductInterface) {
            return new JsonResponse([
                'success' => false,
                'message' => sprintf('%s is not a valid product', $objectId)
            ]);
        }

        $dispatchedVariants = [];

        foreach ($object->getChildren([AbstractObject::OBJECT_TYPE_VARIANT], true) as $variant) {

            if (!$variant instanceof ProductInterface) {
                continue;
            }

            try {
                $this->getUnitDefinitionsCloner()->clone($variant, $object, false);
            } catch (\Throwable $e) {
                $success = false;
                $message = sprintf(
                    'error while cloning unit definition from product %d to variant %d. Error was: %s',
                    $object->getId(), $variant->getId(), $e->getMessage());
                break;
            }

            try {
                $this->getQuantityPriceRulesCloner()->clone($variant, $object, false);
            } catch (\Throwable $e) {
                $success = false;
                $message = sprintf(
                    'error while cloning quantity price rules from product %d to variant %d. Error was: %s',
                    $object->getId(), $variant->getId(), $e->getMessage());
                break;
            }

            try {
                $variant->save();
            } catch (\Throwable $e) {
                $success = false;
                $message = sprintf('error while saving variant %d. Error was: %s', $variant->getId(), $e->getMessage());
                break;
            }

            $dispatchedVariants[] = $variant->getId();

        }

        return new JsonResponse([
            'success'          => $success,
            'message'          => $message,
            'affectedVariants' => $dispatchedVariants
        ]);

    }

    /**
     * @return ProductRepositoryInterface
     */
    protected function getProductRepository()
    {
        return $this->get('coreshop.repository.product');
    }

    /**
     * @return ProductClonerInterface
     */
    protected function getQuantityPriceRulesCloner()
    {
        return $this->get('coreshop.product.cloner.quantity_price_rules');
    }

    /**
     * @return ProductClonerInterface
     */
    protected function getUnitDefinitionsCloner()
    {
        return $this->get('coreshop.product.cloner.unit_definitions');
    }

}
