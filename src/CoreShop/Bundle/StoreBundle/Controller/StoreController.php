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
        if (is_array($list->getSites())) {
            foreach ($list->getSites() as $site) {
                if ($site instanceof Site) {
                    $sites[] = [
                        'id' => $site->getId(),
                        'rootId' => $site->getRootId(),
                        'name' => $site->getMainDomain(),
                    ];
                }
            }
        }

        return $this->viewHandler->handle($sites);
    }
}
