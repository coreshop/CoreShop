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

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use Pimcore\Model\DataObject;
use Pimcore\Model\Element;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement, InvalidArgument, MissingConstructor
 */
class DynamicDropdown extends DataObject\ClassDefinition\Data\ManyToOneRelation
{
    use DynamicDropdownTrait;

    public string $fieldtype = 'coreShopDynamicDropdown';

    public function getFieldType(): string
    {
        return $this->fieldtype;
    }

    public function getDataFromEditmode(mixed $data, ?DataObject\Concrete $object = null, array $params = []): null|\Pimcore\Model\Asset|DataObject\AbstractObject|\Pimcore\Model\Document
    {
        if (empty($data) || !is_numeric($data)) {
            return null;
        }

        return Element\Service::getElementById('object', $data);
    }
}
