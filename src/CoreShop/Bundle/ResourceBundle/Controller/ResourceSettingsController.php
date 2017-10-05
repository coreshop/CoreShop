<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Service;
use Symfony\Component\HttpFoundation\Request;

class ResourceSettingsController extends AdminController
{
    /**
     * @param Request $request
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getNicePathAction(Request $request)
    {
        $targets = $this->decodeJson($request->get("targets"));
        $result = [];

        foreach ($targets as $target) {
            $element = Service::getElementById($target['type'], $target['id']);

            if ($element instanceof AbstractElement) {
                $result[$element->getId()] = $element->getFullPath();
            }
        }

        return $this->json(["success" => true, "data" => $result]);
    }

    /**
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getConfigAction()
    {
        $config = [
            'classMap' => [],
            'implementations' => []
        ];

        if ($this->container->hasParameter('coreshop.pimcore.classes')) {
            $classes = $this->getParameter('coreshop.pimcore.classes');

            foreach ($classes as $key => $definition) {
                $alias = explode('.', $key);
                $alias = $alias[1];

                $class = str_replace('Pimcore\\Model\\DataObject\\', '', $definition['classes']['model']);
                $class = str_replace('\\', '', $class);

                $config['classMap'][$alias] = $class;
            }


            if ($this->container->hasParameter('coreshop.implementations')) {
                $implementations = $this->getParameter('coreshop.implementations');

                foreach ($implementations as $implementation => $interface) {
                    foreach ($classes as $key => $definition) {
                        $class = str_replace('Pimcore\\Model\\DataObject\\', '', $definition['classes']['model']);
                        $class = str_replace('\\', '', $class);

                        if (in_array($interface, class_implements($definition['classes']['model']))) {
                            $config['implementations'][$implementation][] = $class;
                        }
                    }
                }
            }
        }

        return $this->json($config);
    }
}
