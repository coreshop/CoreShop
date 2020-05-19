<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Resource\TokenGenerator;

final class UniqueTokenGenerator
{
    private $alphabet;
    private $numbers;
    private $keys;
    private $keyLength;

    /**
     * @param bool $onlyNumbers
     */
    public function __construct($onlyNumbers = false)
    {
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

    /**
     * @param int $length
     *
     * @return string
     */
    public function generate($length)
    {
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $randomKey = $this->getRandomInteger(0, $this->keyLength);
            $token .= $this->keys[$randomKey];
        }

        return $token;
    }

    /**
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    private function getRandomInteger($min, $max)
    {
        $range = ($max - $min);

        if ($range < 0) {
            return $min;
        }

        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);

        return $min + $rnd;
    }
}
