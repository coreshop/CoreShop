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

namespace CoreShop\Bundle\FrontendBundle\EventListener;

use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @deprecated This class is deprecated since 2.2.0 and will be removed with 3.0.0
 */
class PartialProtectionListener implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
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

        $redirect = $request->get('_redirect', $this->linkGenerator->generate(null, 'coreshop_index'));

        if ($request->isXmlHttpRequest()) {
            $event->setResponse(new JsonResponse(['success' => false, 'errors' => 'GET is not allowed.']));
            return;
        }

        $event->setResponse(new RedirectResponse($redirect, 302));
    }
}
