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
 * Class DatasourceService.
 *
 * @package Drupal\datasourcehandler
 */
class DatasourceService implements DatasourceServiceInterface
{

    static private $classes = array();

    static protected $datasource_config = array();

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        self::$datasource_config = \Drupal::configFactory()->get('datasourcehandler.config');
    }

    /**
     * @param $class
     */
    public static function registerClass($class)
    {
        self::$classes += $class;
    }

    /**
     * @return mixed
     */
    public static function getClasses()
    {
        return self::$classes;
    }

    public function get($datasource){

        $config_sources = self::$datasource_config->get('datasources');

        if(array_key_exists($datasource, $config_sources)){
            return new $config_sources[$datasource]['classpath']($config_sources[$datasource]['args'], FALSE);
        }else{
            throw new DatasourceException('Could not find datasource');
        }
    }

}
