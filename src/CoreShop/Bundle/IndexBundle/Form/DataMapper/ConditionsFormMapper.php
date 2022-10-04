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

namespace CoreShop\Bundle\IndexBundle\Form\DataMapper;

use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataMapperInterface;

/**
 * @internal
 */
class ConditionsFormMapper implements DataMapperInterface
{
    public function __construct(
        private DataMapperInterface $propertyMapper,
    ) {
    }

    public function mapDataToForms($viewData, $forms): void
    {
//        $this->propertyPathDataMapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        if (!$viewData instanceof Collection) {
            return;
        }

        $actualData = [];

        foreach ($forms as $form) {
            $formData = $form->getData();
            $id = $form->get('id')->getData();

            if (!$formData instanceof FilterConditionInterface) {
                continue;
            }

            if ($id) {
                foreach ($viewData as $entry) {
                    if (!$entry instanceof ResourceInterface) {
                        continue;
                    }

                    if ($entry->getId() === $id) {
                        $this->propertyMapper->mapFormsToData($form, $entry);

                        $actualData[] = $entry;

                        continue 2;
                    }
                }
            }

            $actualData[] = $formData;
        }

        $viewData = $actualData;
    }
}
