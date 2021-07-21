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

declare(strict_types=1);

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
    private DataMapperInterface $propertyMapper;

    public function __construct(DataMapperInterface $propertyMapper)
    {
        $this->propertyMapper = $propertyMapper;
    }

    public function mapDataToForms($data, $forms): void
    {
//        $this->propertyPathDataMapper->mapDataToForms($data, $forms);
    }

    public function mapFormsToData($forms, &$data): void
    {
        if (!$data instanceof Collection) {
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
                foreach ($data as $entry) {
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

        $data = $actualData;
    }
}
