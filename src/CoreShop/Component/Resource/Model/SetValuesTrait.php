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

trait SetValuesTrait
{
    public function setValues(array $data = []): static
    {
        foreach ($data as $key => $value) {
            $this->setValue($key, $value);
        }

        return $this;
    }

    public function setValue(string $key, mixed $value, bool $ignoreEmptyValues = false): static
    {
        $method = 'set' . $key;
        if (method_exists($this, $method)) {
            $this->$method($value);
        } elseif (method_exists($this, 'set' . preg_replace('/^o_/', '', $key))) {
            // compatibility mode for objects (they do not have any set_oXyz() methods anymore)
            $this->$method($value);
        }

        return $this;
    }
}
