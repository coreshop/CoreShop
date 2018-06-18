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

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Webmozart\Assert\Assert;

class NestedInterpreter implements InterpreterInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $interpreterRegistry;

    /**
     * @param ServiceRegistryInterface $interpreterRegistry
     */
    public function __construct(ServiceRegistryInterface $interpreterRegistry)
    {
        $this->interpreterRegistry = $interpreterRegistry;
    }


    public function interpret($value, IndexableInterface $object, IndexColumnInterface $config, $interpreterConfig = [])
    {
        Assert::keyExists($interpreterConfig, 'interpreters');
        Assert::isArray($interpreterConfig['interpreters'], 'Interpreter Config needs to be array');

        foreach ($interpreterConfig['interpreters'] as $interpreter) {
            $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);

            if ($interpreterObject instanceof InterpreterInterface) {
                $value = $interpreterObject->interpret($object, $value, $config, $interpreter['interpreterConfig']);
            }
        }

        return $value;
    }
}
