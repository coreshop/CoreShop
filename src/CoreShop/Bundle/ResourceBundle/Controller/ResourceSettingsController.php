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

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceSettingsController extends AdminController
{
    public function getNicePathAction(Request $request): Response
    {
        $targets = $this->decodeJson($this->getParameterFromRequest($request, 'targets'));
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
            /**
             * @var array $classes
             */
            $classes = $this->container->getParameter('coreshop.all.pimcore_classes');

            foreach ($classes as $key => $definition) {
                $alias = explode('.', $key);
                $application = $alias[0];
                $alias = $alias[1];

                $class = str_replace('Pimcore\\Model\\DataObject\\', '', $definition['classes']['model']);
                $class = str_replace('\\', '', $class);

                $config['classMap'][$application][$alias] = $class;
            }

            /**
             * @var array $stack
             */
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
