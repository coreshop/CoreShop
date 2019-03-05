<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\MenuBundle\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Renderer\RendererInterface;

class JsonRenderer implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var MatcherInterface
     */
    private $matcher;

    /**
     * @var array
     */
    private $defaultOptions;

    /**
     * @param \Twig_Environment $environment
     * @param string            $template
     * @param MatcherInterface  $matcher
     * @param array             $defaultOptions
     */
    public function __construct(
        \Twig_Environment $environment,
        $template,
        MatcherInterface $matcher,
        array $defaultOptions = array()
    ) {
        $this->environment = $environment;
        $this->matcher = $matcher;
        $this->defaultOptions = array_merge(array(
            'depth' => null,
            'matchingDepth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'template' => $template,
            'compressed' => false,
            'allow_safe_labels' => false,
            'clear_matcher' => true,
            'leaf_class' => null,
            'branch_class' => null,
        ), $defaultOptions);

    }

    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->defaultOptions, $options);

        $items = $this->recursiveProcessMenuItems($item);

        $html = $this->environment->render($options['template'],
            [
                'item' => $this->renderItem($item),
                'items' => $items,
                'options' => $options,
                'matcher' => $this->matcher
            ]
        );

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }

    protected function renderItem(ItemInterface $item)
    {
        return [
            'id' => strtolower($item->getName()),
            'name' => $item->getLabel(),
            'attributes' => $item->getAttributes(),
        ];
    }

    protected function recursiveProcessMenuItems(ItemInterface $item)
    {
        $items = [];

        foreach ($item->getChildren() as $menuItem) {
            if (!$this->matcher->isCurrent(($menuItem))) {
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
}
