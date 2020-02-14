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

namespace CoreShop\Component\Sequence\Factory;

use CoreShop\Component\Resource\Exception\UnsupportedMethodException;
use CoreShop\Component\Sequence\Model\SequenceInterface;

class SequenceFactory implements SequenceFactoryInterface
{
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnsupportedMethodException
     */
    public function createNew()
    {
        throw new UnsupportedMethodException('createNew');
    }

    /**
     * {@inheritdoc}
     */
    public function createWithType(string $type): SequenceInterface
    {
        $sequence = new $this->className();
        $sequence->setType($type);

        return $sequence;
    }
}
