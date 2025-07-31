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

namespace CoreShop\Bundle\ResourceBundle;

class ResourcePermission
{
    public const CREATE = 'create';

    public const EDIT = 'edit';

    public const LIST = 'list';

    public const VIEW = 'view';

    public const DELETE = 'delete';

    public static function getAllPermissions(): array
    {
        return [
            self::CREATE,
            self::EDIT,
            self::LIST,
            self::VIEW,
            self::DELETE,
        ];
    }
}
