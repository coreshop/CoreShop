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

namespace CoreShop\Component\Pimcore\DataObject;

class ClassLoader
{
    /**
     * Force loads a class, this makes sense if a class is installed and needs to be used in the same request
     *
     * @param $className
     */
    public static function forceLoadDataObjectClass($className)
    {
        $className = static::normalizeClassName($className);

        $fqcp = sprintf('%s/DataObject/%s.php', PIMCORE_CLASS_DIRECTORY, $className);
        $fqcn = sprintf('\\Pimcore\\Model\\DataObject\\%s', $className);

        static::loadClass($fqcp, $fqcn);
    }

    /**
     * Force loads a field-collection, this makes sense if a class is installed and needs to be used in the same request
     *
     * @param $fieldCollection
     */
    public static function forceLoadFieldCollection($fieldCollection)
    {
        $className = static::normalizeClassName($fieldCollection);

        $fqcp = sprintf('%s/DataObject/Fieldcollection/Data/%s.php', PIMCORE_CLASS_DIRECTORY, $className);
        $fqcn = sprintf('\\Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\%s', $className);

        static::loadClass($fqcp, $fqcn);
    }

    /**
     * Force loads a bick, this makes sense if a class is installed and needs to be used in the same request
     *
     * @param $brickName
     */
    public static function forceLoadBrick($brickName)
    {
        $className = static::normalizeClassName($brickName);

        $fqcp = sprintf('%s/DataObject/Objectbrick/Data/%s.php', PIMCORE_CLASS_DIRECTORY, $className);
        $fqcn = sprintf('\\Pimcore\\Model\\DataObject\\Objectbrick\\Data\\%s', $className);

        static::loadClass($fqcp, $fqcn);
    }

    /**
     * Require class
     *
     * @param $fileName
     * @param $className
     */
    protected static function loadClass($fileName, $className)
    {
        if (file_exists($fileName) && !class_exists($className)) {
            require_once $fileName;
        }
    }

    /**
     * Normalize a Pimcore DataObject ClassName
     *
     * @param $className
     * @return string
     */
    protected static function normalizeClassName($className)
    {
        $classNameExploded = explode('\\', $className);
        return ucfirst($classNameExploded[count($classNameExploded) - 1]);
    }
}

class_alias(ClassLoader::class, 'CoreShop\Component\Pimcore\ClassLoader');