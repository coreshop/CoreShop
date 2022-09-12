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

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends ResourceController
{
    public function removeStoreValuesAction(Request $request): Response
    {
        $product = $this->findOr404($this->getParameterFromRequest($request, 'id'));
        $storeValue = $this->get('coreshop.repository.product_store_values')->find($this->getParameterFromRequest($request, 'storeValuesId'));

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
