<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\Doctrine\Cache;

use Doctrine\Common\Cache\CacheProvider;
use Pimcore\Cache;

class PimcoreCache extends CacheProvider
{
    /**
     * @var Cache\Core\CoreHandlerInterface
     */
    private $coreHandler;

    /**
     * @param Cache\Core\CoreHandlerInterface $coreHandler
     */
    public function __construct(Cache\Core\CoreHandlerInterface $coreHandler)
    {
        $this->coreHandler = $coreHandler;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return $this->coreHandler->load($this->getCacheKey($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return null !== $this->coreHandler->load($this->getCacheKey($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $this->coreHandler->save($this->getCacheKey($id), $data, ['doctrine_pimcore_cache'], $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        $this->coreHandler->remove($this->getCacheKey($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        $this->coreHandler->clearTag('doctrine_pimcore_cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return [];
    }

    private function getCacheKey($id)
    {
        return md5($id);
    }
}
