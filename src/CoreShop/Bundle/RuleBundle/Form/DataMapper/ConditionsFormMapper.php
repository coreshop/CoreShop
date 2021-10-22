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

namespace CoreShop\Bundle\RuleBundle\Form\DataMapper;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataMapperInterface;

/**
 * @internal
 */
class ConditionsFormMapper implements DataMapperInterface
{
    public function __construct(private DataMapperInterface $propertyMapper)
    {
    }

    public function mapDataToForms($viewData, $forms): void
    {
//        $this->propertyPathDataMapper->mapDataToForms($data, $forms);
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

            if (!$formData instanceof ConditionInterface) {
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
