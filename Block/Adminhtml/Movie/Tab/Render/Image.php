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

namespace Aislan\MovieCatalog\Block\Adminhtml\Movie\Tab\Render;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Aislan\MovieCatalog\Helper\Data;

/**
 * Class Image.
 */
class Image extends AbstractRenderer
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Context            $context
     * @param Data               $helper
     * @param array              $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * Render movie Image.
     *
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        if ($row->getData($this->getColumn()->getIndex())) {
            $imageUrl = $this->helper->getImageUrl($row->getData($this->getColumn()->getIndex()));
            return '<img src="'.$imageUrl.'" width=\'150\' class="admin__control-thumbnail"/>';
        }
        return '';
    }
}
