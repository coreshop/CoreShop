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

namespace CoreShop\Bundle\RuleBundle\Form\DataMapper;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataMapperInterface;

/**
 * @internal
 */
class ActionsFormMapper implements DataMapperInterface
{
    public function __construct(
        private DataMapperInterface $propertyMapper,
    ) {
    }

    public function mapDataToForms($viewData, $forms): void
    {
//        $this->propertyPathDataMapper->mapDataToForms($data, $forms);
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        if (!$viewData instanceof Collection && !is_array($viewData)) {
            return;
        }

        $actualData = [];

        foreach ($forms as $form) {
            $formData = $form->getData();
            $id = $form->get('id')->getData();

            if (!$formData instanceof ActionInterface) {
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
