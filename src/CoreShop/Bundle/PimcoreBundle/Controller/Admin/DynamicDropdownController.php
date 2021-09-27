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

namespace CoreShop\Bundle\PimcoreBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Factory;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class DynamicDropdownController extends AdminController
{
    private string $separator = ' - ';

    public function optionsAction(Request $request): JsonResponse
    {
        $folderName = $request->get('folderName');
        $parts = array_map(static function (string $part) {
            return Service::getValidKey($part, 'object');
        }, preg_split('/\//', $folderName, 0, PREG_SPLIT_NO_EMPTY));
        $parentFolderPath = sprintf('/%s', implode('/', $parts));
        $sort = $request->get('sortBy');
        $options = [];

        if ($parentFolderPath) {
            // remove trailing slash
            if ($parentFolderPath !== '/') {
                $parentFolderPath = rtrim($parentFolderPath, '/ ');
            }

            // correct wrong path (root-node problem)
            $parentFolderPath = str_replace('//', '/', $parentFolderPath);

            $folder = DataObject\Folder::getByPath($parentFolderPath);
            $options = $folder instanceof DataObject\AbstractObject ? $this->walkPath($request, $folder) : [];
        } else {
            $message = sprintf('The folder submitted for parentId is not valid: "%s"', $folderName);

            return $this->json(
                [
                    'success' => false,
                    'message' => $message,
                    'options' => $options,
                ]
            );
        }

        usort(
            $options,
            static function (array $a, array $b) use ($sort) {
                $field = 'value';

                if (strtolower($sort) === 'byvalue') {
                    $field = 'key';
                }

                if ($a[$field] === $b[$field]) {
                    return 0;
                }

                return $a[$field] < $b[$field] ? 0 : 1;
            }
        );

        return $this->json(
            [
                'success' => true,
                'options' => $options,
            ]
        );
    }

    public function methodsAction(Request $request, Factory $modelFactory): JsonResponse
    {
        $availableMethods = [];

        $className = preg_replace("@[^a-zA-Z0-9_\-]@", '', $request->get('className'));

        if (!empty($className)) {
            /**
             * @psalm-var class-string $fqcn
             */
            $fqcn = '\\Pimcore\\Model\\DataObject\\' . ucfirst($className);
            /**
             * @psalm-suppress InternalMethod
             */
            $instance = $modelFactory->build($fqcn);

            $class = new \ReflectionClass(get_class($instance));
            $methods = $class->getMethods();

            $classMethods = array_map(function (\ReflectionMethod $method) {
                return $method->getName();
            }, $methods);

            foreach ($classMethods as $methodName) {
                if (str_starts_with($methodName, 'get')) {
                    $availableMethods[] = ['value' => $methodName, 'key' => $methodName];
                }
            }
        }

        return $this->json($availableMethods);
    }

    private function walkPath(Request $request, DataObject\AbstractObject $folder, array $options = [], string $path = ''): array
    {
        $currentLang = $request->get('current_language');
        $source = $request->get('methodName');
        $className = preg_replace("@[^a-zA-Z0-9_\-]@", '', $request->get('className'));

        if (empty($className)) {
            throw new \InvalidArgumentException();
        }

        /**
         * @psalm-var class-string $className
         */
        $fqcn = '\\Pimcore\\Model\\DataObject\\' . ucfirst($className);

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
            if ($child instanceof DataObject\Folder) {
                /**
                 * @var DataObject\Folder $child
                 */
                $key = $child->getProperty('Taglabel') != '' ? $child->getProperty('Taglabel') : $child->getKey();
                if ($request->get('recursive') === 'true') {
                    $options = $this->walkPath($request, $child, $options, $path . $this->separator . $key);
                }
            }
            else if ($child instanceof $fqcn) {
                $key = $usesI18n ? $child->$source($currentLang) : $child->$source();
                $options[] = [
                    'value' => $child->getId(),
                    'key' => ltrim($path . $this->separator . $key, $this->separator),
                    'published' => $child instanceof DataObject\Concrete ? $child->getPublished() : false,
                ];

                if ($request->get('recursive') === 'true') {
                    $options = $this->walkPath($request, $child, $options, $path . $this->separator . $key);
                }
            }
        }

        return $options;
    }

    private function isUsingI18n(DataObject\Concrete $object, string $method): bool
    {
        $classDefinition = $object->getClass();
        /**
         * @psalm-suppress InternalMethod
         */
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
    private function parseTree(mixed $tree, mixed $definition)
    {
        if ($tree instanceof DataObject\ClassDefinition\Layout || $tree instanceof DataObject\ClassDefinition\Data\Localizedfields) { // Did I forget something?
            $children = $tree->getChildren();
            foreach ($children as $child) {
                /**
                 * @psalm-suppress InternalProperty, UndefinedPropertyFetch
                 */
                $definition['get' . ucfirst($child->name)] = $tree->fieldtype === 'localizedfields';
                $definition = $this->parseTree($child, $definition);
            }
        }

        return $definition;
    }
}
