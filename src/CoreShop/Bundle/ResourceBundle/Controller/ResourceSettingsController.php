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

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Service;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;

class ResourceSettingsController extends AdminController
{
    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getNicePathAction(Request $request)
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

    /**
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getConfigAction()
    {
        $config = [
            'classMap' => [],
            'stack' => [],
        ];

        /**
         * @var array $classes
         */
        $classes =  $this->container->hasParameter('coreshop.all.pimcore_classes');

        if ($classes) {
            foreach ($classes as $key => $definition) {
                $alias = explode('.', $key);
                $application = $alias[0];
                $alias = $alias[1];

                $lastBackslash = strrpos($definition['classes']['model'], '\\');
                if($lastBackslash !== false) {
                    $class = substr($definition['classes']['model'], $lastBackslash + 1);
                } else {
                    $class = $definition['classes']['model'];
                }

                try {
                    $reflectionClass = new ReflectionClass($definition['classes']['model']);
                    $classStackPimcoreClassName[$alias][] = $reflectionClass->getDefaultProperties()['o_className'] ?? $definition['classes']['model'];
                } catch(ReflectionException $e) {
                    $classStackPimcoreClassName[$alias][] = $definition['classes']['model'];
                }

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
            }
        }

        return $this->viewHandler->handle($config);
    }
}
