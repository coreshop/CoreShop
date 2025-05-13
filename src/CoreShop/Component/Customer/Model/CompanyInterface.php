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

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

interface CompanyInterface extends ResourceInterface, PimcoreModelInterface, EquatableInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name);

    public function getVatIdentificationNumber(): ?string;

    public function setVatIdentificationNumber(?string $vatIdentificationNumber);
}
