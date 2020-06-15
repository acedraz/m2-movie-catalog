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

namespace Aislan\MovieCatalog\Helper;

/**
 * Class Config
 */
class Config
{
    const API_KEY = 'api_key';
    const MOVIE_URL = 'catalog_movies/movie/view/';
    const VIEW_URL = 'view/id/';
    const SYSTEM_MOVIEINTEGRATION_GENERAL = 'movies_integration/general/';
    const SYSTEM_MOVIEINTEGRATION_EXIHIBITION = 'movies_integration/exhibition/';
    const SYSTEM_MOVIEINTEGRATION_ENABLE = self::SYSTEM_MOVIEINTEGRATION_GENERAL.'enable';
    const SYSTEM_MOVIEINTEGRATION_API_URL = self::SYSTEM_MOVIEINTEGRATION_GENERAL.'api_url';
    const SYSTEM_MOVIEINTEGRATION_API_KEY = self::SYSTEM_MOVIEINTEGRATION_GENERAL.self::API_KEY;
    const SYSTEM_MOVIEINTEGRATION_ATTEMPTS = self::SYSTEM_MOVIEINTEGRATION_GENERAL.'attempts';
    const SYSTEM_MOVIEINTEGRATION_API_CRON = self::SYSTEM_MOVIEINTEGRATION_GENERAL.'cron';
    const SYSTEM_MOVIEINTEGRATION_EXIHIBITION_MOVIES_ROW_QTY = self::SYSTEM_MOVIEINTEGRATION_EXIHIBITION.'movies_row_qty';
}
