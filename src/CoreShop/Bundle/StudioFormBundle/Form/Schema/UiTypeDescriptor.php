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

final class UiTypeDescriptor implements \JsonSerializable
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public string $widget,
        public array $options = [],
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = ['widget' => $this->widget];

        if (count($this->options) > 0) {
            $data = array_merge($data, $this->options);
        }

        return $data;
    }
}
