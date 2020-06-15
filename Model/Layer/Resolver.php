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

namespace Aislan\MovieCatalog\Model\Layer;

use Magento\Framework\ObjectManagerInterface;

class Resolver
{
    const CATALOG_LAYER = 'catalog';
    const CATALOG_LAYER_SEARCH = 'search';

    /**
     * Catalog view layer models list
     *
     * @var array
     */
    protected $layersPool;

    /**
     * Filter factory
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Aislan\MovieCatalog\Model\Layer
     */
    protected $layer = null;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $layersPool
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $layersPool = []
    ) {
        $this->objectManager = $objectManager;
        $this->layersPool = $layersPool;
    }

    /**
     * Get current Catalog Layer
     *
     * @return \Aislan\MovieCatalog\Model\Layer
     */
    public function get()
    {
        if (!isset($this->layer)) {
            $this->layer = $this->objectManager->create($this->layersPool[self::CATALOG_LAYER]);
        }
        return $this->layer;
    }
}
