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

namespace CoreShop\Bundle\ResourceBundle\Pimcore;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Traits\LocateFileTrait;

class ClassIdResolver implements ClassIdResolverInterface
{
    use LocateFileTrait;

    public function resolveClassId(string $className): string
    {
        $classDefinitionFile = $this->locateDefinitionFile($className, 'definition_%s.php');
        $classDefinitionFile = $this->findCaseInsensitive($classDefinitionFile);

        if (!$classDefinitionFile) {
            return $className;
        }

        $tokens = token_get_all(file_get_contents($classDefinitionFile));

        $foundName = false;
        $foundClassDefinition = false;

        foreach ($tokens as $token) {
            [$type, $statement] = $token;

            if ($statement === 'Pimcore\Model\DataObject\ClassDefinition') {
                $foundClassDefinition = true;
                continue;
            }

            if (!$foundClassDefinition) {
                continue;
            }

            if ($statement === '\'name\'') {
                $foundName = true;
                continue;
            }

            if (!$foundName) {
                continue;
            }

            if ($type === 269) {
                return str_replace('\'', '', $statement);
            }
        }

        return $className;
    }

    protected function findCaseInsensitive(string $fileName)
    {
        // Handle case insensitive requests
        $directoryName = dirname($fileName);
        $fileArray = glob($directoryName.'/*', GLOB_NOSORT);
        $fileNameLowerCase = strtolower($fileName);
        foreach ($fileArray as $file) {
            if (strtolower($file) === $fileNameLowerCase) {
                return $file;
            }
        }

        return false;
    }
}