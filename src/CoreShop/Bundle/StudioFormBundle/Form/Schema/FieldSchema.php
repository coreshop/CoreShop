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

    /** @var array<string, mixed>|null */
    public ?array $choices = null;

    public bool $multiple = false;

    public bool $expanded = false;

    /** @var array<string, mixed> */
    public array $extra = [];

    public ?FormSchema $prototype = null;

    /** @var array<string, FormSchema>|null */
    public ?array $prototypes = null;

    /**
     * @param string[] $blockPrefixes
     */
    public function __construct(
        public string $name,
        public array $blockPrefixes,
        public bool $required = false,
        public ?string $label = null,
        public bool $disabled = false,
        public ?FormSchema $children = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'name' => $this->name,
            'blockPrefixes' => $this->blockPrefixes,
            'required' => $this->required,
        ];

        if ($this->label !== null) {
            $data['label'] = $this->label;
        }

        if ($this->disabled) {
            $data['disabled'] = true;
        }

        if ($this->choices !== null) {
            $data['choices'] = $this->choices;
            $data['multiple'] = $this->multiple;
            $data['expanded'] = $this->expanded;
        }

        if (count($this->extra) > 0) {
            $data['extra'] = $this->extra;
        }

        if ($this->children !== null) {
            $data['children'] = $this->children;
        }

        if ($this->prototype !== null) {
            $data['prototype'] = $this->prototype;
        }

        if ($this->prototypes !== null) {
            $data['prototypes'] = $this->prototypes;
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
