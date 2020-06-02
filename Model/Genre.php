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

use Aislan\MovieCatalog\Api\Data\GenreInterface;
use Magento\Framework\Model\AbstractModel;
use Aislan\MovieCatalog\Model\ResourceModel\Genre as ResourceModelGenre;

/**
 * Class Genre
 */
class Genre extends AbstractModel implements GenreInterface
{
    const CACHE_TAG = 'catalog_movie_genre';

    const AISLAN_MOVIECATALOG_MODEL_GENRE = 'Aislan\MovieCatalog\Model\Genre';

    const ID = 'id';

    const NAME = 'name';

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init(ResourceModelGenre::AISLAN_MOVIECATALOG_MODEL_RESOURCE_MODEL_GENRE);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return (string)$this->_getData(self::NAME);
    }

    /**
     * @param $name
     * @return void
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }
}
