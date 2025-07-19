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

interface MysqlWorkerInterface extends WorkerInterface
{
    /**
     * Field Type Integer for Index.
     */
    public const FIELD_TYPE_INTEGER = 'INTEGER';

    /**
     * Field Type Double for Index.
     */
    public const FIELD_TYPE_DOUBLE = 'DOUBLE';

    /**
     * Field Type String for Index.
     */
    public const FIELD_TYPE_STRING = 'STRING';

    /**
     * Field Type Text for Index.
     */
    public const FIELD_TYPE_TEXT = 'TEXT';

    /**
     * Field Type Boolean for Index.
     */
    public const FIELD_TYPE_BOOLEAN = 'BOOLEAN';

    /**
     * Field Type Date for Index.
     */
    public const FIELD_TYPE_DATE = 'DATE';
}
