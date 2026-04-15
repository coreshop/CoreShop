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

namespace CoreShop\Component\Registry;

class NonExistingServiceException extends \InvalidArgumentException
{
    public function __construct(
        $context,
        $type,
        array $existingServices,
    ) {
        parent::__construct(sprintf(
            '%s service "%s" does not exist, available %s services: "%s"',
            ucfirst($context),
            $type,
            $context,
            implode('", "', $existingServices),
        ));
    }
}
