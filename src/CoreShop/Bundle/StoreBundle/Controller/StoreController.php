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

namespace CoreShop\Bundle\StoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends ResourceController
{
    public function listSitesAction(Request $request): Response
    {
        $list = new Site\Listing();
        $list->setOrder('ASC');
        $list->load();

        $sites = [];
        foreach ($list->getSites() as $site) {
            if ($site instanceof Site) {
                $sites[] = [
                    'id' => $site->getId(),
                    'rootId' => $site->getRootId(),
                    'name' => $site->getMainDomain(),
                ];
            }
        }

        return $this->viewHandler->handle($sites);
    }
}
