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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\TestBundle\Service\SharedStorageInterface;
use Pimcore\Model\Document\Email;

final class EmailDocumentContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Given /^the site has an email document "([^"]+)"$/
     */
    public function theSiteHasAnEmailDocument(string $key): void
    {
        $emailDocument = new Email();
        $emailDocument->setKey($key);
        $emailDocument->setParentId(1);
        $emailDocument->setPublished(true);
        $emailDocument->save();

        $this->sharedStorage->set('email_document', $emailDocument);
    }
}
