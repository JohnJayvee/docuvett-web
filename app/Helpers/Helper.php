<?php

if (!function_exists('setting')) {

    function setting($key, $default = null)
    {
        if (is_null($key)) {
            return new \App\Models\Setting\Setting();
        }

        if (is_array($key)) {
            return \App\Models\Setting\Setting::set($key[0], $key[1]);
        }

        $value = \App\Models\Setting\Setting::get($key);

        return is_null($value) ? value($default) : $value;
    }
}

/**
 * Make an array of class constants and variables for passing in JS
 */
if (!function_exists('generateClassData')) {

    function generateClassData(&$exportArray, array $exceptions, $className)
    {
        if (!class_exists($className)) {
            return;
        }

        $reflectionClass = new \ReflectionClass($className);
        //Fix computed properties
        // search for class constants
        foreach ($reflectionClass->getConstants() as $constantName => $constantValue) {
            // omit CREATED_AT/UPDATED_AT constants
            if (in_array($constantName, ['CREATED_AT', 'UPDATED_AT'])) {
                continue;
            }

            // search in exceptions array
            if (isset($exceptions[$className]) && in_array($constantName, $exceptions[$className])) {
                continue;
            }

            $exportArray[$reflectionClass->getShortName()][$constantName] = $constantValue;
        }

        // search for class variables
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                continue;
            }

            if ($property->class != $className) {
                continue;
            }

            // property related to Audit
            if (in_array($property->name, ['auditingDisabled'])) {
                continue;
            }

            // search in exceptions array
            if (isset($exceptions[$property->class]) && in_array($property->name, $exceptions[$property->class])) {
                continue;
            }

            $exportArray[$reflectionClass->getShortName()][$property->name] = $reflectionClass->getStaticPropertyValue($property->name);
        }
    }
}