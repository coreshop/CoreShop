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

final class TabSchema implements \JsonSerializable
{
    public function __construct(
        public string $key,
        public string $label,
        public int $order = 0,
        public ?string $widget = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'key' => $this->key,
            'label' => $this->label,
            'order' => $this->order,
        ];

        if ($this->widget !== null) {
            $data['widget'] = $this->widget;
        }

        return $data;
    }
}
