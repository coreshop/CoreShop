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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Resource\Metadata\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Webmozart\Assert\Assert;

final class ResourceContext implements Context
{
    public function __construct(
        private Registry $metadataRegistry,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @Then /^the (class "[^"]+") is registered as Pimcore Resource$/
     */
    public function theClassIsRegisteredAsPimcoreResource(): void
    {
        $car = $this->metadataRegistry->get('app.car');

        Assert::eq($car->getClass('model'), 'Pimcore\Model\DataObject\Car');
    }

    /**
     * @Then /^the "([^"]+)" translation entity should map its locale column with a length of (\d+)$/
     */
    public function theTranslationEntityShouldMapItsLocaleColumnWithALengthOf(
        string $resourceAlias,
        int $expectedLength,
    ): void {
        $translationMetadata = $this->metadataRegistry->get('coreshop.' . $resourceAlias . '_translation');
        $translationClass = $translationMetadata->getClass('model');

        $classMetadata = $this->entityManager->getClassMetadata($translationClass);
        $actualLength = $classMetadata->getFieldMapping('locale')['length'];

        Assert::eq(
            $actualLength,
            $expectedLength,
            sprintf(
                'Expected the "locale" column of "%s" to be mapped with a length of %d, got %d instead.',
                $translationClass,
                $expectedLength,
                $actualLength,
            ),
        );
    }
}
