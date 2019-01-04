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

class RelationalValue implements RelationalValueInterface
{
    /**
     * @var int
     */
    protected $destinationId;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param int    $destinationId
     * @param string $type
     */
    public function __construct(int $destinationId, string $type)
    {
        $this->destinationId = $destinationId;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestinationId()
    {
        return $this->destinationId;
    }

    /**
     * @param int $destinationId
     */
    public function setDestinationId($destinationId)
    {
        $this->destinationId = $destinationId;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }
}
