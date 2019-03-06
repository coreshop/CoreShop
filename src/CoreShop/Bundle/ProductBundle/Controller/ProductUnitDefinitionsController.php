<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductUnitDefinitionsController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function productAdditionalUnitDefinitionsListAction(Request $request)
    {
        $definitions = [];

        /** @var StackRepository $repository */
        $repository = $this->get('coreshop.repository.stack.product');

        /** @var ProductInterface $product */
        $product = $repository->find($request->get('productId'));

        /** @var ProductUnitDefinitionsInterface $productUnitDefinitions */
        $productUnitDefinitions = $product->getUnits();

        if ($productUnitDefinitions instanceof ProductUnitDefinitionsInterface) {
            $definitions = $productUnitDefinitions->getAdditionalUnitDefinitions();
        }

        return $this->viewHandler->handle($definitions);
    }
}
