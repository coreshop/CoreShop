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

namespace CoreShop\Behat\Service;


final class NotificationType
{
    /** @var string */
    private string $value;

    /** @var array */
    private static array $types = [];

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function error(): self
    {
        return static::getTyped('error');
    }

    public static function success(): self
    {
        return static::getTyped('success');
    }

    public static function info(): self
    {
        return static::getTyped('info');
    }

    private static function getTyped(string $type): self
    {
        if (!isset(static::$types[$type])) {
            static::$types[$type] = new self($type);
        }

        return static::$types[$type];
    }
}
