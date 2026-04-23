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

final class FormSchema implements \JsonSerializable
{
    /**
     * @param FieldSchema[] $fields
     * @param TabSchema[] $tabs
     * @param SectionSchema[] $sections
     */
    public function __construct(
        public string $blockPrefix,
        public array $fields = [],
        public array $tabs = [],
        public array $sections = [],
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'blockPrefix' => $this->blockPrefix,
            'fields' => $this->fields,
            'tabs' => $this->tabs,
            'sections' => $this->sections,
        ];
    }
}
