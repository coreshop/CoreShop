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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use Pimcore\Model;
use Pimcore\Model\DataObject\Concrete;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
abstract class Multiselect extends Model\DataObject\ClassDefinition\Data\Multiselect
{
    /**
     * Storage marker for an explicitly empty selection.
     *
     * Pimcore's Multiselect persists the selection as a comma separated list and reads an empty
     * string back as null, which makes "nothing selected" indistinguishable from "never set" and
     * therefore always triggers inheritance from the parent object.
     *
     * A single comma cannot be produced by imploding a selection, because Pimcore rejects option
     * values containing a comma (see Model\DataObject\ClassDefinition\Data\Multiselect::preSave()),
     * so it is safe to use as the marker for an explicitly empty selection.
     */
    public const EXPLICITLY_EMPTY = ',';

    public function isDiffChangeAllowed($object, $params = []): bool
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = []): ?array
    {
        return [];
    }

    /**
     * Not called by Pimcore: the generated getter only calls preGetData() if the field definition
     * implements PreGetDataInterface, which this one deliberately does not. Kept for BC only.
     *
     * @deprecated will be removed in a future major version
     *
     * @param mixed $object
     * @param array $params
     *
     * @return mixed
     */
    public function preGetData($object, $params = [])
    {
        if (!$object instanceof Model\AbstractModel) {
            return null;
        }

        return $object->getObjectVar($this->getName());
    }

    /**
     * Decides whether Pimcore's inheritance kicks in. An array (including an empty one) is an
     * explicit selection and must never inherit, everything else (null, '') is "not set".
     *
     * Called both with the object value (array|null) and, from the InheritanceHelper, with the raw
     * storage value (string|null), hence both shapes are handled here.
     */
    public function isEmpty(mixed $data): bool
    {
        if (is_array($data)) {
            return false;
        }

        if (self::EXPLICITLY_EMPTY === $data) {
            return false;
        }

        return parent::isEmpty($data);
    }

    public function getDataForResource(
        mixed $data,
        ?Concrete $object = null,
        array $params = [],
    ): ?string {
        if ([] === $data) {
            return self::EXPLICITLY_EMPTY;
        }

        return parent::getDataForResource($data, $object, $params);
    }

    public function getDataFromResource(
        mixed $data,
        ?Concrete $object = null,
        array $params = [],
    ): ?array {
        if (self::EXPLICITLY_EMPTY === $data) {
            return [];
        }

        return parent::getDataFromResource($data, $object, $params);
    }

    /**
     * The query table is only used for grid display and filtering. Filters match against ",value,",
     * which the marker must not satisfy, so an explicitly empty selection is stored as null there.
     */
    public function getDataForQueryResource(
        mixed $data,
        ?Concrete $object = null,
        array $params = [],
    ): ?string {
        if ([] === $data) {
            return null;
        }

        return parent::getDataForQueryResource($data, $object, $params);
    }

    /**
     * Keeps the storage marker out of the edit mode payload, an empty selection is an empty string.
     */
    public function getDataForEditmode(
        mixed $data,
        ?Concrete $object = null,
        array $params = [],
    ): ?string {
        if ([] === $data) {
            return '';
        }

        return parent::getDataForEditmode($data, $object, $params);
    }
}
