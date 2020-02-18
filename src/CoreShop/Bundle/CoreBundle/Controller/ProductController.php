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

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends ResourceController
{
    public function removeStoreValuesAction(Request $request)
    {
        $product = $this->findOr404($request->get('id'));
        $storeValue = $this->get('coreshop.repository.product_store_values')->find($request->get('storeValuesId'));

        if (!$storeValue instanceof ProductStoreValuesInterface) {
            throw new NotFoundHttpException();
        }

        if ($storeValue->getProduct() && $storeValue->getProduct()->getId() === $product->getId()) {
            $this->get('coreshop.manager.product_store_values')->remove($storeValue);
            $this->get('coreshop.manager.product_store_values')->flush();

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false]);
    }
}
