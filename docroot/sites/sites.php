<?php

/**
 * @file
 * Configuration file for multi-site support and directory aliasing feature.
 *
 * This file is required for multi-site support and also allows you to define a
 * set of aliases that map hostnames, ports, and pathnames to configuration
 * directories in the sites directory. These aliases are loaded prior to
 * scanning for directories, and they are exempt from the normal discovery
 * rules. See default.settings.php to view how Drupal discovers the
 * configuration directory when no alias is found.
 *
 * Aliases are useful on development servers, where the domain name may not be
 * the same as the domain of the live server. Since Drupal stores file paths in
 * the database (files, system table, etc.) this will ensure the paths are
 * correct when the site is deployed to a live server.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/sites.php'.
 *
 * Aliases are defined in an associative array named $sites. The array is
 * written in the format: '<port>.<domain>.<path>' => 'directory'. As an
 * example, to map https://www.drupal.org:8080/mysite/test to the configuration
 * directory sites/example.com, the array should be defined as:
 * @code
 * $sites = array(
 *   '8080.www.drupal.org.mysite.test' => 'example.com',
 * );
 * @endcode
 * The URL, https://www.drupal.org:8080/mysite/test/, could be a symbolic link
 * or an Apache Alias directive that points to the Drupal root containing
 * index.php. An alias could also be created for a subdomain. See the
 * @link https://www.drupal.org/documentation/install online Drupal installation guide @endlink
 * for more information on setting up domains, subdomains, and subdirectories.
 *
 * The following examples look for a site configuration in sites/example.com:
 * @code
 * URL: http://dev.drupal.org
 * $sites['dev.drupal.org'] = 'example.com';
 *
 * URL: http://localhost/example
 * $sites['localhost.example'] = 'example.com';
 *
 * URL: http://localhost:8080/example
 * $sites['8080.localhost.example'] = 'example.com';
 *
 * URL: https://www.drupal.org:8080/mysite/test/
 * $sites['8080.www.drupal.org.mysite.test'] = 'example.com';
 * @endcode
 *
 * @see default.settings.php
 * @see \Drupal\Core\DrupalKernel::getSitePath()
 * @see https://www.drupal.org/documentation/install/multi-site
 */

// LIVE MULTISITE

// DEMO
$sites['demo.worldvision.de'] = 'default';
$sites['demo.worldvision.com'] = 'default';
$sites['demo.wvunity.com'] = 'default';

// DE enterprise.
$sites['dev.worldvision.de'] = 'worldvision.de';
$sites['stage.worldvision.de'] = 'worldvision.de';
$sites['www.worldvision.de'] = 'worldvision.de';

// DE enterprise test
$sites['de-test.wvunite.com'] = 'worldvision.de';
$sites['de-dev.wvunite.com'] = 'worldvision.de';
$sites['wveustagede.prod.acquia-sites.com'] = 'worldvision.de';
$sites['dev-de.wvunity.com'] = 'worldvision.de';
$sites['stage-de.wvunity.com'] = 'worldvision.de';
$sites['de.wvunity.com'] = 'worldvision.de';
$sites['wveustagetemp.prod.acquia-sites.com'] = 'worldvision.de';

// NL
$sites['dev.worldvision.nl'] = 'dev.worldvision.nl';
$sites['stage.worldvision.nl'] = 'stage.worldvision.nl';
$sites['live.worldvision.nl'] = 'live.worldvision.nl';
$sites['dev-nl.wvunity.com'] = 'dev.worldvision.nl';
$sites['stage-nl.wvunity.com'] = 'stage.worldvision.nl';
$sites['nl.wvunity.com'] = 'live.worldvision.nl';


// PLAYGROUNDS

// DEMO
$sites['dev-demo-playground.wvunite.com'] = 'default';
$sites['stage-demo-playground.wvunite.com'] = 'pg.stage.worldvision.demo';
$sites['demo-playground.wvunite.com'] = 'pg.www.worldvision.demo';

// NL
$sites['dev-nl-playground.wvunite.com'] = 'pg.worldvision.nl';
$sites['stage-nl-playground.wvunite.com'] = 'pg.worldvision.nl';
$sites['nl-playground.wvunite.com'] = 'pg.worldvision.nl';
$sites['nl-test.wvunite.com'] = 'pg.worldvision.nl';
$sites['nl-dev.wvunite.com'] = 'pg.worldvision.nl';
$sites['worldvision.nl'] = 'pg.worldvision.nl';
$sites['www.worldvision.nl'] = 'pg.worldvision.nl';
$sites['dev.worldvision.nl'] = 'pg.worldvision.nl';


if (file_exists($app_root . '/sites/sites.local.php')) {
  include $app_root . '/sites/sites.local.php';
}
