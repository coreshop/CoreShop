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

namespace CoreShop\Bundle\IndexBundle\Order\Mysql;

use CoreShop\Component\Index\Order\DynamicOrderRendererInterface;
use Doctrine\DBAL\Connection;

abstract class AbstractMysqlDynamicRenderer implements DynamicOrderRendererInterface
{
    public function __construct(
        protected Connection $connection,
    ) {
    }

    protected function quoteIdentifier(string $identifier): string
    {
        return $this->connection->quoteIdentifier($identifier);
    }

    protected function renderPrefix(?string $prefix): string
    {
        if (null === $prefix) {
            return '';
        }

        return $prefix . '.';
    }

    protected function quoteFieldName(string $fieldName, ?string $prefix = null): string
    {
        return $this->renderPrefix($prefix) . $this->quoteIdentifier($fieldName);
    }
}
