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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Twig;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Model\Staticroute;
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

            $link = '';
            if ($this->getMainRequest()->attributes->get('pimcore_request_source') === 'staticroute') {
                $route = $this->getMainRequest()->attributes->get('_route');
                $staticRoute = Staticroute::getByName($route);
                $params = [];
                if (str_contains($staticRoute->getVariables(), '_locale')) {
                    $params = ['_locale' => $language];
                }
                $link = $this->router->generate($route, $params);
            } else {
                if (isset($translations[$language])) {
                    $localizedDocument = Document::getById($translations[$language]);
                } else {
                    $localizedDocument = Document::getByPath($target);
                }

                if ($localizedDocument instanceof Document && $localizedDocument->getPublished()) {
                    $link = $localizedDocument->getFullPath();
                }
            }

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

    private function getMainRequest(): Request
    {
        $mainRequest = $this->requestStack->getMainRequest();

        if (null === $mainRequest) {
            throw new \UnexpectedValueException('There are not any requests on request stack');
        }

        return $mainRequest;
    }
}
