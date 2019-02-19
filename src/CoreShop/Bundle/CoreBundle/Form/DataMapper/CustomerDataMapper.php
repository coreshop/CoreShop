<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Form\DataMapper;

use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class CustomerDataMapper implements DataMapperInterface
{
    /**
     * @var DataMapperInterface
     */
    private $propertyPathDataMapper;

    /**
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(PropertyAccessor $propertyAccessor)
    {
        $this->propertyPathDataMapper = new PropertyPathMapper($propertyAccessor);
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms): void
    {
        $formsOtherThanAddress = [];

        foreach ($forms as $key => $form) {
            if ($data instanceof CustomerInterface && $key === 'address') {
                $address = $data->getAddresses();

                if (count($address) > 0) {
                    $form->setData($address[0]);
                }

                continue;
            }

            $formsOtherThanAddress[] = $form;
        }

        if (!empty($formsOtherThanAddress)) {
            $this->propertyPathDataMapper->mapDataToForms($data, $formsOtherThanAddress);
        }
    }

    public function mapFormsToData($forms, &$data)
    {
        $formsOtherThanAddress = [];

        foreach ($forms as $key => $form) {
            if ($key === 'address') {
                $address = $form->getData();

                if (null !== $address) {
                    $data->setAddresses([$form->getData()]);
                }

                continue;
            }

            $formsOtherThanAddress[] = $form;
        }

        if (!empty($formsOtherThanAddress)) {
            $this->propertyPathDataMapper->mapFormsToData($formsOtherThanAddress, $data);
        }
    }
}
