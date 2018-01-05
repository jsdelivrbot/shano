<?php

/**
 * User: MoeVoe
 * Date: 28.06.16
 * Time: 18:59
 */

namespace Drupal\facedetection;

use svay\FaceDetector as FaceDetectorSvay;

class FaceDetector extends FaceDetectorSvay
{

    public function getFace()
    {
        return parent::getFace();
    }

}
