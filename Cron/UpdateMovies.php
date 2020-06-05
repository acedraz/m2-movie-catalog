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

namespace Aislan\MovieCatalog\Cron;

use Aislan\MovieCatalog\Api\UpdateCatalogMovieInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateMovies
 */
class UpdateMovies
{
    protected $logger;
    /**
     * @var UpdateCatalogMovieInterface
     */
    private $updateCatalogMovie;

    public function __construct(
        LoggerInterface $logger,
        UpdateCatalogMovieInterface $updateCatalogMovie
    ) {
        $this->logger = $logger;
        $this->updateCatalogMovie = $updateCatalogMovie;
    }

    /**
     * Write to system.log
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->info('Cron in execution');
        $this->logger->info('Updating Genre');
        $this->updateCatalogMovie->updateGenre();
        $this->logger->info('Updating Movie');
        $this->updateCatalogMovie->updateMovies();
        $this->logger->info('Cron Finished');
    }
}
