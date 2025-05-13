<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

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
