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

namespace CoreShop\Component\Variant\Model;

use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

abstract class AttributeGroup extends AbstractPimcoreModel implements AttributeGroupInterface
{
    public function getAttributes(): array
    {
        $attributes = [];
        foreach ($this->getChildren([self::OBJECT_TYPE_OBJECT]) as $object) {
            if ($object instanceof AttributeInterface) {
                $attributes[] = $object;
            }
        }

        return $attributes;
    }
}
