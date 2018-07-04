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

namespace CoreShop\Component\Resource\Pimcore;

use CoreShop\Component\Pimcore\Document\DocumentTagFactoryInterface;

class ResourceDocumentTagFactory implements DocumentTagFactoryInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $repositoryName;

    /**
     * @var string
     */
    private $nameProperty;

    /**
     * @param string $class
     * @param string $repositoryName
     * @param string $nameProperty
     */
    public function __construct(string $class, string $repositoryName, string $nameProperty)
    {
        $this->class = $class;
        $this->repositoryName = $repositoryName;
        $this->nameProperty = $nameProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $params)
    {
        array_unshift($params, $type);
        array_unshift($params, $this->nameProperty);
        array_unshift($params, $this->repositoryName);

        $className = $this->class;
        $instance = new $className(...$params);

        return $instance;
    }
}