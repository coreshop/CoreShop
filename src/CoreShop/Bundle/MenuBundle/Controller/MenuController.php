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

namespace CoreShop\Bundle\MenuBundle\Controller;

use CoreShop\Bundle\MenuBundle\Renderer\StudioRenderer;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MenuController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }
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

    public function jsonAction(
        MenuProviderInterface $menuProvider,
        StudioRenderer $jsonRenderer,
        #[Autowire(param: 'coreshop.menus.types')]
        array $menuTypes = [],
    ): JsonResponse {
        $allMenus = [];
        $allItems = [];

        foreach ($menuTypes as $type) {
            try {
                $type = sprintf('coreshop.%s', $type);
                $menu = $menuProvider->get($type);
                $menuData = json_decode($jsonRenderer->render($menu), true);
                $transformedItems = $this->transformMenuForStudio($menuData);

                if (!empty($transformedItems)) {
                    $allMenus[$type] = $transformedItems;
                    $allItems = array_merge($allItems, $transformedItems);
                }
            } catch (\Exception $e) {
                // Log error but continue with other menus
                error_log(sprintf('Failed to load menu "%s": %s', $type, $e->getMessage()));
            }
        }

        return new JsonResponse([
            'success' => true,
            'items' => $allItems,
        ]);
    }

    private function transformMenuForStudio(array $menuData): array
    {
        $items = [];

        if (isset($menuData['children'])) {
            foreach ($menuData['children'] as $child) {
                $items[] = $this->transformMenuItem($child);
            }
        }

        $labelKey = $menuData['label'] ?? $menuData['name'];

        return [
            $menuData['name'] = [
                'id' => $menuData['name'],
                'label' => $this->translator->trans($labelKey, [], 'studio'),
                'content' => $menuData['attributes']['content'] ?? null,
                'children' => $items,
            ]
        ];
    }

    private function transformMenuItem(array $item): array
    {
        $labelKey = $item['label'] ?? $item['name'] ?? 'Unnamed';

        $transformed = [
            'id' => $item['name'] ?? 'unnamed',
            'widgetId' => $item['attributes']['widgetId'] ?? null,
            'widgetEvent' => $item['attributes']['widgetEvent'] ?? null,
            'widgetButton' => $item['attributes']['widgetButton'] ?? null,
            'label' => $this->translator->trans($labelKey, [], 'studio'),
            'path' => $item['uri'] ?? null,
            'icon' => $item['attributes']['iconCls'] ?? null,
            'content' => $item['attributes']['content'] ?? null,
            'disabled' => !($item['display'] ?? true),
        ];

        // Add custom attributes
        if (isset($item['extras'])) {
            if (isset($item['extras']['permission'])) {
                $transformed['permission'] = $item['extras']['permission'];
            }
            if (isset($item['extras']['badge'])) {
                $transformed['badge'] = $item['extras']['badge'];
            }
            if (isset($item['extras']['widgetId'])) {
                $transformed['widgetId'] = $item['extras']['widgetId'];
            }
            if (isset($item['extras']['widgetEvent'])) {
                $transformed['widgetEvent'] = $item['extras']['widgetEvent'];
            }
            if (isset($item['extras']['widgetButton'])) {
                $transformed['widgetButton'] = $item['extras']['widgetButton'];
            }
        }

        // Handle children
        if (isset($item['children']) && !empty($item['children'])) {
            $transformed['children'] = [];
            foreach ($item['children'] as $child) {
                $transformed['children'][] = $this->transformMenuItem($child);
            }
        }

        return $transformed;
    }
}
