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

use Aislan\MovieCatalog\Api\Data\MovieApiInterfaceFactory;
use Aislan\MovieCatalog\Api\Data\MovieGenreInterfaceFactory;
use Aislan\MovieCatalog\Api\GenreRepositoryInterface;
use Aislan\MovieCatalog\Api\MovieApiRepositoryInterface;
use Aislan\MovieCatalog\Api\MovieGenreRepositoryInterface;
use Aislan\MovieCatalog\Api\Service\TMDApiServiceInterface;
use Aislan\MovieCatalog\Api\UpdateCatalogMovieInterface;
use Aislan\MovieCatalog\Model\ResourceModel\Genre\CollectionFactory;
use Aislan\MovieCatalog\Service\TMDApiService;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

/**
 * Class UpdateCatalogMovie
 */
class UpdateCatalogMovie implements UpdateCatalogMovieInterface
{

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
     * @var MovieApiInterfaceFactory
     */
    private $movieApiInterfaceFactory;

    /**
     * @var MovieGenreInterfaceFactory
     */
    private $movieGenreInterfaceFactory;

    /**
     * @var MovieApiRepositoryInterface
     */
    private $movieApiRepository;

    /**
     * @var MovieGenreRepositoryInterface
     */
    private $movieGenreRepository;

    /**
     * GenerateIndex constructor.
     * @param TMDApiServiceInterface $TMDApiService
     * @param CollectionFactory $collectionFactory
     * @param GenreFactory $genreFactory
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilder
     * @param FilterBuilderFactory $filterBuilder
     * @param GenreRepositoryInterface $genreRepository
     * @param MovieApiInterfaceFactory $movieApiInterfaceFactory
     * @param MovieGenreInterfaceFactory $movieGenreInterfaceFactory
     * @param MovieApiRepositoryInterface $movieApiRepository
     * @param MovieGenreRepositoryInterface $movieGenreRepository
     */
    public function __construct(
        TMDApiServiceInterface $TMDApiService,
        CollectionFactory $collectionFactory,
        GenreFactory $genreFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilder,
        FilterBuilderFactory $filterBuilder,
        GenreRepositoryInterface $genreRepository,
        MovieApiInterfaceFactory $movieApiInterfaceFactory,
        MovieGenreInterfaceFactory $movieGenreInterfaceFactory,
        MovieApiRepositoryInterface $movieApiRepository,
        MovieGenreRepositoryInterface $movieGenreRepository
    ) {
        $this->TMDApiService = $TMDApiService;
        $this->collectionFactory = $collectionFactory;
        $this->genreFactory = $genreFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->genreRepository = $genreRepository;
        $this->movieApiInterfaceFactory = $movieApiInterfaceFactory;
        $this->movieGenreInterfaceFactory = $movieGenreInterfaceFactory;
        $this->movieApiRepository = $movieApiRepository;
        $this->movieGenreRepository = $movieGenreRepository;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function updateGenre()
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

    /**
     * @return bool|mixed
     */
    public function updateMovies()
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
        foreach ($collectionResponse as $movie) {
            $items = $this->movieApiRepository->getMovieApiByApiId($movie->id);
            if (empty($items->getItems())) {
                $this->movieApiInterfaceFactory->create()
                    ->setData([
                        'popularity' => $movie->popularity,
                        'vote_count' => $movie->vote_count,
                        'video' => $movie->video,
                        'poster_path' => $movie->poster_path,
                        'api_id' => $movie->id,
                        'adult' => $movie->adult,
                        'backdrop_path' => $movie->backdrop_path,
                        'original_language' => $movie->original_language,
                        'original_title' => $movie->original_title,
                        'title' => $movie->title,
                        'vote_average' => $movie->vote_average,
                        'overview' => $movie->overview,
                        'release_date' => $movie->release_date
                    ])
                    ->save();
            }
            foreach ($movie->genre_ids as $genre) {
                $items = $this->movieGenreRepository->getMovieGenreByMovieApiId($movie->id);
                if (empty($items->getItems())) {
                    $this->createMovieGenre($movie,$genre);
                    continue;
                }
                if (!$this->haveGenre($items,$genre)) {
                    $this->createMovieGenre($movie,$genre);
                }
            }
        }
        return true;
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

    /**
     * @param MovieGenreSearchResult $items
     * @param $genre
     * @return bool
     */
    protected function haveGenre(MovieGenreSearchResult $items, $genre)
    {
        foreach ($items->getItems() as $item) {
            if ((int)$item->getGenreApiId() === (int)$genre) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $movie
     * @param $genre
     */
    protected function createMovieGenre($movie, $genre)
    {
        $this->movieGenreInterfaceFactory->create()
            ->setData([
                'movie_api_id' => $movie->id,
                'genre_api_id' => $genre
            ])
            ->save();
    }
}
