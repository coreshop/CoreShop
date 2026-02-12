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

namespace CoreShop\Bundle\StudioFormBundle\Form\Schema;

final class FieldSchema implements \JsonSerializable
{
    public ?string $tab = null;

    public ?string $section = null;

    /**
     * @param FieldSchema[]|null $fields
     */
    public function __construct(
        public string $name,
        public string $blockPrefix,
        public bool $required,
        public UiTypeDescriptor $uiType,
        public ?FormSchema $children = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'name' => $this->name,
            'blockPrefix' => $this->blockPrefix,
            'required' => $this->required,
            'uiType' => $this->uiType,
        ];

        if ($this->children !== null) {
            $data['children'] = $this->children;
        }

        if ($this->tab !== null) {
            $data['tab'] = $this->tab;
        }

        if ($this->section !== null) {
            $data['section'] = $this->section;
        }

        return $data;
    }
}
