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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension\Document;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\Document\Tag;

class Select extends Tag
{
    /**
     * @var int
     */
    public $resource;

    /**
     * @var string
     */
    protected $repositoryName;

    /**
     * @var string
     */
    protected $nameProperty;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $repositoryName
     * @param string $nameProperty
     * @param string $type
     */
    public function __construct(string $repositoryName, string $nameProperty, string $type)
    {
        $this->repositoryName = $repositoryName;
        $this->nameProperty = $nameProperty;
        $this->type = $type;
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
            return $this->getRepository()->find($this->resource);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $data = $this->getRepository()->findAll();
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
     * @return mixed
     */
    protected function getResourceName(ResourceInterface $resource)
    {
        $getter = 'get'.ucfirst($this->nameProperty);

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

    /**
     * @return RepositoryInterface
     */
    private function getRepository()
    {
        return \Pimcore::getContainer()->get($this->repositoryName);
    }
}