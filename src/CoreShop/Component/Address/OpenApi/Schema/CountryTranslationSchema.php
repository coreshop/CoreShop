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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Address\OpenApi\Schema;

use CoreShop\Component\Resource\OpenApi\Schema\ResourceTranslationSchema;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;


#[Schema(
    title: 'CoreShop Country Translation',
    description: 'a Country',
    required: ['id', 'name'],
    type: 'object',
)]
class CountryTranslationSchema extends ResourceTranslationSchema
{
    public function __construct(
        int $id,
        readonly string $locale,
        #[Property(description: 'Name of the Country Translation', type: 'string', example: 'role')]
        private readonly string $name,
    ) {
        parent::__construct($id, $locale);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
