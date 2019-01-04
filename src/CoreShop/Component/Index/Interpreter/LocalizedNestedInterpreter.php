<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

class LocalizedNestedInterpreter implements LocalizedInterpreterInterface
{
    use NestedTrait;

    /**
     * @param ServiceRegistryInterface $interpreterRegistry
     */
    public function __construct(ServiceRegistryInterface $interpreterRegistry)
    {
        $this->interpreterRegistry = $interpreterRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function interpret($value, IndexableInterface $indexable, IndexColumnInterface $config, $interpreterConfig = [])
    {
        throw new \Exception('method "interpret" in Localized Interpreter not allowed. Please use "interpretForLanguage" instead.');
    }

    /**
     * {@inheritdoc}
     */
    public function interpretForLanguage($language, $value, IndexableInterface $indexable, IndexColumnInterface $config, $interpreterConfig = [])
    {
        $this->assert($interpreterConfig);

        $value = $this->loop($value, $interpreterConfig, function ($value, InterpreterInterface $interpreter, $interpreterConfig) use ($language, $indexable, $config) {
            if ($interpreter instanceof LocalizedInterpreterInterface) {
                return $interpreter->interpretForLanguage($language, $value, $indexable, $config, $interpreterConfig);
            }

            return $value;
        });

        return $value;
    }
}
