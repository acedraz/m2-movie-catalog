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

namespace Aislan\MovieCatalog\Block\Movie;

use Aislan\MovieCatalog\Helper\Config;
use Aislan\MovieCatalog\Helper\Data;
use Aislan\MovieCatalog\Helper\System;
use Aislan\MovieCatalog\Model\Layer;
use Aislan\MovieCatalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;

class ListMovie extends AbstractMovie
{

    /**
     * Movie Collection
     *
     * @var AbstractCollection
     */
    protected $_movieCollection;

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * Catalog layer
     *
     * @var Layer
     */
    protected $_catalogLayer;

    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var System
     */
    private $system;

    /**
     * ListMovie constructor.
     * @param Template\Context $context
     * @param array $data
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param Data $helperData
     * @param Layer $layer
     * @param TimezoneInterface $dateTime
     * @param System $system
     */
    public function __construct(
        Template\Context $context,
        array $data = [],
        Resolver $layerResolver,
        Registry $registry,
        Data $helperData,
        Layer $layer,
        TimezoneInterface $dateTime,
        System $system
    ) {
        parent::__construct($context, $data);
        $this->layerResolver = $layerResolver;
        $this->helperData = $helperData;
        $this->_catalogLayer = $layer;
        $this->dateTime = $dateTime;
        $this->system = $system;
    }

    /**
     * @return AbstractCollection
     */
    protected function _getMovieCollection()
    {
        if ($this->_movieCollection === null) {
            $this->_movieCollection = $this->initializeMovieCollection();
        }
        return $this->_movieCollection;
    }

    private function initializeMovieCollection()
    {
        $layer = $this->getLayer();
        /* @var $layer Layer */
        $collection = $layer->getMovieCollection();
        $this->_eventManager->dispatch(
            'catalog_block_movie_list_collection',
            ['collection' => $collection]
        );
        return $collection;
    }

    /**
     * Get catalog layer model
     *
     * @return Layer
     */
    public function getLayer()
    {
        return $this->_catalogLayer;
    }

    /**
     * Add toolbar block from movie listing layout
     *
     * @param Collection $collection
     */
    private function addToolbarBlock(Collection $collection)
    {
        $toolbarLayout = $this->getToolbarFromLayout();

        if ($toolbarLayout) {
            $this->configureToolbar($toolbarLayout, $collection);
        }
    }

    /**
     * Get toolbar block from layout
     *
     * @return bool|Toolbar
     */
    private function getToolbarFromLayout()
    {
        $blockName = $this->getToolbarBlockName();

        $toolbarLayout = false;

        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }

        return $toolbarLayout;
    }

    /**
     * @param $path
     * @return string
     */
    public function getImageUrl($path)
    {
        return $this->helperData->getImageUrl($path);
    }

    /**
     * @return AbstractCollection
     */
    public function getMovies()
    {
        return $this->_getMovieCollection();
    }

    /**
     * @param $date
     * @return string
     */
    public function convertDate($date)
    {
        return $this->dateTime->date($date)->format('dd de MMM de yyyy');
    }

    /**
     * @param $id
     * @return string
     */
    public function getMovieUrl($id)
    {
        return $this->_baseUrl . Config::VIEW_URL . $id;
    }

    public function getMoviesRowQty()
    {
        return $this->system->getMoviesQtyRow();
    }

    public function formatRowsCollection()
    {
        $formated = [];
        $countMovies = 0;
        $countRows = 0;
        foreach ($this->getMovies() as $movie) {
            if ($countMovies < $this->getMoviesRowQty()) {
                $formated[$countRows][] = $movie;
                $countMovies++;
                continue;
            }
            $countMovies = 0;
            $countRows++;
            $formated[$countRows][] = $movie;
        }
        return $formated;
    }
}
