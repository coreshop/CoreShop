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

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;

/**
 * @experimental Use with caution only, this is a new experimental feature
 */
interface DefinitionFieldReNamerInterface
{
    public function rename(): void;

    public function getOldFieldName(): string;

    public function getNewFieldName(): string;

    public function getDefinition(): ClassDefinition|Fieldcollection\Definition|Objectbrick\Definition;
}
