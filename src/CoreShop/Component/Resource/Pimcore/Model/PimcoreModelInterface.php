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

namespace CoreShop\Component\Resource\Pimcore\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\Element\ElementInterface;

interface PimcoreModelInterface extends ResourceInterface, ElementInterface
{
    public function getId(): ?int;

    public function setKey(string $key): static;

    public function getKey(): ?string;

    public function setPublished(bool $published): static;

    public function getPublished(): bool;

    public function isPublished(): bool;

    public function setParent(?ElementInterface $parent): static;

    public function getParent(): ?ElementInterface;

    public function getObjectVar(?string $var): mixed;

    public function save(array $parameters = []): static;

    public function delete(): void;

    public function getChildren(array $type = [], bool $includingUnpublished = false): Listing;

    public function getClass(): ClassDefinition;
}
