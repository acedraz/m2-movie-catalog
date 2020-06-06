<?php
/**
 * Aislan
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to aislan.cedraz@gmail.com.br for more information.
 *
 * @module      Aislan Movie Catalog
 * @category    Aislan
 * @package     Aislan_MovieCatalog
 *
 * @copyright   Copyright (c) 2020 Aislan.
 *
 * @author      Aislan Core Team <aislan.cedraz@gmail.com.br>
 */

declare(strict_types=1);

namespace Aislan\MovieCatalog\Ui\DataProvider\Movie;

use Aislan\MovieCatalog\Model\ResourceModel\MovieEntity\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $dataPersistor;
    protected $loadedData;

    /**
     * Constructor.
     *
     * @param string                 $name
     * @param string                 $primaryFieldName
     * @param string                 $requestFieldName
     * @param CollectionFactory      $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array                  $meta
     * @param array                  $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get('aislan_moviecatalog_movie');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('aislan_moviecatalog_movie');
        }

        return $this->loadedData;
    }
}
