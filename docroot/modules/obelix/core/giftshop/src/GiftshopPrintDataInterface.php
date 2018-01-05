<?php

/**
 * @files
 * Contains the print data export service.
 *
 * @see \Drupal\giftshop\GiftshopPrintDataExport
 */

namespace Drupal\giftshop;

/**
 * Class GiftshopPrintDataExport
 *
 * @package Drupal\giftshop
 */
interface GiftshopPrintDataInterface{

  /**
   *
   *
   */
  public function getData($from, $to);

  /**
   *
   *
   */
  public function exportXLSX($from, $to);

}
