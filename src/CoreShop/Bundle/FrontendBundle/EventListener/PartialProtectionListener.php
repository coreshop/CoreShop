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

namespace CoreShop\Bundle\FrontendBundle\EventListener;

use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @deprecated This class is deprecated since 2.2.0 and will be removed with 3.0.0
 */
class PartialProtectionListener
{
    const PROTECTED_ROUTES = [
        'coreshop_cart_add'
    ];

    /**
     * @var LinkGeneratorInterface
     */
    protected $linkGenerator;

    /**
     * @param LinkGeneratorInterface $linkGenerator
     */
    public function __construct(LinkGeneratorInterface $linkGenerator)
    {
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * @param RequestEvent $event
     */
    public function checkFragmentRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$request->isMethod('GET')) {
            return;
        }

        if (!$request->attributes->has('_route')) {
            return;
        }

        $route = $request->attributes->get('_route');
        if (!in_array($route, self::PROTECTED_ROUTES)) {
            return;
        }

        if ($request->get('_partial', 0) !== 0) {
            return;
        }

        $redirect = $request->get('_redirect', $this->linkGenerator->generate(null, 'coreshop_index'));

        if ($request->isXmlHttpRequest()) {
            $event->setResponse(new JsonResponse(['success' => false, 'errors' => 'GET is not allowed.']));
            return;
        }

        $event->setResponse(new RedirectResponse($redirect, 302));
    }
}
