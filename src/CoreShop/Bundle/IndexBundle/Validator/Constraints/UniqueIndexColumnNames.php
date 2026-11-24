<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Column names become the column names of the index table, so they have to be unique
 * within an index. Several columns may still use the same class field.
 */
final class UniqueIndexColumnNames extends Constraint
{
    public string $message = 'Column name "%name%" is used more than once in this index.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
