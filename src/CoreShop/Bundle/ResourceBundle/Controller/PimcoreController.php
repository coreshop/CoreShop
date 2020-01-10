<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class PimcoreController extends AdminController
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var PimcoreRepositoryInterface
     */
    protected $repository;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param MetadataInterface          $metadata
     * @param PimcoreRepositoryInterface $repository
     * @param FactoryInterface           $factory
     * @param ViewHandlerInterface       $viewHandler
     */
    public function __construct(
        MetadataInterface $metadata,
        PimcoreRepositoryInterface $repository,
        FactoryInterface $factory,
        ViewHandlerInterface $viewHandler
    ) {
        parent::__construct($viewHandler);

        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @throws AccessDeniedException
     */
    protected function isGrantedOr403()
    {
        if ($this->getPermission()) {
            $user = method_exists($this, 'getAdminUser') ? $this->getAdminUser() : $this->getUser();

            if ($user->isAllowed($this->getPermission())) {
                return;
            }

            throw new AccessDeniedException();
        }
    }

    /**
     * @return string
     */
    protected function getPermission()
    {
        return '';
    }
}
