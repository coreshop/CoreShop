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

namespace CoreShop\Component\Resource\Model;

interface ResourceInterface
{
    /**
     * Can be a string or int or null, depending on where we store the data
     * DataDefinitions for example uses a string identifier
     */
    public function getId(): int|string|null;

    public function setValues(array $data = []): static;

    public function setValue(string $key, mixed $value, bool $ignoreEmptyValues = false): static;
}
