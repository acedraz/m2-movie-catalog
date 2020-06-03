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

use Aislan\MovieCatalog\Api\Data\GenreSearchResultInterfaceFactory;
use Aislan\MovieCatalog\Api\GenreRepositoryInterface;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenreRepository
 */
class GenreRepository extends AbstractGenreRepository implements GenreRepositoryInterface
{
    /**
     * @var GenreFactory
     */
    private $genreFactory;

    /**
     * @var GenreSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var ResourceModel\Genre\CollectionFactory
     */
    private $genreCollectionFactory;

    /**
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    public function __construct(
        GenreFactory $genreFactory,
        GenreSearchResultInterfaceFactory $searchResultFactory,
        ResourceModel\Genre\CollectionFactory $genreCollectionFactory,
        FilterBuilderFactory $filterBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->genreFactory = $genreFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->genreCollectionFactory = $genreCollectionFactory;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param \Aislan\MovieCatalog\Api\Data\GenreInterface $genre
     * @return \Aislan\MovieCatalog\Api\Data\GenreInterface
     */
    public function save(\Aislan\MovieCatalog\Api\Data\GenreInterface $genre)
    {
        $genre->getResource()->save($genre);
        return $genre;
    }

    /**
     * @param \Aislan\MovieCatalog\Api\Data\GenreInterface $genre
     * @return \Aislan\MovieCatalog\Api\Data\GenreInterface
     */
    public function delete(\Aislan\MovieCatalog\Api\Data\GenreInterface $genre)
    {
        $genre->getResource()->delete($genre);
    }

    /**
     * @param int $id
     * @return \Aislan\MovieCatalog\Api\Data\GenreInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $obj = $this->genreFactory->create();
        $obj->getResource()->load($obj, $id);
        if (! $obj->getId()) {
            throw new NoSuchEntityException(__('Unable to find Genre with ID "%1"', $id));
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
     * @return \Aislan\MovieCatalog\Api\Data\MyEntitySearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->genreCollectionFactory->create();
        $searchResults = $this->searchResultFactory->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();
        return $this->buildSearchResult($searchCriteria, $collection, $searchResults);
    }

    /**
     * @param $apiId
     * @return \Aislan\MovieCatalog\Api\Data\GenreInterface
     */
    public function getGenreByApiId($apiId)
    {
        $filters[] = $this->filterBuilderFactory->create()->setField('api_id')
            ->setValue($apiId)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()->addFilters($filters)->create();
        return $this->getList($searchCriteria);
    }
}
