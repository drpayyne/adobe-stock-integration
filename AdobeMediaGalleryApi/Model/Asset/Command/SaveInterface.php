<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Model\Asset\Command;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;

/**
 * Save media asset.
 *
 * @api
 */
interface SaveInterface
{
    /**
     * Save media asset
     *
     * @param \Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface $asset
     *
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(AssetInterface $asset): int;
}
