<?php

namespace Drupal\datasourcehandler;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ConfigManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ivision\IVision;
use Drupal\ivision\IVisionTest;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class DatasourceServiceLocal.
 *
 * @package Drupal\datasourcehandler
 */
class DatasourceServiceLocal extends DatasourceService
{
    public function get($datasource){

        $config_sources = parent::$datasource_config->get('datasources');

        if(array_key_exists($datasource, $config_sources)){
            return new $config_sources[$datasource]['classpath']($config_sources[$datasource]['args'], TRUE);
        }else{
            throw new DatasourceException('Could not find datasource');
        }
    }

}
