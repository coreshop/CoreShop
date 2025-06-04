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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class ResourceFormFactory implements ResourceFormFactoryInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function create(MetadataInterface $metadata, ResourceInterface $resource): FormInterface
    {
        $formType = $metadata->getClass('form');

        return $this->formFactory->createNamed('', $formType, $resource);
    }
}
