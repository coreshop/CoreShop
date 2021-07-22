<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Webmozart\Assert\Assert;

final class IteratorInterpreter implements InterpreterInterface
{
    private ServiceRegistryInterface $interpreterRegistry;

    public function __construct(ServiceRegistryInterface $interpreterRegistry)
    {
        $this->interpreterRegistry = $interpreterRegistry;
    }

    public function interpret(
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = []
    ): mixed
    {
        Assert::isArray($value, 'IteratorInterpreter can only be used with array values');

        $interpreter = $interpreterConfig['interpreter'];
        /**
         * @var InterpreterInterface $interpreterObject
         */
        $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);

        foreach ($value as &$val) {
            $val = $interpreterObject->interpret($val, $indexable, $config, $interpreter['interpreterConfig']);
        }

        return $value;
    }
}
