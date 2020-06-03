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

use Aislan\MovieCatalog\Model\ResourceModel\Genre\CollectionFactory;
use Aislan\MovieCatalog\Model\GenreFactory;
use Aislan\MovieCatalog\Api\GenreRepositoryInterface;
use Aislan\MovieCatalog\Api\Service\TMDApiServiceInterface;
use Aislan\MovieCatalog\Helper\System;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aislan\MovieCatalog\Service\TMDApiService;

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
     * @var TMDApiServiceInterface
     */
    private $TMDApiService;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GenreFactory
     */
    private $genreFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var GenreRepositoryInterface
     */
    private $genreRepository;

    /**
     * GenerateIndex constructor.
     * @param string|null $name
     * @param System $system
     * @param TMDApiServiceInterface $TMDApiService
     * @param CollectionFactory $collectionFactory
     * @param GenreFactory $genreFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param GenreRepositoryInterface $genreRepository
     */
    public function __construct(
        string $name = null,
        System $system,
        TMDApiServiceInterface $TMDApiService,
        CollectionFactory $collectionFactory,
        GenreFactory $genreFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilder,
        FilterBuilderFactory $filterBuilder,
        GenreRepositoryInterface $genreRepository
    ) {
        parent::__construct($name);
        $this->system = $system;
        $this->TMDApiService = $TMDApiService;
        $this->collectionFactory = $collectionFactory;
        $this->genreFactory = $genreFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->genreRepository = $genreRepository;
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
        if (!$this->updateGenre()) {
            $output->writeln('<error>Error in update genre request<error>');
            return;
        }
        $output->writeln('<info>Genre updated<error>');
        $output->writeln('<info>Updating catalog movie<info>');
        if (!$this->updateMovies()) {
            $output->writeln('<error>Error in update catalog movies<error>');
            return;
        }
        $output->writeln('<info>Catalog movies updated<error>');
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function updateGenre()
    {
        $collectionResponse = $this->request(TMDApiService::GENRE_MOVIE_LIST);
        if (!$collectionResponse) {
            return false;
        }
        $response = json_decode($collectionResponse,true);
        foreach ($response['genres'] as $genre) {
            $items = $this->genreRepository->getGenreByApiId($genre['id']);
            if (empty($items->getItems())) {
                $this->genreFactory->create()
                    ->setData(['api_id' => $genre['id'],'name' => $genre['name']])
                    ->save();
            }
        }
        return true;
    }

    protected function updateMovies()
    {
        $response = json_decode($this->request(TMDApiService::DISCOVER_MOVIE));
        $totalPages = $response->total_pages;
        if (!$totalPages) {
            return false;
        }
        $collectionResponse = [];
        for ($page = 1; $page <= $totalPages; $page++) {
            $this->TMDApiService->addParams([TMDApiService::PAGE => $page]);
            $response = json_decode($this->request(TMDApiService::DISCOVER_MOVIE));
            foreach ($response->results as $result) {
                array_push($collectionResponse,$result);
            }
        }
    }

    /**
     * @param $endpoint
     * @return mixed
     */
    protected function request($endpoint)
    {
        $this->TMDApiService->setRequestEndpoint($endpoint);
        return $this->TMDApiService->execute();
    }
}
