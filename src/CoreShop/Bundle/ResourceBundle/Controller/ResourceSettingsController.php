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

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceSettingsController extends AdminController
{
    public function getNicePathAction(Request $request): Response
    {
        $targets = $this->decodeJson($request->get('targets'));
        $result = [];

        foreach ($targets as $target) {
            $element = Service::getElementById($target['type'], $target['id']);

            if ($element instanceof AbstractElement) {
                $result[$element->getId()] = $element->getFullPath();
            }
        }

        return $this->viewHandler->handle(['success' => true, 'data' => $result]);
    }

    public function getConfigAction(): Response
    {
        $config = [
            'classMap' => [],
            'stack' => [],
        ];

        if ($this->container->hasParameter('coreshop.all.pimcore_classes')) {
            $classes = $this->container->getParameter('coreshop.all.pimcore_classes');

            foreach ($classes as $key => $definition) {
                $alias = explode('.', $key);
                $application = $alias[0];
                $alias = $alias[1];

                $class = str_replace('Pimcore\\Model\\DataObject\\', '', $definition['classes']['model']);
                $class = str_replace('\\', '', $class);

                $config['classMap'][$application][$alias] = $class;
            }

            $stack = $this->container->getParameter('coreshop.all.stack.pimcore_class_names');

            foreach ($stack as $key => $impl) {
                $alias = explode('.', $key);
                $application = $alias[0];
                $alias = $alias[1];

                $config['stack'][$application][$alias] = $impl;
                $config['full_stack'][] = $key;
            }
        }

        return $this->viewHandler->handle($config);
    }
}
