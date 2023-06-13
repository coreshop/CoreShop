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
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Core\Model\ConfigurationInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController extends ResourceController
{
    public function saveAllAction(
        Request $request,
        ConfigurationServiceInterface $configurationService,
        StoreRepositoryInterface $storeRepository
    ): Response
    {
        $values = $this->decodeJson($this->getParameterFromRequest($request, 'values'));
        $values = array_htmlspecialchars($values);

        foreach ($values as $store => $storeValues) {
            /**
             * @var StoreInterface $store
             */
            $store = $storeRepository->find($store);

            foreach ($storeValues as $key => $value) {
                $configurationService->setForStore($key, $value, $store);
            }
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    public function getAllAction(StoreRepositoryInterface $storeRepository): Response
    {
        $stores = $storeRepository->findAll();
        $valueArray = [];

        foreach ($stores as $store) {
            $storeValues = [];

            /**
             * @var ConfigurationInterface[] $configurations
             */
            $configurations = $this->repository->findBy(['store' => [$store, null]]);

            if (is_array($configurations)) {
                foreach ($configurations as $c) {
                    $storeValues[$c->getKey()] = $c->getData();
                }
            }

            $valueArray[$store->getId()] = $storeValues;
        }

        return $this->viewHandler->handle(['success' => true, 'data' => $valueArray]);
    }
}
