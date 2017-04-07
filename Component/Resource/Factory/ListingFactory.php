<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Factory;

/**
 * Class Factory.
 */
final class ListingFactory implements FactoryInterface
{
    /**
     * @var string
     */
    private $listClassName;

    /**
     * @var string
     */
    private $classModel;

    /**
     * @param string $listClassName
     * @param string $classModel
     */
    public function __construct($listClassName, $classModel)
    {
        $this->listClassName = $listClassName;
        $this->classModel = $classModel;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new $this->listClassName($this->classModel);
    }
}
