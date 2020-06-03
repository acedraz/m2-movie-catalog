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

use Aislan\MovieCatalog\Api\Data\MovieGenreInterface;
use Magento\Framework\Model\AbstractModel;
use Aislan\MovieCatalog\Model\ResourceModel\MovieGenre as ResourceModelMovieGenre;

/**
 * Class MovieGenre
 */
class MovieGenre extends AbstractModel implements MovieGenreInterface
{
    const CACHE_TAG = 'catalog_movie_api_genre';

    const AISLAN_MOVIECATALOG_MODEL_MOVIE_GENRE = 'Aislan\MovieCatalog\Model\MovieGenre';

    const ID = 'entity_id';

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init(ResourceModelMovieGenre::AISLAN_MOVIECATALOG_MODEL_RESOURCE_MODEL_MOVIE_GENRE);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
