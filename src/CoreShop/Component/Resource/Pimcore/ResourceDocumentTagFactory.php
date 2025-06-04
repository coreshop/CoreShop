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

namespace CoreShop\Component\Resource\Pimcore;

use CoreShop\Component\Pimcore\Document\DocumentTagFactoryInterface;
use Pimcore\Model\Document\Editable\EditableInterface;

class ResourceDocumentTagFactory implements DocumentTagFactoryInterface
{
    /**
     * @psalm-param class-string $class
     */
    public function __construct(
        private string $class,
        private string $repositoryName,
        private string $nameProperty,
    ) {
    }

    public function create(string $type, array $params): EditableInterface
    {
        array_unshift($params, $type);
        array_unshift($params, $this->nameProperty);
        array_unshift($params, $this->repositoryName);

        $className = $this->class;

        return new $className(...$params);
    }
}
