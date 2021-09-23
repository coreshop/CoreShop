<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;


final class UniqueEntity extends Constraint
{
    public string $message = 'This entity already exists.';
    public array $fields = [];
    public array $values = [];
    public bool $allowSameEntity = false;

    public function validatedBy(): string
    {
        return 'coreshop.unique_entity';
    }

    public function getRequiredOptions(): array
    {
        return ['fields', 'values'];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
