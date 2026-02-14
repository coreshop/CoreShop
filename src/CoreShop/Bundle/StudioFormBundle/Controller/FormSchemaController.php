<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\StudioFormBundle\Controller;

use CoreShop\Bundle\StudioFormBundle\Form\Schema\BlockPrefixFormTypeRegistry;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FormSchemaController
{
    public function __construct(
        private readonly FormSchemaGenerator $generator,
        private readonly BlockPrefixFormTypeRegistry $blockPrefixRegistry,
    ) {
    }

    public function schemaAction(Request $request, string $blockPrefix): JsonResponse
    {
        $formTypeClass = $this->blockPrefixRegistry->resolve($blockPrefix);

        if ($formTypeClass === null) {
            return new JsonResponse(
                ['error' => sprintf('No form type registered for block prefix "%s".', $blockPrefix)],
                Response::HTTP_NOT_FOUND,
            );
        }

            $schema = $this->generator->generate($formTypeClass);

        return new JsonResponse($schema);
    }
}
