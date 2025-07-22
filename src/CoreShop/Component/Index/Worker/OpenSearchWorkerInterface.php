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

namespace CoreShop\Component\Index\Worker;

interface OpenSearchWorkerInterface extends WorkerInterface
{
    public const string FIELD_TYPE_NULL = 'null';

    public const string FIELD_TYPE_BOOLEAN = 'boolean';

    public const string FIELD_TYPE_FLOAT = 'float';

    public const string FIELD_TYPE_DOUBLE = 'double';

    public const string FIELD_TYPE_INTEGER = 'integer';

    public const string FIELD_TYPE_OBJECT = 'object';

    public const string FIELD_TYPE_NESTED = 'nested';

    public const string FIELD_TYPE_TEXT = 'text';

    public const string FIELD_TYPE_KEYWORD = 'keyword';
}
