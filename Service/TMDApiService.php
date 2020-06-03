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

namespace Aislan\MovieCatalog\Service;

use Aislan\MovieCatalog\Api\Service\TMDApiServiceInterface;
use Aislan\MovieCatalog\Helper\Config;
use Aislan\MovieCatalog\Helper\System;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;
use Psr\Log\LoggerInterface;

/**
 * Class TMDApiService
 */
class TMDApiService implements TMDApiServiceInterface
{

    const API_REQUEST_URI = 'https://api.themoviedb.org/3/';

    const GENRE_MOVIE_LIST = 'genre/movie/list';

    const DISCOVER_MOVIE = 'discover/movie';

    const PAGE = 'page';

    const URL_PATH_IMAGE = 'https://image.tmdb.org/t/p/w300_and_h450_bestv2';

    /**
     * @var string
     */
    private $apiRequestUri;

    /**
     * @var string
     */
    private $apiRequestKey;

    /**
     * @var string
     */
    private $apiRequestEndpoint;

    /**
     * @var int
     */
    private $apiAttempts;

    /**
     * @var ClientFactory
     */
    private $_clientFactory;

    /**
     * @var ResponseFactory
     */
    private $_responseFactory;

    /**
     * @var System
     */
    private $system;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var \string[][]
     */
    private $params;

    /**
     * TMDApiService constructor.
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param System $system
     * @param LoggerInterface $_logger
     */
    public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        System $system,
        LoggerInterface $_logger
    ) {
        $this->_clientFactory = $clientFactory;
        $this->_responseFactory = $responseFactory;
        $this->system = $system;
        $this->apiRequestUri = $this->system->getApiUrl();
        $this->apiRequestKey = $this->system->getApiKey();
        $this->apiAttempts = $this->system->getApiAttempts();
        $this->_logger = $_logger;
        $this->apiRequestEndpoint = self::DISCOVER_MOVIE;
        $this->params = [RequestOptions::QUERY => [Config::API_KEY => $this->apiRequestKey]];
    }

    /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     *
     * @return Response
     */
    private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {
        if (empty($this->apiRequestUri)) {
            $this->apiRequestUri = self::API_REQUEST_URI;
        }
        $client = $this->_clientFactory->create(['config' => [
            'base_uri' => $this->apiRequestUri
        ]]);
        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            $response = $this->_responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }
        return $response;
    }

    /**
     * Fetch some data from API
     */
    public function execute()
    {

        $attempts = 0;
        do {
            $response = $this->doRequest($this->apiRequestEndpoint,$this->params);
            $status = $response->getStatusCode();
        } while ((int)$status != 200 && $attempts < $this->apiAttempts);
        if ($status != 200)
        {
            $this->_logger->critical('Error in API request');
            return false;
        }
        $responseBody = $response->getBody();
        $responseContent = $responseBody->getContents();
        return $responseContent;
    }

    /**
     * @param $endpoint
     */
    public function setRequestEndpoint($endpoint)
    {
        $this->apiRequestEndpoint = $endpoint;
    }

    /**
     * @param $params
     */
    public function addParams($params)
    {
        foreach ($params as $key => $value) {
            $this->params[RequestOptions::QUERY][$key] = $value;
        }
    }
}
