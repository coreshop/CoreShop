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

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CustomerGroupInterface extends PimcoreModelInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name);

    public function getRoles(): ?array;

    public function setRoles(?array $roles);
}
