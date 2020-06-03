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

namespace Aislan\MovieCatalog\Console\Command;

use Aislan\MovieCatalog\Api\UpdateCatalogMovieInterface;
use Aislan\MovieCatalog\Helper\System;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Update Movies
 */
class UpdateMovies extends Command
{
    /**
     * @var System
     */
    private $system;

    /**
     * @var UpdateCatalogMovieInterface
     */
    private $updateCatalogMovie;

    /**
     * UpdateMovies constructor.
     * @param string|null $name
     * @param System $system
     * @param UpdateCatalogMovieInterface $updateCatalogMovie
     */
    public function __construct(
        string $name = null,
        System $system,
        UpdateCatalogMovieInterface $updateCatalogMovie
    ) {
        parent::__construct($name);
        $this->system = $system;
        $this->updateCatalogMovie = $updateCatalogMovie;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('catalog:movies:update')
            ->setDescription('Update movies list in the TMD collection');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Executing<info>');
        $output->writeln('<info>Updating genre type movie<info>');
        if (!$this->updateCatalogMovie->updateGenre()) {
            $output->writeln('<error>Error in update genre request<error>');
            return;
        }
        $output->writeln('<info>Genre updated<error>');
        $output->writeln('<info>Updating catalog movie<info>');
        if (!$this->updateCatalogMovie->updateMovies()) {
            $output->writeln('<error>Error in update catalog movies<error>');
            return;
        }
        $output->writeln('<info>Catalog movies updated<error>');
    }
}
