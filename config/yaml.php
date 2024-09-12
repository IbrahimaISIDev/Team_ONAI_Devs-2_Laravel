<?php

use Symfony\Component\Yaml\Yaml;

$yamlFile = base_path('config/authentication.yaml');
return Yaml::parseFile($yamlFile);