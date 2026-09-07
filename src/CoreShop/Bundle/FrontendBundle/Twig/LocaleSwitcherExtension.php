<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Twig;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LocaleSwitcherExtension extends AbstractExtension
{
    public function __construct(
        private Document\Service $documentService,
        private ShopperContextInterface $shopperContext,
        private RequestStack $requestStack,
        private RouterInterface $router,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_locale_switcher', [$this, 'getLocalizedLinks']),
        ];
    }

    public function getLocalizedLinks(Document $document): array
    {
        $translations = $this->documentService->getTranslations($document);
        $links = [];
        $basePath = '/';

        $store = $this->shopperContext->getStore();

        if ($store->getSiteId()) {
            try {
                $site = Site::getById($store->getSiteId());

                if ($site instanceof Site) {
                    $basePath = $site->getRootDocument()->getRealFullPath() . '/';
                }
            } catch (\Exception) {
                $basePath = '/';
            }
        }

        $object = $this->getMainRequest()->attributes->get('object');

        foreach (Tool::getValidLanguages() as $language) {
            $target = $basePath . $language;

            if ($object instanceof SluggableInterface) {
                $urlSlug = $object->getSlug($language)[0] ?? null;

                if ($urlSlug instanceof UrlSlug) {
                    $links[] = [
                        'language' => $language,
                        'target' => $urlSlug->getSlug(),
                        'displayLanguage' => \Locale::getDisplayLanguage($language, $language),
                    ];
                }

                continue;
            }

            $link = $this->resolveLocalizedLink($language, $target, $translations);

            if (!empty($link)) {
                $links[] = [
                    'language' => $language,
                    'target' => $link,
                    'displayLanguage' => \Locale::getDisplayLanguage($language, $language),
                ];
            }
        }

        return $links;
    }

    private function resolveLocalizedLink(string $language, string $target, array $translations): string
    {
        $request = $this->getMainRequest();
        $route = $request->attributes->get('_route');

        if (is_string($route) && $this->routeAcceptsLocale($route)) {
            $params = $request->attributes->get('_route_params', []);
            $params['_locale'] = $language;

            return $this->router->generate($route, $params);
        }

        $localizedDocument = isset($translations[$language])
            ? Document::getById($translations[$language])
            : Document::getByPath($target);

        if ($localizedDocument instanceof Document && $localizedDocument->getPublished()) {
            return $localizedDocument->getFullPath();
        }

        return '';
    }

    private function routeAcceptsLocale(string $routeName): bool
    {
        $route = $this->router->getRouteCollection()->get($routeName);

        if (null === $route) {
            return false;
        }

        return in_array('_locale', $route->compile()->getVariables(), true);
    }

    private function getMainRequest(): Request
    {
        $mainRequest = $this->requestStack->getMainRequest();

        if (null === $mainRequest) {
            throw new \UnexpectedValueException('There are not any requests on request stack');
        }

        return $mainRequest;
    }
}
