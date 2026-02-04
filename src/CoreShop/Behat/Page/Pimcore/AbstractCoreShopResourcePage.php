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

namespace CoreShop\Behat\Page\Pimcore;

abstract class AbstractCoreShopResourcePage extends AbstractPimcoreTabPage
{
    public function create(string $name): void
    {
        $addButton = $this->extjsComponentQuery('[itemId=add-button]');
        $addButton->click();

        $newDialog = $this->extsDocumentQuery('[itemId=' . $this->getLayoutId() . '-new-dialog]');
        $newDialog->find('css', 'input')->setValue($name);

        $okButton = $this->extjsComponentQuery('[itemId=ok]', $newDialog->getAttribute('id'));
        $okButton->click();
    }
}
