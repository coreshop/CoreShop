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

namespace CoreShop\Bundle\CoreBundle\Form\DataMapper;

use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;

class CustomerDataMapper implements DataMapperInterface
{
    private DataMapper $propertyPathDataMapper;

    public function __construct()
    {
        $this->propertyPathDataMapper = new DataMapper();
    }

    public function mapDataToForms($viewData, $forms): void
    {
        $formsOtherThanAddress = [];

        foreach ($forms as $key => $form) {
            if ($viewData instanceof CustomerInterface && 'address' === $key) {
                $address = $viewData->getAddresses();

                if (count($address) > 0) {
                    $form->setData($address[0]);
                }

                continue;
            }

            $formsOtherThanAddress[] = $form;
        }

        if (!empty($formsOtherThanAddress)) {
            $this->propertyPathDataMapper->mapDataToForms($viewData, $formsOtherThanAddress);
        }
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        $formsOtherThanAddress = [];

        foreach ($forms as $key => $form) {
            if ('address' === $key) {
                $address = $form->getData();

                if (null !== $address) {
                    $viewData->setAddresses([$form->getData()]);
                }

                continue;
            }

            $formsOtherThanAddress[] = $form;
        }

        if (!empty($formsOtherThanAddress)) {
            $this->propertyPathDataMapper->mapFormsToData($formsOtherThanAddress, $viewData);
        }
    }
}
