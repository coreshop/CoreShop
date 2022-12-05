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

namespace CoreShop\Bundle\StorageListBundle\Controller;

use CoreShop\Component\StorageList\Model\ShareableStorageListInterface;
use CoreShop\Component\StorageList\Repository\ShareableStorageListRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StorageListShareController extends AbstractController
{
    public function __construct(
        protected string $identifier,
        protected ShareableStorageListRepositoryInterface $repository,
        protected string $templateShareSummary,
    ) {
    }

    public function shareSummaryAction(Request $request): Response
    {
        $token = $request->attributes->get('token');

        if (!$token) {
            throw new NotFoundHttpException();
        }

        $list = $this->repository->findByToken($token);

        if (!$list instanceof ShareableStorageListInterface) {
            throw new NotFoundHttpException();
        }

        if (!$list->listCanBeShared()) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            $this->templateShareSummary,
            [
                'storage_list' => $list,
            ],
        );
    }
}
