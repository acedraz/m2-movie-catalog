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

use Aislan\MovieCatalog\Api\Data\MovieEntitySearchResultInterfaceFactory;
use Aislan\MovieCatalog\Api\Data\MovieEntityInterface;
use Aislan\MovieCatalog\Api\MovieEntityRepositoryInterface;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MovieEntityRepository
 */
class MovieEntityRepository extends AbstractMovieEntityRepository implements MovieEntityRepositoryInterface
{
    /**
     * @var EntityFactory
     */
    private $movieEntityFactory;

    /**
     * @var EntitySearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var ResourceModel\Entity\CollectionFactory
     */
    private $movieEntityCollectionFactory;

    /**
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * MovieEntityRepository constructor.
     * @param MovieEntityFactory $movieEntityFactory
     * @param MovieEntitySearchResultInterfaceFactory $searchResultFactory
     * @param ResourceModel\MovieEntity\CollectionFactory $movieEntityCollectionFactory
     * @param FilterBuilderFactory $filterBuilderFactory
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        MovieEntityFactory $movieEntityFactory,
        MovieEntitySearchResultInterfaceFactory $searchResultFactory,
        ResourceModel\MovieEntity\CollectionFactory $movieEntityCollectionFactory,
        FilterBuilderFactory $filterBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->movieEntityFactory = $movieEntityFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->movieEntityCollectionFactory = $movieEntityCollectionFactory;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param MovieEntityInterface $movieEntity
     * @return MovieEntityInterface
     */
    public function save(MovieEntityInterface $movieEntity)
    {
        $movieEntity->getResource()->save($movieEntity);
        return $movieEntity;
    }

    /**
     * @param MovieEntityInterface $movieEntity
     * @return void
     */
    public function delete(MovieEntityInterface $movieEntity)
    {
        $movieEntity->getResource()->delete($movieEntity);
    }

    /**
     * @param int $id
     * @return \Aislan\MovieCatalog\Api\Data\EntityInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $obj = $this->movieEntityFactory->create();
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
        $collection = $this->movieEntityCollectionFactory->create();
        $searchResults = $this->searchResultFactory->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();
        return $this->buildSearchResult($searchCriteria, $collection, $searchResults);
    }


    /**
     * @param $movieEntityApiId
     * @return \Aislan\MovieCatalog\Api\Data\MovieEntitySearchResultInterface|mixed
     */
    public function getMovieEntityByApiId($movieEntityApiId)
    {
        $filters[] = $this->filterBuilderFactory->create()->setField('api_id')
            ->setValue($movieEntityApiId)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()->addFilters($filters)->create();
        return $this->getList($searchCriteria);
    }

    /**
     * @param $movieEntityId
     * @return \Aislan\MovieCatalog\Api\Data\MovieEntitySearchResultInterface|mixed
     */
    public function getMovieEntityById($movieEntityId)
    {
        $filters[] = $this->filterBuilderFactory->create()->setField('entity_id')
            ->setValue($movieEntityId)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()->addFilters($filters)->create();
        return $this->getList($searchCriteria);
    }
}
