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

namespace CoreShop\Bundle\CoreBundle\Storage;

use CoreShop\Component\Resource\Storage\StorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CookieStorage implements StorageInterface, EventSubscriberInterface
{
    /**
     * @var ParameterBag
     */
    private $requestCookies;

    /**
     * @var ParameterBag
     */
    private $responseCookies;

    public function __construct()
    {
        $this->requestCookies = new ParameterBag();
        $this->responseCookies = new ParameterBag();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 1024]],
            KernelEvents::RESPONSE => [['onKernelResponse', -1024]],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->requestCookies = new ParameterBag($event->getRequest()->cookies->all());
        $this->responseCookies = new ParameterBag();
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        foreach ($this->responseCookies as $name => $value) {
            $response->headers->setCookie(new Cookie($name, $value));
        }

        $this->requestCookies = new ParameterBag();
        $this->responseCookies = new ParameterBag();
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return !in_array($this->get($name), ['', null], true);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return $this->responseCookies->get($name, $this->requestCookies->get($name, $default));
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $this->responseCookies->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $this->set($name, null);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return array_merge($this->responseCookies->all(), $this->requestCookies->all());
    }
}
