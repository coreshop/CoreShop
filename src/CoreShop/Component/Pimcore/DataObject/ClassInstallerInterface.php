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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject;

interface ClassInstallerInterface
{
    public function createBrick(string $jsonFile, string $brickName): DataObject\Objectbrick\Definition;

    public function createClass(string $jsonFile, string $className, bool $updateClass = false): DataObject\ClassDefinition;

    public function createFieldCollection(string $jsonFile, string $name): DataObject\Fieldcollection\Definition;
}
