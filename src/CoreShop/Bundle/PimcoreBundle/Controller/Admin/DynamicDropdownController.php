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

namespace CoreShop\Bundle\PimcoreBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\DataObject;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;

final class DynamicDropdownController extends AdminController
{
    private $separator = ' - ';

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    public function optionsAction(Request $request)
    {
        $folderName = $request->get('folderName');
        $parentFolderPath = preg_replace('@[^a-zA-Z0-9/\-_\s]@', '', $folderName);
        $sort = $request->get('sortBy');
        $options = [];

        if ($parentFolderPath) {
            // remove trailing slash
            if ($parentFolderPath != '/') {
                $parentFolderPath = rtrim($parentFolderPath, '/ ');
            }

            // correct wrong path (root-node problem)
            $parentFolderPath = str_replace('//', '/', $parentFolderPath);

            $folder = DataObject\Folder::getByPath($parentFolderPath);

            if ($folder) {
                $options = $this->walkPath($request, $folder);
            } else {
                $message = sprintf('The folder submitted could not be found: "%s"', $folderName);
                \Pimcore\Logger::crit($message);

                return $this->json(
                    [
                        'success' => false,
                        'message' => $message,
                        'options' => $options,
                    ]
                );
            }
        } else {
            $message = sprintf('The folder submitted for parentId is not valid: "%s"', $folderName);
            \Pimcore\Logger::warning($message);

            return $this->json(
                [
                    'success' => false,
                    'message' => $message,
                    'options' => $options,
                ]
            );
        }

        if (!is_null($options)) {
            usort(
                $options,
                function ($a, $b) use ($sort) {
                    $field = 'id';

                    if ($sort == 'byValue') {
                        $field = 'key';
                    }

                    if ($a[$field] == $b[$field]) {
                        return 0;
                    }

                    return $a[$field] < $b[$field] ? 0 : 1;
                }
            );
        }

        return $this->json(
            [
                'success' => true,
                'options' => $options,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function methodsAction(Request $request)
    {
        $availableMethods = [];

        $className = preg_replace("@[^a-zA-Z0-9_\-]@", '', $request->get('className'));

        if (!empty($className)) {
            $class = new \ReflectionClass('\\Pimcore\\Model\\DataObject\\' . ucfirst($className));
            $methods = $class->getMethods();

            $classMethods = array_map(function(\ReflectionMethod $method) {
                return $method->getName();
            }, $methods);

            foreach ($classMethods as $methodName) {
                if (substr($methodName, 0, 3) === 'get') {
                    $availableMethods[] = ['value' => $methodName, 'key' => $methodName];
                }
            }
        }

        return $this->json($availableMethods);
    }

    /**
     * @param Request                   $request
     * @param DataObject\AbstractObject $folder
     * @param array                     $options
     * @param string                    $path
     *
     * @return array
     *
     * @throws \Exception
     */
    private function walkPath(Request $request, DataObject\AbstractObject $folder, $options = [], $path = '')
    {
        $currentLang = $request->get('current_language');
        $source = $request->get('methodName');
        $className = ucfirst($request->get('className'));
        $objectName = 'Pimcore\\Model\\DataObject\\' . $className;

        $usesI18n = false;
        $children = $folder->getChildren();
        if (is_array($children)) {
            foreach ($children as $i18nProbeChild) {
                if ($i18nProbeChild instanceof DataObject\Concrete) {
                    $usesI18n = $this->isUsingI18n($i18nProbeChild, $source);

                    break;
                }
            }
        }

        if (!Tool::isValidLanguage($currentLang)) {
            $currentLang = Tool::getDefaultLanguage();

            if (is_null($currentLang)) {
                $usesI18n = false;
            }
        }

        foreach ($children as $child) {
            /** @var DataObject\Concrete $child */
            $class = get_class($child);
            switch ($class) {
                case DataObject\Folder::class:
                    /**
                     * @var DataObject\Folder $child
                     */
                    $key = $child->getProperty('Taglabel') != '' ? $child->getProperty('Taglabel') : $child->getKey();
                    if ($request->get('recursive') == 'true') {
                        $options = $this->walkPath($request, $child, $options, $path . $this->separator . $key);
                    }

                    break;
                case $objectName:
                    $key = $usesI18n ? $child->$source($currentLang) : $child->$source();
                    $options[] = [
                        'value' => $child->getId(),
                        'key' => ltrim($path . $this->separator . $key, $this->separator),
                        'published' => $child->getPublished(),
                    ];

                    if ($request->get('recursive') == 'true') {
                        $options = $this->walkPath($request, $child, $options, $path . $this->separator . $key);
                    }

                    break;
            }
        }

        return $options;
    }

    /**
     * @param string $class
     *
     * @return array
     */
    private function getThisClassMethods($class)
    {
        $classMethods = get_class_methods($class);

        if ($parentClass = get_parent_class($class)) {
            $parentClassMethods = get_class_methods($parentClass);

            return array_diff($classMethods, $parentClassMethods);
        }

        return $classMethods;
    }

    /**
     * @param DataObject\Concrete $object
     * @param string              $method
     *
     * @return bool
     */
    private function isUsingI18n(DataObject\Concrete $object, $method)
    {
        $classDefinition = $object->getClass();
        $definitionFile = $classDefinition->getDefinitionFile();

        if (!is_file($definitionFile)) {
            return false;
        }

        $tree = include $definitionFile;
        $definition = $this->parseTree($tree, []);

        return $definition[$method];
    }

    /**
     * @param mixed $tree
     * @param mixed $definition
     *
     * @return mixed
     */
    private function parseTree($tree, $definition)
    {
        if ($tree instanceof DataObject\ClassDefinition\Layout || $tree instanceof DataObject\ClassDefinition\Data\Localizedfields) { // Did I forget something?
            $children = $tree->getChildren();
            foreach ($children as $child) {
                $definition['get' . ucfirst($child->name)] = $tree->fieldtype == 'localizedfields';
                $definition = $this->parseTree($child, $definition);
            }
        }

        return $definition;
    }
}
