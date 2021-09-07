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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject;

interface ClassInstallerInterface
{
    public function createBrick(string $jsonFile, string $brickName): DataObject\Objectbrick\Definition;

    public function createClass(string $jsonFile, string $className, bool $updateClass = false): DataObject\ClassDefinition;

    public function createFieldCollection(string $jsonFile, string $name): DataObject\Fieldcollection\Definition;
}
