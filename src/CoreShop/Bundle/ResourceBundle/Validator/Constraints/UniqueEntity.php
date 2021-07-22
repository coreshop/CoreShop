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

namespace CoreShop\Bundle\ResourceBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class UniqueEntity extends Constraint
{
    public $message = 'This entity already exists.';

    public $fields = [];

    public $values = [];

    public $allowSameEntity = false;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'coreshop.unique_entity';
    }

    /**
     * @return array
     */
    public function getRequiredOptions()
    {
        return ['fields', 'values'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
