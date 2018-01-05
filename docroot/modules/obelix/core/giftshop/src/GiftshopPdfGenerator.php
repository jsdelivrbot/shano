<?php

/**
 * @file
 * Provides the Giftshop Pdf Generator.
 */

namespace Drupal\giftshop;

use Drupal\file\Entity\File;
use Drupal\file\FileInterface;

class GiftshopPdfGenerator implements GiftshopPdfGeneratorInterface {

  /**
   * {@inheritdoc}
   */
  public function generate(FileInterface $file, $from, $to) {
    if ($file->getMimeType() != 'application/pdf') {
      throw new GiftshopPdfGeneratorException('File must have a valid PDF mimetype.');
    }

    $pdf = new \setasign\Fpdi\Fpdi();
    $pdf->AddPage();

    $pdf->setSourceFile($file->getFileUri());
    $pdf->useTemplate($pdf->importPage(1), 0, 0);

    $pdf->SetFont('Arial');
    $pdf->SetTextColor(0, 0, 0);

    $pdf->SetXY(12, 255);
    $pdf->Write(0, utf8_decode($from));

    $pdf->SetXY(12, 230);
    $pdf->Write(0, utf8_decode($to));

    $directory = 'public://giftshop/pdf-generator/' .
      \Drupal::service('uuid')->generate();

    file_prepare_directory($directory, FILE_CREATE_DIRECTORY);

    $file = file_save_data($pdf->Output('S'), $directory . '/' . $file->getFilename());

    return $file;
  }
}


