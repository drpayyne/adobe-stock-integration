<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Exception;
use Magento\AdobeStockAsset\Controller\Adminhtml\OAuth\Callback;
use Magento\AdobeStockAssetApi\Api\UserProfileRepositoryInterface;
use Magento\AdobeStockClient\Model\Config;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ImagePreview
 */
class ImagePreview extends Column
{
    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var Config
     */
    private $config;

    /**
     * ImagePreview constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param Config $config
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository,
        Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        $this->setData(
            'config',
            array_replace_recursive(
                (array)$this->getData('config'),
                [
                    'authConfig' => [
                        'url' => $this->config->getAuthUrl(),
                        'isAuthorized' => $this->isAuthorized(),
                        'response' => [
                            'regexpPattern' => Callback::RESPONSE_REGEXP_PATTERN,
                            'codeIndex' => Callback::RESPONSE_CODE_INDEX,
                            'messageIndex' => Callback::RESPONSE_MESSAGE_INDEX,
                            'successCode' => Callback::RESPONSE_SUCCESS_CODE,
                            'errorCode' => Callback::RESPONSE_ERROR_CODE
                        ]
                    ]
                ]
            )
        );
    }

    /**
     * Is authorized a user
     *
     * @return bool
     */
    private function isAuthorized(): bool
    {
        try {
            $userProfile = $this->userProfileRepository->getByUserId(
                (int)$this->userContext->getUserId()
            );

            $isAuthorized = !empty($userProfile->getId())
                && !empty($userProfile->getAccessToken())
                && !empty($userProfile->getAccessTokenExpiresAt())
                && strtotime($userProfile->getAccessTokenExpiresAt()) >= strtotime('now');
        } catch (Exception $e) {
            $isAuthorized = false;
        }

        return $isAuthorized;
    }
}