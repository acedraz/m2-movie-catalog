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

namespace Aislan\MovieCatalog\Block\Movie;

use Aislan\MovieCatalog\Api\GenreRepositoryInterface;
use Aislan\MovieCatalog\Api\MovieEntityRepositoryInterface;
use Aislan\MovieCatalog\Api\MovieGenreRepositoryInterface;
use Aislan\MovieCatalog\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;

class View extends AbstractMovie
{
    /**
     * @var MovieEntityRepositoryInterface
     */
    private $movieEntityRepository;
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var TimezoneInterface
     */
    private $dateTime;
    /**
     * @var MovieGenreRepositoryInterface
     */
    private $movieGenreRepository;
    /**
     * @var GenreRepositoryInterface
     */
    private $genreRepository;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param Data $helperData
     * @param TimezoneInterface $dateTime
     * @param array $data
     * @param MovieEntityRepositoryInterface $movieEntityRepository
     * @param MovieGenreRepositoryInterface $movieGenreRepository
     * @param GenreRepositoryInterface $genreRepository
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        TimezoneInterface $dateTime,
        array $data = [],
        MovieEntityRepositoryInterface $movieEntityRepository,
        MovieGenreRepositoryInterface $movieGenreRepository,
        GenreRepositoryInterface $genreRepository
    ) {
        parent::__construct($context, $data);
        $this->movieEntityRepository = $movieEntityRepository;
        $this->helperData = $helperData;
        $this->dateTime = $dateTime;
        $this->movieGenreRepository = $movieGenreRepository;
        $this->genreRepository = $genreRepository;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getMovieEntity($id)
    {
        $movies = $this->movieEntityRepository->getMovieEntityById($id)->getItems();
        return reset($movies);
    }

    /**
     * @param $path
     * @return string
     */
    public function getImageUrl($path)
    {
        return $this->helperData->getImageUrl($path);
    }

    /**
     * @param $date
     * @return string
     */
    public function convertDate($date)
    {
        return $this->dateTime->date($date)->format('yyyy');
    }

    /**
     * @param $apiId
     * @return mixed
     */
    public function getGenres($apiId)
    {
        return $this->movieGenreRepository->getMovieGenreByMovieApiId($apiId)->getItems();
    }

    /**
     * @param $genreApiId
     * @return mixed
     */
    public function getGenre($genreApiId)
    {
        $genre = $this->genreRepository->getGenreByApiId($genreApiId)->getItems();
        return reset($genre);
    }
}
