<?php

namespace CoreShop\Plugin;

interface InstallPlugin
{
    public function install(\CoreShop\Plugin\Install $installer);
    public function uninstall(\CoreShop\Plugin\Install $installer);
}