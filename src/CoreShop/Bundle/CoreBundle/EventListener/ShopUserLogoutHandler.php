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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

final class ShopUserLogoutHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @var LinkGeneratorInterface
     */
    private $linkGenerator;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param LinkGeneratorInterface $linkGenerator
     * @param string                 $routeName
     * @param SessionInterface       $session
     * @param StoreContextInterface  $storeContext
     */
    public function __construct(
        LinkGeneratorInterface $linkGenerator,
        $routeName,
        SessionInterface $session,
        StoreContextInterface $storeContext
    ) {
        $this->linkGenerator = $linkGenerator;
        $this->routeName = $routeName;
        $this->session = $session;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        $store = $this->storeContext->getStore();

        if ($store instanceof StoreInterface) {
            $this->session->remove('coreshop.cart.' . $store->getId());
        }

        return new RedirectResponse($this->linkGenerator->generate(null, $this->routeName, ['_locale' => $request->getLocale()]));
    }
}
