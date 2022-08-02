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

namespace CoreShop\Bundle\ProductBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductUnitDefinitionsController extends ResourceController
{
    public function productUnitDefinitionsListAction(Request $request): Response
    {
        $definitions = [];

        /** @var StackRepository $repository */
        $repository = $this->get('coreshop.repository.stack.product');

        /** @var ProductInterface $product */
        $product = $repository->find($this->getParameterFromRequest($request, 'productId'));

        $definitions = $this->getUnitDefinitionsForProduct($product, 'all');

        return $this->viewHandler->handle($definitions);
    }

    public function productAdditionalUnitDefinitionsListAction(Request $request): Response
    {
        $definitions = [];

        /** @var StackRepository $repository */
        $repository = $this->get('coreshop.repository.stack.product');

        /** @var ProductInterface $product */
        $product = $repository->find($this->getParameterFromRequest($request,'productId'));

        if ($product instanceof Concrete) {
            $product = VersionHelper::getLatestVersion($product);
        }

        if ($product instanceof ProductInterface) {
            $definitions = $this->getUnitDefinitionsForProduct($product, 'additional');
        }

        return $this->viewHandler->handle($definitions);
    }

    protected function getUnitDefinitionsForProduct(ProductInterface $product, string $type = 'all'): Collection
    {
        $definitions = new ArrayCollection();

        if ($product->hasUnitDefinitions()) {
            $productUnitDefinitions = $product->getUnitDefinitions();
            $definitions = $type === 'additional'
                ? $productUnitDefinitions->getAdditionalUnitDefinitions()
                : $productUnitDefinitions->getUnitDefinitions();
        } else {
            $parent = $product->getParent();

            if ($parent instanceof ProductInterface && $product instanceof Concrete && $product->getClass()->getAllowInherit()) {
                $definitions = $this->getUnitDefinitionsForProduct($parent, $type);
            }
        }

        return $definitions->filter(function (ProductUnitDefinitionInterface $unitDefinition) {
            return null !== $unitDefinition->getId();
        });
    }

    protected function getLatestVersion(Concrete $object): Concrete
    {
        $modificationDate = $object->getModificationDate();
        $latestVersion = $object->getLatestVersion();
        if ($latestVersion) {
            /**
             * @psalm-suppress InternalMethod
             */
            $latestObj = $latestVersion->loadData();
            if ($latestObj instanceof Concrete) {
                $object = $latestObj;
                $object->setModificationDate($modificationDate); // set de modification-date from published version to compare it in js-frontend
            }
        }

        return $object;
    }
}
