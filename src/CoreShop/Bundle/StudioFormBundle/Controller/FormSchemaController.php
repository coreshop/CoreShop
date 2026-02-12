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

use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaAliasRegistry;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FormSchemaController extends AbstractController
{
    public function __construct(
        private readonly FormSchemaGenerator $generator,
        private readonly FormSchemaAliasRegistry $aliasRegistry,
    ) {
    }

    public function schemaAction(Request $request, string $alias): JsonResponse
    {
        $formTypeClass = $this->aliasRegistry->resolve($alias);

        if (!class_exists($formTypeClass)) {
            return new JsonResponse(
                ['error' => sprintf('Form type "%s" not found.', $alias)],
                Response::HTTP_NOT_FOUND,
            );
        }

        try {
            $schema = $this->generator->generate($formTypeClass);
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['error' => sprintf('Failed to generate schema: %s', $e->getMessage())],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return new JsonResponse($schema);
    }
}
