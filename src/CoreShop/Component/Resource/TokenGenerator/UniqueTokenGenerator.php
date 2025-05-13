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

        for ($i = 0; $i < $length; ++$i) {
            $randomKey = $this->getRandomInteger($this->keyLength);
            $token .= $this->keys[$randomKey];
        }

        return $token;
    }

    private function getRandomInteger(int $max): int
    {
        $range = ($max - 0);

        if ($range < 0) {
            return 0;
        }

        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);

        return 0 + $rnd;
    }
}
