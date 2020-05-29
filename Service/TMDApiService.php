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

    const API_REQUEST_URI = 'https://api.themoviedb.org/3/';

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
        $this->apiRequestEndpoint = Config::DISCOVER_MOVIE;
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
        /** @var Client $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => $this->apiRequestUri
        ]]);
        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
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
        $params = [Config::API_KEY => $this->apiRequestKey];
        $attempts = 0;
        do {
            $response = $this->doRequest($this->apiRequestEndpoint,$params);
            $status = $response->getStatusCode();
        } while ((int)$status != 200 && $attempts < $this->apiAttempts);
        if ($status != 200)
        {
            $this->logger->critical('Error in API request');
            return;
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
}