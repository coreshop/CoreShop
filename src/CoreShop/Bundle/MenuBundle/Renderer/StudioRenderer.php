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

namespace CoreShop\Bundle\MenuBundle\Renderer;

use CoreShop\Bundle\MenuBundle\Guard\PimcoreGuard;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Renderer\RendererInterface;

class StudioRenderer implements RendererInterface
{
    private MatcherInterface $matcher;
    private PimcoreGuard $guard;

    public function __construct(
        MatcherInterface $matcher,
        PimcoreGuard $guard
    ) {
        $this->matcher = $matcher;
        $this->guard = $guard;
    }

    public function render(ItemInterface $item, array $options = []): string
    {
        $this->reorderMenuItems($item);
        $array = $this->toArray($item, $options);

        return json_encode($array, JSON_THROW_ON_ERROR);
    }

    /**
     * Convert menu item to array
     */
    public function toArray(ItemInterface $item, array $options = []): array
    {
        $data = [
            'name' => $item->getName(),
            'label' => $item->getLabel(),
            'uri' => $item->getUri(),
            'current' => $this->matcher->isCurrent($item),
            'ancestor' => $this->matcher->isAncestor($item),
            'display' => $item->isDisplayed(),
        ];

        // Add extra attributes
        $extras = $item->getExtras();
        if (!empty($extras)) {
            $data['extras'] = $extras;
        }

        // Add attributes
        $attributes = $item->getAttributes();
        if (!empty($attributes)) {
            $data['attributes'] = $attributes;
        }

        // Add link attributes
        $linkAttributes = $item->getLinkAttributes();
        if (!empty($linkAttributes)) {
            $data['linkAttributes'] = $linkAttributes;
        }

        // Add label attributes
        $labelAttributes = $item->getLabelAttributes();
        if (!empty($labelAttributes)) {
            $data['labelAttributes'] = $labelAttributes;
        }

        // Add children recursively with guard checks
        if ($item->hasChildren() && $item->getDisplayChildren()) {
            $data['children'] = [];
            foreach ($item->getChildren() as $child) {
                if ($child->isDisplayed() && $this->guard->matchItem($child)) {
                    $data['children'][] = $this->toArray($child, $options);
                }
            }
        }

        return $data;
    }

    /**
     * Reorder menu items based on 'order' extra attribute
     */
    public function reorderMenuItems(ItemInterface $menu): void
    {
        $menuOrderArray = [];
        $addLast = [];
        $alreadyTaken = [];

        foreach ($menu->getChildren() as $menuItem) {
            if ($menuItem->hasChildren()) {
                $this->reorderMenuItems($menuItem);
            }

            $orderNumber = $menuItem->getExtra('order');

            if ($orderNumber !== null) {
                if (!isset($menuOrderArray[$orderNumber])) {
                    $menuOrderArray[$orderNumber] = $menuItem->getName();
                } else {
                    $alreadyTaken[$orderNumber] = $menuItem->getName();
                }
            } else {
                $addLast[] = $menuItem->getName();
            }
        }

        // Sort them after first pass
        ksort($menuOrderArray);

        // Handle position duplicates
        if (count($alreadyTaken) > 0) {
            foreach ($alreadyTaken as $key => $value) {
                $keysArray = array_keys($menuOrderArray);
                $position = array_search($key, $keysArray, true);

                if ($position !== false) {
                    $menuOrderArray = array_merge(
                        array_slice($menuOrderArray, 0, $position),
                        [$value],
                        array_slice($menuOrderArray, $position)
                    );
                }
            }
        }

        // Sort them after second pass
        ksort($menuOrderArray);

        // Add items without order number to the end
        if (count($addLast) > 0) {
            foreach ($addLast as $value) {
                $menuOrderArray[] = $value;
            }
        }

        if (count($menuOrderArray) > 0) {
            $menu->reorderChildren($menuOrderArray);
        }
    }
}
