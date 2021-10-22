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

namespace CoreShop\Bundle\MenuBundle\Renderer;

use CoreShop\Bundle\MenuBundle\Guard\PimcoreGuard;
use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererInterface;
use Twig\Environment;

class JsonRenderer implements RendererInterface
{
    private array $defaultOptions;

    public function __construct(
        private Environment $environment,
        string $template,
        private PimcoreGuard $guard,
        array $defaultOptions = []
    ) {
        $this->defaultOptions = array_merge([
            'depth' => null,
            'matchingDepth' => null,
            'template' => $template,
            'compressed' => false,
            'clear_matcher' => true,
        ], $defaultOptions);
    }

    public function render(ItemInterface $item, array $options = []): string
    {
        $options = array_merge($this->defaultOptions, $options);

        $this->reorderMenuItems($item);

        $items = $this->recursiveProcessMenuItems($item);

        return $this->environment->render(
            $options['template'],
            [
                'item' => $this->renderItem($item),
                'items' => $items,
                'options' => $options,
            ]
        );
    }

    protected function renderItem(ItemInterface $item): array
    {
        return [
            'id' => strtolower($item->getName()),
            'name' => $item->getLabel(),
            'attributes' => $item->getAttributes(),
        ];
    }

    protected function recursiveProcessMenuItems(ItemInterface $item): array
    {
        $items = [];

        foreach ($item->getChildren() as $menuItem) {
            if (!$this->guard->matchItem($menuItem)) {
                continue;
            }

            $itemData = $this->renderItem($menuItem);
            $children = $this->recursiveProcessMenuItems($menuItem);

            if (count($children) > 0) {
                $itemData['children'] = $children;
            }

            $items[] = $itemData;
        }

        return $items;
    }

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

            if (null != $orderNumber) {
                if (!isset($menuOrderArray[$orderNumber])) {
                    $menuOrderArray[$orderNumber] = $menuItem->getName();
                } else {
                    $alreadyTaken[$orderNumber] = $menuItem->getName();
                    // $alreadyTaken[] = array('orderNumber' => $orderNumber, 'name' => $menuItem->getName());
                }
            } else {
                $addLast[] = $menuItem->getName();
            }
        }

        // sort them after first pass
        ksort($menuOrderArray);

        // handle position duplicates
        if (count($alreadyTaken)) {
            foreach ($alreadyTaken as $key => $value) {
                // the ever shifting target
                $keysArray = array_keys($menuOrderArray);

                $position = array_search($key, $keysArray);

                if (false === $position) {
                    continue;
                }

                $menuOrderArray = array_merge(
                    array_slice($menuOrderArray, 0, $position),
                    [$value],
                    array_slice($menuOrderArray, $position)
                );
            }
        }

        // sort them after second pass
        ksort($menuOrderArray);

        // add items without ordernumber to the end
        if (count($addLast)) {
            foreach ($addLast as $value) {
                $menuOrderArray[] = $value;
            }
        }

        if (count($menuOrderArray)) {
            $menu->reorderChildren($menuOrderArray);
        }
    }
}
