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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Core\Repository\ProductStoreValuesRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends ResourceController
{
    public function removeStoreValuesAction(
        Request $request,
        ProductStoreValuesRepositoryInterface $storeValuesRepository,
        EntityManagerInterface $entityManager,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $product = $this->findOr404($request->get('id'));
        $storeValue = $storeValuesRepository->find($request->get('storeValuesId'));

        if (!$storeValue instanceof ProductStoreValuesInterface) {
            throw new NotFoundHttpException();
        }

        if ($storeValue->getProduct() && $storeValue->getProduct()->getId() === $product->getId()) {
            $storeValuesRepository->remove($storeValue);
            $entityManager->flush();

            return $viewHandler->handle(['success' => true]);
        }

        return $viewHandler->handle(['success' => false]);
    }
}
