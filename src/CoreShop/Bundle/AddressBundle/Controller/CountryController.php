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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\AddressBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Address\Repository\CountryRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CountryController extends ResourceController
{
    public function listActiveAction(): JsonResponse
    {
        /**
         * @var CountryRepositoryInterface $repository
         */
        $repository = $this->repository;

        $data = $repository->findBy(['active' => true]);

        return $this->viewHandler->handle($data, ['group' => 'List']);
    }
}
