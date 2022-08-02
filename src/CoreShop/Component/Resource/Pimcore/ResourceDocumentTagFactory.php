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
        private string $nameProperty
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
