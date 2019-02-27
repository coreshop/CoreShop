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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractResource;

class ProductUnit extends AbstractResource implements ProductUnitInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var integer
     */
    protected $defaultPrecision;

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultPrecision()
    {
        return $this->defaultPrecision;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultPrecision(int $defaultPrecision)
    {
        $this->defaultPrecision = $defaultPrecision;
    }
}
