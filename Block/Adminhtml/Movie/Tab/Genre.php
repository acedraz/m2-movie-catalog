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

namespace Aislan\MovieCatalog\Block\Adminhtml\Movie\Tab;

use Aislan\MovieCatalog\Api\MovieEntityRepositoryInterface;
use Aislan\MovieCatalog\Api\MovieGenreRepositoryInterface;
use Aislan\MovieCatalog\Model\ResourceModel\Genre\Collection;
use Aislan\MovieCatalog\Model\ResourceModel\MovieGenre\CollectionFactory;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\App\ObjectManager;
use Aislan\MovieCatalog\Api\GenreRepositoryInterface;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Backend\Block\Template\Context;
use Aislan\MovieCatalog\Block\Adminhtml\Movie\Tab\Render\Image;

class Genre extends Extended
{
    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var ComponentFactory
     */
    protected $genreRepository;

    /**
     * @var Yesno
     */
    private $yesno;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilderFactory
     */
    private $filterBuilder;

    /**
     * @var MovieEntityRepositoryInterface
     */
    private $movieEntityRepository;

    /**
     * @var MovieGenreRepositoryInterface
     */
    private $movieGenreRepository;
    /**
     * @var Collection
     */
    private $genreCollection;
    /**
     * @var CollectionFactory
     */
    private $movieGenreCollectionFactory;

    /**
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param GenreRepositoryInterface $genreRepository
     * @param Registry $coreRegistry
     * @param array $data
     * @param Yesno|null $yesno
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilder
     * @param FilterBuilderFactory $filterBuilder
     * @param MovieEntityRepositoryInterface $movieEntityRepository
     * @param MovieGenreRepositoryInterface $movieGenreRepository
     * @param Collection $genreCollection
     * @param CollectionFactory $movieGenreCollectionFactory
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        GenreRepositoryInterface $genreRepository,
        Registry $coreRegistry,
        array $data = [],
        Yesno $yesno = null,
        SearchCriteriaBuilderFactory $searchCriteriaBuilder,
        FilterBuilderFactory $filterBuilder,
        MovieEntityRepositoryInterface $movieEntityRepository,
        MovieGenreRepositoryInterface $movieGenreRepository,
        Collection $genreCollection,
        CollectionFactory $movieGenreCollectionFactory
    ) {
        $this->genreRepository = $genreRepository;
        $this->_coreRegistry = $coreRegistry;
        $this->yesno = $yesno ?: ObjectManager::getInstance()->get(Yesno::class);
        parent::__construct($context, $backendHelper, $data);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->movieEntityRepository = $movieEntityRepository;
        $this->movieGenreRepository = $movieGenreRepository;
        $this->genreCollection = $genreCollection;
        $this->movieGenreCollectionFactory = $movieGenreCollectionFactory;
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('avanti_mosaicmanager_movie_genres');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * @return array|null
     */
    public function getMovie()
    {
        return $this->_coreRegistry->registry('aislan_moviecatalog_movie_entity');
    }

    /**
     * @param Column $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'entity_id') {
            $genresId = $this->_getSelectedGenres();
            if (empty($genresId)) {
                $genresId = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.id', ['in' => $genresId]);
            } elseif (!empty($genresId)) {
                $this->getCollection()->addFieldToFilter('main_table.api_id', ['nin' => $genresId]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        parent::_addColumnFilterToCollection($column);
        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->getGenreMovieEntityCollection());
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'api_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'api_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('aislan_moviecatalog/movie/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedGenres()
    {
        $genres = $this->getRequest()->getPost('selected_genres');
        if ($genres === null) {
            $movieEntity = $this->getMovie();
            $genres = $this->movieGenreRepository->getMovieGenreWithNameByMovieApiId($movieEntity->getApiId());
            return array_keys($genres);
        }
    }

    /**
     * @return \Aislan\MovieCatalog\Model\ResourceModel\MovieGenre\Collection
     */
    public function getGenreMovieEntityCollection()
    {
        $collection = $this->movieGenreCollectionFactory->create();
        $collection->getSelect()->join('catalog_movie_genre as genre','main_table.genre_api_id = genre.api_id');
        $filters[] = $this->filterBuilder->create()->setField('movie_api_id')
            ->setValue($this->getMovie()->getApiId())
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder->create()->addFilters($filters)->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        return $collection->load();
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param \Aislan\MovieCatalog\Model\ResourceModel\MovieGenre\Collection $collection
     */
    public function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param $collection
     */
    public function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param $collection
     */
    public function addPagingToCollection(SearchCriteriaInterface $searchCriteria, $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }
}
