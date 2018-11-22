<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Webmozart\Assert\Assert;

trait NestedTrait
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $interpreterRegistry;

    /**
     * @param mixed $value
     * @param array $interpreterConfig
     * @param callable $callback
     * @return mixed
     */
    protected function loop($value, $interpreterConfig, callable $callback)
    {
        foreach ($interpreterConfig['interpreters'] as $interpreter) {
            $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);

            $value = $callback($value, $interpreterObject, $interpreter['interpreterConfig']);
        }

        return $value;
    }

    /**
     * @param array $interpreterConfig
     */
    protected function assert($interpreterConfig)
    {
        Assert::keyExists($interpreterConfig, 'interpreters');
        Assert::isArray($interpreterConfig['interpreters'], 'Interpreter Config needs to be array');
    }
}
