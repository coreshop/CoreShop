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

namespace CoreShop\Component\Resource\Doctrine\Type;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class BigIntIntegerType extends Type
{
    public const BIGINT_INTEGER = 'bigintInteger';

    public function getName()
    {
        return self::BIGINT_INTEGER;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getBigIntTypeDeclarationSQL($column);
    }

    public function getBindingType()
    {
        return ParameterType::STRING;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : (int) $value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
