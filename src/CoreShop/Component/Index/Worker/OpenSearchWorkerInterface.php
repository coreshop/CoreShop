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

namespace CoreShop\Component\Index\Worker;

interface OpenSearchWorkerInterface extends WorkerInterface
{
    public const FIELD_TYPE_NULL = 'null';

    public const FIELD_TYPE_BOOLEAN = 'boolean';

    public const FIELD_TYPE_FLOAT = 'float';

    public const FIELD_TYPE_DOUBLE = 'double';

    public const FIELD_TYPE_INTEGER = 'integer';

    public const FIELD_TYPE_OBJECT = 'object';

    public const FIELD_TYPE_NESTED = 'nested';

    public const FIELD_TYPE_TEXT = 'text';

    public const FIELD_TYPE_KEYWORD = 'keyword';
}
