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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/** @psalm-suppress DeprecatedInterface */
final class ShopUserLogoutHandler implements LogoutSuccessHandlerInterface
{
    private RouterInterface $router;
    private string $routeName;
    private SessionInterface $session;
    private StoreContextInterface $storeContext;

    public function __construct(
        RouterInterface $router,
        string $routeName,
        SessionInterface $session,
        StoreContextInterface $storeContext
    ) {
        $this->router = $router;
        $this->routeName = $routeName;
        $this->session = $session;
        $this->storeContext = $storeContext;
    }

    public function onLogoutSuccess(Request $request): Response
    {
        $store = $this->storeContext->getStore();

        if ($store instanceof StoreInterface) {
            $this->session->remove('coreshop.cart.' . $store->getId());
        }

        return new RedirectResponse($this->router->generate($this->routeName, ['_locale' => $request->getLocale()]));
    }
}
