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
use CoreShop\Bundle\ResourceBundle\Form\Type\PimcoreAssetChoiceType;
use CoreShop\Bundle\TestBundle\Service\SharedStorageInterface;
use Pimcore\Model\Asset;
use Symfony\Component\Form\FormFactoryInterface;
use Webmozart\Assert\Assert;

final class PimcoreAssetFormContext implements Context
{
    private mixed $resolvedAsset = null;

    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private FormFactoryInterface $formFactory,
    ) {
    }

    /**
     * @When /^I submit the asset id as string to the asset choice form$/
     */
    public function iSubmitTheAssetIdAsStringToTheAssetChoiceForm(): void
    {
        $form = $this->formFactory->create(PimcoreAssetChoiceType::class);
        $form->submit((string) $this->getAsset()->getId());

        Assert::true($form->isValid(), (string) $form->getErrors(true));

        $this->resolvedAsset = $form->getData();
    }

    /**
     * @Then /^the asset should have been resolved$/
     */
    public function theAssetShouldHaveBeenResolved(): void
    {
        Assert::isInstanceOf($this->resolvedAsset, Asset::class);
        Assert::same($this->resolvedAsset->getId(), $this->getAsset()->getId());
    }

    private function getAsset(): Asset
    {
        $asset = $this->sharedStorage->get('asset');
        Assert::isInstanceOf($asset, Asset::class);

        return $asset;
    }
}
