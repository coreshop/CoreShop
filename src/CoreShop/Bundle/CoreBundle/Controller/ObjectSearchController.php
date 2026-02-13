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

namespace CoreShop\Bundle\CoreBundle\Controller;

use Pimcore\Model\DataObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides server-side search for Pimcore DataObjects.
 *
 * Used by the AutocompleteType widget in the StudioFormBundle to search
 * entities without loading all records into memory.
 */
final class ObjectSearchController extends AbstractController
{
    public function searchAction(Request $request): JsonResponse
    {
        $className = $request->query->getString('class', '');
        $query = $request->query->getString('query', '');
        $ids = $request->query->getString('ids', '');
        $limit = $request->query->getInt('limit', 25);

        if ($className === '') {
            return new JsonResponse(
                ['error' => 'Parameter "class" is required.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $fqcn = 'Pimcore\\Model\\DataObject\\' . $className;

        if (!class_exists($fqcn)) {
            return new JsonResponse(
                ['error' => sprintf('DataObject class "%s" not found.', $className)],
                Response::HTTP_NOT_FOUND,
            );
        }

        $listingClass = $fqcn . '\\Listing';

        if (!class_exists($listingClass)) {
            return new JsonResponse(
                ['error' => sprintf('Listing class for "%s" not found.', $className)],
                Response::HTTP_NOT_FOUND,
            );
        }

        /** @var DataObject\Listing $listing */
        $listing = new $listingClass();
        $listing->setUnpublished(false);

        // Mode 1: Resolve existing IDs (for displaying initial values)
        if ($ids !== '') {
            $idArray = array_filter(array_map('intval', explode(',', $ids)));

            if (count($idArray) === 0) {
                return new JsonResponse([]);
            }

            $placeholders = implode(',', array_fill(0, count($idArray), '?'));
            $listing->setCondition('oo_id IN (' . $placeholders . ')', $idArray);
            $listing->setLimit(count($idArray));

            return new JsonResponse($this->formatResults($listing->load()));
        }

        // Mode 2: Search by query
        if ($query !== '') {
            $listing->setCondition('o_key LIKE ?', ['%' . $query . '%']);
        }

        $listing->setLimit(min($limit, 100));
        $listing->setOrderKey('o_key');
        $listing->setOrder('ASC');

        return new JsonResponse($this->formatResults($listing->load()));
    }

    /**
     * @param DataObject\AbstractObject[] $objects
     *
     * @return array<int, array{id: int, label: string}>
     */
    private function formatResults(array $objects): array
    {
        $results = [];

        foreach ($objects as $object) {
            $label = $this->getObjectLabel($object);

            $results[] = [
                'id' => $object->getId(),
                'label' => $label,
            ];
        }

        return $results;
    }

    private function getObjectLabel(DataObject\AbstractObject $object): string
    {
        // Try getName() first (most entities)
        if (method_exists($object, 'getName') && $object->getName()) {
            return (string) $object->getName();
        }

        // For customers: firstname + lastname + email
        if (method_exists($object, 'getFirstname') || method_exists($object, 'getLastname')) {
            $parts = [];

            if (method_exists($object, 'getFirstname') && $object->getFirstname()) {
                $parts[] = $object->getFirstname();
            }

            if (method_exists($object, 'getLastname') && $object->getLastname()) {
                $parts[] = $object->getLastname();
            }

            if (!empty($parts)) {
                $name = implode(' ', $parts);

                if (method_exists($object, 'getEmail') && $object->getEmail()) {
                    return $name . ' (' . $object->getEmail() . ')';
                }

                return $name;
            }

            if (method_exists($object, 'getEmail') && $object->getEmail()) {
                return (string) $object->getEmail();
            }
        }

        // Fallback: use the key
        return $object->getKey() ?: '#' . $object->getId();
    }
}
