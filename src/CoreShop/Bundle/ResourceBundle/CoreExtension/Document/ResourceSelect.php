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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension\Document;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\Document\Tag;

class ResourceSelect extends Tag
{
    /**
     * @var ResourceInterface|null
     */
    public $resource;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $nameProperty;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param RepositoryInterface $repository
     * @param string $nameProperty
     * @param string $type
     * @param array $params
     */
    public function __construct(RepositoryInterface $repository, string $nameProperty, string $type, array $params = [])
    {
        $this->repository = $repository;
        $this->nameProperty = $nameProperty;
        $this->type = $type;
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function frontend()
    {
        return '';
    }

    public function getData()
    {
        return $this->resource;
    }

    /**
     * @return null|ResourceInterface
     */
    public function getResourceObject()
    {
        if ($this->resource) {
            return $this->repository->find($this->resource);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $data = $this->repository->findAll();
        $result = [];

        foreach ($data as $resource) {
            if (!$resource instanceof ResourceInterface) {
                throw new \InvalidArgumentException('Only ResourceInterface is allowed');
            }

            $result[] = [
                $resource->getId(),
                $this->getResourceName($resource),
            ];
        }

        return [
            'store' => $result,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setDataFromEditmode($data)
    {
        $this->resource = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataFromResource($data)
    {
        $this->resource = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getForWebserviceExport($document = null, $params = [])
    {
        return [
            'id' => $this->resource->getId(),
        ];
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return mixed
     */
    protected function getResourceName(ResourceInterface $resource)
    {
        $getter = 'get' . ucfirst($this->nameProperty);

        if (!method_exists($resource, $getter)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Property with Name %s does not exist in resource %s',
                    $this->nameProperty,
                    get_class($resource)
                )
            );
        }

        return $resource->$getter();
    }
}
