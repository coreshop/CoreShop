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
