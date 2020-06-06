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

namespace Aislan\MovieCatalog\Model;

use Aislan\MovieCatalog\Api\Data\MovieGenreSearchResultInterfaceFactory;
use Aislan\MovieCatalog\Api\Data\MovieGenreInterface;
use Aislan\MovieCatalog\Api\MovieGenreRepositoryInterface;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MovieGenreRepository
 */
class MovieGenreRepository extends AbstractMovieGenreRepository implements MovieGenreRepositoryInterface
{
    /**
     * @var GenreFactory
     */
    private $movieGenreFactory;

    /**
     * @var GenreSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var ResourceModel\Genre\CollectionFactory
     */
    private $movieGenreCollectionFactory;

    /**
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * MovieGenreRepository constructor.
     * @param MovieGenreFactory $movieGenreFactory
     * @param MovieGenreSearchResultInterfaceFactory $searchResultFactory
     * @param ResourceModel\MovieGenre\CollectionFactory $movieGenreCollectionFactory
     * @param FilterBuilderFactory $filterBuilderFactory
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        MovieGenreFactory $movieGenreFactory,
        MovieGenreSearchResultInterfaceFactory $searchResultFactory,
        ResourceModel\MovieGenre\CollectionFactory $movieGenreCollectionFactory,
        FilterBuilderFactory $filterBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->movieGenreFactory = $movieGenreFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->movieGenreCollectionFactory = $movieGenreCollectionFactory;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param MovieGenreInterface $movieGenre
     * @return MovieGenreInterface
     */
    public function save(MovieGenreInterface $movieGenre)
    {
        $movieGenre->getResource()->save($movieGenre);
        return $movieGenre;
    }

    /**
     * @param MovieGenreInterface $movieGenre
     * @return void
     */
    public function delete(MovieGenreInterface $movieGenre)
    {
        $movieGenre->getResource()->delete($movieGenre);
    }

    /**
     * @param int $id
     * @return \Aislan\MovieCatalog\Api\Data\GenreInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $obj = $this->movieGenreFactory->create();
        $obj->getResource()->load($obj, $id);
        if (! $obj->getId()) {
            throw new NoSuchEntityException(__('Unable to find movie with ID "%1"', $id));
        }
        return $obj;
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteById($id)
    {
        $obj = $this->getById($id);
        $obj->delete();
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aislan\MovieCatalog\Api\Data\MovieApiSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->movieGenreCollectionFactory->create();
        $searchResults = $this->searchResultFactory->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();
        return $this->buildSearchResult($searchCriteria, $collection, $searchResults);
    }

    /**
     * @param $movieApiId
     * @return \Aislan\MovieCatalog\Api\Data\MovieApiSearchResultInterface|mixed
     */
    public function getMovieGenreByMovieApiId($movieApiId)
    {
        $filters[] = $this->filterBuilderFactory->create()->setField('movie_api_id')
            ->setValue($movieApiId)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()->addFilters($filters)->create();
        return $this->getList($searchCriteria);
    }

    /**
     * @param $movieApiId
     * @return \Aislan\MovieCatalog\Api\Data\MovieApiSearchResultInterface|mixed
     */
    public function getMovieGenreWithNameByMovieApiId($movieApiId)
    {
        $collection = $this->movieGenreCollectionFactory->create();
        $searchResults = $this->searchResultFactory->create();
        $collection->getSelect()->join('catalog_movie_genre as genre','main_table.genre_api_id = genre.api_id');
        $filters[] = $this->filterBuilderFactory->create()->setField('movie_api_id')
            ->setValue($movieApiId)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()->addFilters($filters)->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();
        return $this->buildSearchResult($searchCriteria, $collection, $searchResults);
    }
}
