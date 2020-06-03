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

namespace Aislan\MovieCatalog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface GenreRepositoryInterface
 * @package Aislan\MovieCatalog\Api
 */
interface GenreRepositoryInterface
{
    /**
     * @param \Aislan\MovieCatalog\Api\Data\GenreInterface $myEntity
     * @return \Aislan\MovieCatalog\Api\Data\GenreInterface
     */
    public function save(\Aislan\MovieCatalog\Api\Data\GenreInterface $genre);

    /**
     * @param \Aislan\MovieCatalog\Api\Data\MyEntityInterface $myEntity
     * @return \Aislan\MovieCatalog\Api\Data\MyEntityInterface
     */
    public function delete(\Aislan\MovieCatalog\Api\Data\GenreInterface $genre);

    /**
     * @param SearchCriteriaInterface $criteria
     * @return \Aislan\MovieCatalog\Api\Data\GenreSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param $id
     * @return mixed
     */
    public function deleteById($id);

    /**
     * @param $apiId
     * @return mixed
     */
    public function getGenreByApiId($apiId);
}
