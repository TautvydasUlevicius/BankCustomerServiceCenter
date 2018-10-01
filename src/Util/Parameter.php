<?php
declare(strict_types=1);

namespace App\Util;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\Yaml\Yaml;

class Parameter
{
    const MAIN_PARAMETER_NAME = 'parameters';

    public static function get(string $parameter_name): array
    {
        $parameters = Yaml::parseFile('./config/parameters.yaml');

        if (isset($parameters[self::MAIN_PARAMETER_NAME][$parameter_name])) {
            return $parameters[self::MAIN_PARAMETER_NAME][$parameter_name];
        } else {
            throw new ParameterNotFoundException('Parameter: ' . $parameter_name . ' not found');
        }
    }
}
