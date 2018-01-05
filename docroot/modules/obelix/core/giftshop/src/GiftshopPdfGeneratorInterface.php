<?php

/**
 * @file
 * Provides the interface for the gift shop pdf generator.
 */

namespace Drupal\giftshop;

use Drupal\file\FileInterface;

/**
 * Interface GiftshopPdfGeneratorInterface
 * @package Drupal\giftshop
 */
interface GiftshopPdfGeneratorInterface {

  /**
   * Generates a personalized PDF from a template file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The File entity to use for generation. If the physical file behind the
   *   entity isn't a pdf an exception will be thrown.
   * @param string $from
   *   The name of the person this pdf comes from.
   * @param string $to
   *   The name of the person this pdf is generated for.
   *
   * @throws
   *  GiftShopPdfGeneratorException
   *
   * @return \Drupal\file\FileInterface
   *   A new file entity with the desired modifications.
   */
  public function generate(FileInterface $file, $from, $to);
}



