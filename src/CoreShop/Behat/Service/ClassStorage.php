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

class ClassStorage implements ClassStorageInterface
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * {@inheritdoc}
     */
    public function get($className)
    {
        if (!isset($this->storage[$className])) {
            throw new \InvalidArgumentException(sprintf('There is no class name for "%s"!', $className));
        }

        return $this->storage[$className];
    }

    /**
     * {@inheritdoc}
     */
    public function has($className)
    {
        return isset($this->storage[$className]);
    }

    /**
     * {@inheritdoc}
     */
    public function set($className)
    {
        $this->storage[$className] = $this->getBehatClassName($className);

        return $this->storage[$className];
    }

    /**
     * {@inheritdoc}
     */
    private function getBehatClassName($className)
    {
        return sprintf('Behat%s%s', $className, uniqid());
    }
}
