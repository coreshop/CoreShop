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

namespace CoreShop\Bundle\OrderBundle\Controller;

use Pimcore\Model\DataObject;

class QuoteController extends AbstractSaleDetailController
{
    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getFolderConfigurationAction()
    {
        $this->isGrantedOr403();

        $name = null;
        $folderId = null;

        $orderClassId = $this->getParameter('coreshop.model.quote.pimcore_class_name');
        $folderPath = $this->getParameter('coreshop.folder.quote');
        $orderClassDefinition = DataObject\ClassDefinition::getByName($orderClassId);

        $folder = DataObject::getByPath('/' . $folderPath);

        if ($folder instanceof DataObject\Folder) {
            $folderId = $folder->getId();
        }

        if ($orderClassDefinition instanceof DataObject\ClassDefinition) {
            $name = $orderClassDefinition->getName();
        }

        return $this->viewHandler->handle(['success' => true, 'className' => $name, 'folderId' => $folderId]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleRepository()
    {
        return $this->get('coreshop.repository.quote');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSalesList()
    {
        return $this->getSaleRepository()->getList();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleClassName()
    {
        return 'coreshop.model.quote.pimcore_class_name';
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrderKey()
    {
        return 'quoteDate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleNumberField()
    {
        return 'quoteNumber';
    }
}
