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

use Aislan\MovieCatalog\Api\Data\MovieEntityInterface;
use Magento\Framework\Model\AbstractModel;
use Aislan\MovieCatalog\Model\ResourceModel\MovieEntity as ResourceModelMovieEntity;

/**
 * Class MovieGenre
 */
class MovieEntity extends AbstractModel implements MovieEntityInterface
{
    const CACHE_TAG = 'catalog_movie_entity';

    const AISLAN_MOVIECATALOG_MODEL_MOVIE_ENTITY = 'Aislan\MovieCatalog\Model\MovieEntity';

    const ID = 'entity_id';

    const POPULARITY = 'popularity';

    const VOTE_COUNT = 'vote_count';

    const VIDEO  = 'video';

    const POSTER_PATH = 'poster_path';

    const API_ID = 'api_id';

    const ADULT = 'adult';

    const BACKDROP_PATH = 'backdrop_path';

    const ORIGINAL_LANGUAGE = 'original_language';

    const ORIGINAL_TITLE = 'original_title';

    const TITLE = 'title';

    const VOTE_AVERAGE = 'vote_average';

    const OVERVIEW = 'overview';

    const RELEASE_DATE = 'release_date';

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init(ResourceModelMovieEntity::AISLAN_MOVIECATALOG_MODEL_RESOURCE_MODEL_MOVIE_ENTITY);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string[]
     */
    public function getEntries()
    {
        return [
            self::ID,
            self::POPULARITY,
            self::VOTE_COUNT,
            self::VIDEO,
            self::POSTER_PATH,
            self::API_ID,
            self::ADULT,
            self::BACKDROP_PATH,
            self::ORIGINAL_LANGUAGE,
            self::ORIGINAL_TITLE,
            self::VOTE_AVERAGE,
            self::OVERVIEW,
            self::RELEASE_DATE
        ];
    }
}
