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

namespace CoreShop\Bundle\CoreBundle\NumberGenerator;

use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Sequence\Generator\SequenceGeneratorInterface;

class SequenceNumberGenerator implements NumberGeneratorInterface
{
    /**
     * @var SequenceGeneratorInterface
     */
    protected $sequenceNumberGenerator;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param SequenceGeneratorInterface $sequenceNumberGenerator
     * @param string                     $type
     */
    public function __construct(SequenceGeneratorInterface $sequenceNumberGenerator, $type)
    {
        $this->sequenceNumberGenerator = $sequenceNumberGenerator;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ResourceInterface $model)
    {
        return $this->sequenceNumberGenerator->getNextSequenceForType($this->type);
    }
}
