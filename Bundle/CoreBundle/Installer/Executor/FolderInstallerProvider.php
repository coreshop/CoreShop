<?php

namespace CoreShop\Bundle\CoreBundle\Installer\Executor;

use Pimcore\Model\Object;

final class FolderInstallerProvider
{
    /**
     * @var string
     */
    private $cartFolder;

    /**
     * @var string
     */
    private $productFolder;

    /**
     * @var string
     */
    private $customerFolder;

    /**
     * @var string
     */
    private $customerGroupFolder;

    /**
     * @var string
     */
    private $orderFolder;

    /**
     * @param string $cartFolder
     * @param string $productFolder
     * @param string $customerFolder
     * @param string $customerGroupFolder
     * @param string $orderFolder
     */
    public function __construct($cartFolder, $productFolder, $customerFolder, $customerGroupFolder, $orderFolder)
    {
        $this->cartFolder = $cartFolder;
        $this->productFolder = $productFolder;
        $this->customerFolder = $customerFolder;
        $this->customerGroupFolder = $customerGroupFolder;
        $this->orderFolder = $orderFolder;
    }

    /**
     * Installs all CoreShop needed Folders
     */
    public function installFolders() {
        $folders = [
            $this->cartFolder,
            $this->productFolder,
            $this->customerFolder,
            $this->customerGroupFolder,
            $this->orderFolder
        ];

        foreach ($folders as $folder) {
            Object\Service::createFolderByPath($folder);
        }
    }
}