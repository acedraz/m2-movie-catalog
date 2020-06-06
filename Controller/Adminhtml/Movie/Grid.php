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

namespace Aislan\MovieCatalog\Controller\Adminhtml\Movie;

use Aislan\MovieCatalog\Controller\Adminhtml\Movie;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Registry;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Aislan\MovieCatalog\Model\MovieEntityFactory;
use Aislan\MovieCatalog\Block\Adminhtml\Movie\Tab\Genre as MovieComponentsGrid;

class Grid extends Movie
{
    /**
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var MovieEntityFactory
     */
    private $movieFactory;

    /**
     * @param Context       $context
     * @param Registry      $registry
     * @param RawFactory    $resultRawFactory
     * @param LayoutFactory $layoutFactory
     * @param MovieEntityFactory $movieFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory,
        MovieEntityFactory $movieFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->movieFactory = $movieFactory;
        parent::__construct($context, $registry);
    }

    /**
     * Initialize requested mosaic and put it into registry.
     *
     * @return \Aislan\MovieCatalog\Model\MovieEntityFactory | false
     */
    protected function initMovie()
    {
        $movieId = (int) $this->getRequest()->getParam('entity_id', false);
        $movie = $this->movieFactory->create();

        if ($movieId) {
            $movie->load($movieId);
        }

        $this->registry->register('aislan_moviecatalog_movie', $movie);

        return $movie;
    }

    /**
     * Grid Action
     * Display list of movies
     *
     * @return Raw
     */
    public function execute()
    {
        $movie = $this->initMovie();
        if (!$movie) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('movie_catalog/*/', ['_current' => true, 'id' => null]);
        }

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                MovieComponentsGrid::class,
                'aislan_moviecatalog.movie.genre.grid'
            )->toHtml()
        );
    }
}
