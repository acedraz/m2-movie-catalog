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

namespace Aislan\MovieCatalog\Model\ResourceModel\Genre;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aislan\MovieCatalog\Model\ResourceModel\Genre;
use Aislan\MovieCatalog\Model\Genre as ModelGenre;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{

    protected $_idFieldName = Genre::ID_FIELD_NAME;

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            ModelGenre::AISLAN_MOVIECATALOG_MODEL_GENRE,
            Genre::AISLAN_MOVIECATALOG_MODEL_RESOURCE_MODEL_GENRE
        );
    }
}
