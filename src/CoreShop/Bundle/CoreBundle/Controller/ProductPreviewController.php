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
use CoreShop\Component\Core\Model\PimcoreStoresAwareInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductPreviewController extends AdminController
{
    public function previewAction(Request $request): Response
    {
        if (!$this->getParameterFromRequest($request, 'store')) {
            return new Response('No Store selected');
        }

        $store = $this->get('coreshop.repository.store')->find($this->getParameterFromRequest($request, 'store'));

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

        $id = $this->getParameterFromRequest($request, 'id');

        /**
         * @var DataObject\Concrete|null $object
         */
        $object = DataObject::getById($id);

        if (null === $object) {
            return new Response('DataObject not found');
        }

        if (!$object instanceof PimcoreStoresAwareInterface) {
            return new Response('Store Preview is only available for objects implementing CoreShop\Component\Core\Model\PimcoreStoresAwareInterface');
        }

        if (!in_array($store->getId(), array_values($object->getStores()))) {
            return new Response('Selected Store is Invalid for Object');
        }

        $url = $object->getClass()->getPreviewUrl();
        if ($url) {
            // replace named variables
            $vars = $object->getObjectVars();
            foreach ($vars as $key => $value) {
                if (!empty($value) && (is_string($value) || is_numeric($value))) {
                    $url = str_replace('%' . $key, urlencode($value), $url);
                } else {
                    if (str_contains($url, '%' . $key)) {
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

        $newUrl = ($site ? 'https://' . $site->getMainDomain() : '') . ($urlParts['path'] ?? '');
        $newUrl .= '?pimcore_object_preview=' . $id . '&_dc=' . time() . (isset($urlParts['query']) ? '&' . $urlParts['query'] : '');

        return $this->redirect($newUrl);
    }
}
