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

namespace CoreShop\Component\Resource\TokenGenerator;

final class UniqueTokenGenerator
{
    private string $alphabet;

    private string $numbers;

    private string $keys;

    private int $keyLength;

    public function __construct(
        bool $onlyNumbers = false,
    ) {
        $this->alphabet =
            implode(range('a', 'z'))
            . implode(range('A', 'Z'));

        $this->numbers = implode(range(0, 9));

        if ($onlyNumbers === false) {
            $this->keys = $this->alphabet . $this->numbers;
        } else {
            $this->keys = $this->numbers;
        }

        $this->keyLength = strlen($this->keys);
    }

    public function generate(int $length): string
    {
        $token = '';
        $maxIndex = $this->keyLength - 1;

        for ($i = 0; $i < $length; ++$i) {
            $randomKey = random_int(0, $maxIndex);
            $token .= $this->keys[$randomKey];
        }

        return $token;
    }
}
