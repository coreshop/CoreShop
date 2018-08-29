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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionInterpreter implements InterpreterInterface
{
    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ExpressionLanguage $expressionLanguage
     * @param ContainerInterface $container
     */
    public function __construct(ExpressionLanguage $expressionLanguage, ContainerInterface $container)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function interpret($value, IndexableInterface $indexable, IndexColumnInterface $config, $interpreterConfig = [])
    {
        $expression = $interpreterConfig['expression'];

        return $this->expressionLanguage->evaluate($expression, [
            'value' => $value,
            'object' => $indexable,
            'config' => $config,
            'container' => $this->container,
        ]);
    }
}
