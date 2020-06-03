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

namespace Aislan\MovieCatalog\Model\ResourceModel;

use Aislan\MovieCatalog\Model\MovieApi as ModelMovieApi;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class MovieApi
 */
class MovieApi extends AbstractDb
{

    const AISLAN_MOVIECATALOG_MODEL_RESOURCE_MODEL_MOVIE_API = 'Aislan\MovieCatalog\Model\ResourceModel\MovieApi';
    const ID_FIELD_ID = 'entity_id';

    protected function _construct()
    {
        $this->_init(ModelMovieApi::CACHE_TAG,self::ID_FIELD_ID);
    }
}
