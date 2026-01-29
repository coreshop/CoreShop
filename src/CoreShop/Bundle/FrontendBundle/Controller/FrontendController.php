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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class FrontendController extends AbstractController
{
    public function __construct(
        \Psr\Container\ContainerInterface $container,
    ) {
        $this->container = $container;
    }

    protected function getTemplateConfigurator(): TemplateConfiguratorInterface
    {
        return $this->container->get(TemplateConfiguratorInterface::class);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TemplateConfiguratorInterface::class => TemplateConfiguratorInterface::class,
            ShopperContextInterface::class => ShopperContextInterface::class,
            CartContextInterface::class => CartContextInterface::class,
            'translator' => TranslatorInterface::class,
            RequestHelper::class => RequestHelper::class,
            SEOPresentationInterface::class => SEOPresentationInterface::class,
        ]);
    }

    /**
     * @return mixed
     *
     * based on Symfony\Component\HttpFoundation\Request::get
     */
    protected function getParameterFromRequest(Request $request, string $key, $default = null)
    {
        if ($request !== $result = $request->attributes->get($key, $request)) {
            return $result;
        }

        if ($request->query->has($key)) {
            return $request->query->all()[$key];
        }

        if ($request->request->has($key)) {
            return $request->request->all()[$key];
        }

        return $default;
    }

    /**
     * Validates a redirect URL to prevent open redirects.
     *
     * Only allows:
     * - Relative URLs (starting with "/" but not "//")
     * - URLs on the same host as the current request with http/https scheme
     *
     * @param Request $request The current request to validate against
     * @param string  $url     The URL to validate
     * @param string  $default The default URL to return if validation fails
     *
     * @return string The validated URL or the default if invalid
     */
    protected function validateRedirectUrl(Request $request, string $url, string $default): string
    {
        // Empty URL, use default
        if ('' === $url) {
            return $default;
        }

        // Check for protocol-relative URLs (//example.com) which could be used for open redirects
        if (str_starts_with($url, '//')) {
            return $default;
        }

        // Relative URLs (starting with /) are safe if they don't contain dangerous characters
        if (str_starts_with($url, '/')) {
            // Reject URLs with backslashes, control characters, or whitespace that could be misinterpreted
            if (preg_match('/[\\\\\\x00-\\x1f\\x7f]/', $url)) {
                return $default;
            }

            return $url;
        }

        // For absolute URLs, verify the host matches the current request
        $parsedUrl = parse_url($url);

        // If parsing failed or no host is present, use default
        if (false === $parsedUrl || !isset($parsedUrl['host'])) {
            return $default;
        }

        // Only allow http and https schemes
        if (isset($parsedUrl['scheme']) && !in_array(strtolower($parsedUrl['scheme']), ['http', 'https'], true)) {
            return $default;
        }

        // Reject URLs with @ in the authority component (user:pass@host manipulation)
        if (isset($parsedUrl['user']) || str_contains($url, '@')) {
            return $default;
        }

        // Check if the host matches the current request host
        if (strtolower($parsedUrl['host']) === strtolower($request->getHost())) {
            return $url;
        }

        return $default;
    }
}
