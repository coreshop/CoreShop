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

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductPreviewController extends AdminController
{
    public function previewAction(
        Request $request,
        StoreRepositoryInterface $storeRepository,
        ProductRepositoryInterface $productRepository
    ): Response {
        if (!$request->get('store')) {
            return new Response('No Store selected');
        }

        $store = $storeRepository->find($request->get('store'));

        if (!$store instanceof StoreInterface) {
            return new Response('Invalid Store selected');
        }

        $site = null;

        if (!$store->getIsDefault()) {
            $site = Site::getById($store->getSiteId());

            if (!$site) {
                return new Response('Store\'s Site is invalid');
            }
        }

        /**
         * @var DataObject\Concrete $object
         */
        $object = $productRepository->find($request->get('id'));

        if (!$object instanceof ProductInterface) {
            return new Response('Store Preview is only available for Products');
        }

        if (!in_array($store->getId(), array_values($object->getStores()))) {
            return new Response('Selected Store is Invalid for Product');
        }

        $url = $object->getClass()->getPreviewUrl();
        if ($url) {
            // replace named variables
            $vars = $object->getObjectVars();
            foreach ($vars as $key => $value) {
                if (!empty($value) && (is_string($value) || is_numeric($value))) {
                    $url = str_replace('%'.$key, urlencode($value), $url);
                } else {
                    if (strpos($url, '%'.$key) !== false) {
                        return new Response('No preview available, please ensure that all fields which are required for the preview are filled correctly.');
                    }
                }
            }
        } elseif ($linkGenerator = $object->getClass()->getLinkGenerator()) {
            $url = $linkGenerator->generate($object, ['preview' => true, 'context' => $this]);
        }

        if (!$url) {
            return new Response("Preview not available, it seems that there's a problem with this object.");
        }

        // replace all remainaing % signs
        $url = str_replace('%', '%25', $url);

        $urlParts = parse_url($url);

        return $this->redirect(($site ? 'https://'.$site->getMainDomain() : '').$urlParts['path']);
    }
}
