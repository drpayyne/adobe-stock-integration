<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model;

/**
 * Extract data from an object using available getters
 */
class DataExtractor
{
    /**
     * Extract data from an object using available getters (does not process extension attributes)
     *
     * @param object $object
     * @param string|null $interface
     * @return array
     */
    public function extract($object, string $interface = null)
    {
        $data = [];

        $reflectionClass = new \ReflectionClass($interface ?? $object);

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if (strpos($methodName, 'get') !== 0
                || !empty($method->getParameters())
                || strpos($methodName, 'getExtensionAttributes') !== false
            ) {
                continue;
            }
            $value = $object->$methodName();
            if (!empty($value)) {
                $key = strtolower(preg_replace("/([a-z])([A-Z])/", "$1_$2", substr($methodName, 3)));
                $data[$key] = $value;
            }
        }
        return $data;
    }
}
