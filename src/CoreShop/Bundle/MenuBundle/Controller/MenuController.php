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

namespace CoreShop\Bundle\MenuBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class MenuController
{
    public function menuAction(string $type, Environment $twig): Response
    {
        $result = $twig->render('@CoreShopMenu/menu.js.twig', [
            'type' => $type,
            'typeId' => str_replace('.', '_', $type),
        ]);

        $response = new Response($result);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
