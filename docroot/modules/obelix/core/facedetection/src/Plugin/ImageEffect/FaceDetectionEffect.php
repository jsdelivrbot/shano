<?php

namespace Drupal\facedetection\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\facedetection\FaceDetector;
use Drupal\image\ConfigurableImageEffectBase;
use Drupal\Component\Utility\Image;

/**
 * Provides a 'FaceDetection' image effect.
 *
 * @ImageEffect(
 *  id = "face_detection_effect",
 *  label = @Translation("Face detection"),
 *  description = @Translation("Detect the Face of a person and centers the face in the image.")
 * )
 */
class FaceDetectionEffect extends ConfigurableImageEffectBase
{
    /**
     * {@inheritdoc}
     */
    public function applyEffect(ImageInterface $image)
    {

        if ($image !== NULL) {

            // convert to jpg to prevent problems with face detection class
            $image->convert('jpeg');
            $image->save();

            // get the width an height from the configuration
            $width = $this->configuration['width'];
            $height = $this->configuration['height'];

            // if not both fields set calculate the missing parameter to prevent screwing the image
            if (empty($width) && empty($height)) {
                $width = $image->getWidth();
                $height = $image->getHeight();
            } else if (empty($width)) {
                $width = (int)($image->getWidth() / $image->getHeight() * $height);
            } else if (empty($height)) {
                $height = (int)($image->getHeight() / $image->getWidth() * $width);
            }

            // get the coordinates of the face
            $facedetector = new FaceDetector();
            $facedetector->faceDetect($image->getSource());
            $face = $facedetector->getFace();

            if ($face !== NULL) {
                // transform to integer
                $face['x'] = (int)$face['x'];
                $face['y'] = (int)$face['y'];
                $face['w'] = (int)$face['w'];
                $face['h'] = $face['w'];

                // get the margin values from the config an convert to px size.
                $margin_face['left'] = $this->configuration['margin']['left'] * $image->getWidth() / 100;
                $margin_face['right'] = $this->configuration['margin']['right'] * $image->getWidth() / 100;
                $margin_face['top'] = $this->configuration['margin']['top'] * $image->getHeight() / 100;
                $margin_face['bottom'] = $this->configuration['margin']['bottom'] * $image->getHeight() / 100;

                $image_crop = $face;

                // extend the face canvas by the margin values
                $image_crop['x'] -= $margin_face['left'];
                $image_crop['y'] -= $margin_face['top'];
                $image_crop['w'] += ($margin_face['right'] + $margin_face['left']);
                $image_crop['h'] += ($margin_face['bottom'] + $margin_face['top']);

                // check if some margin values overflow the original image. If they do - set them to max value.
                if ($image_crop['x'] < 0) $image_crop['x'] = 0;
                if ($image_crop['y'] < 0) $image_crop['y'] = 0;
                if ($image_crop['w'] > $image->getWidth()) $image_crop['w'] = $image->getWidth();
                if ($image_crop['h'] > $image->getHeight()) $image_crop['h'] = $image->getHeight();

                // calculate the padding from original image to the face canvas.
                $padding_croparea['left'] = $image_crop['x'];
                $padding_croparea['right'] = $image->getWidth() - ($image_crop['x'] + $image_crop['w']);
                $padding_croparea['top'] = $image_crop['y'];
                $padding_croparea['bottom'] = $image->getHeight() - ($image_crop['y'] + $image_crop['h']);


                // expand the face canvas to reach the chosen width an height.
                // for the width
                $width_diff = (int)(($width - $image_crop['w']) / 2);
                if ($width_diff < 0) {
                    // canvas to big
                    // calculate the height with the new width
                    $image_crop['h'] = (int)($image->getHeight() / $image->getWidth() * $image_crop['w']);
                } else {
                    // canvas to small
                    $image_crop['x'] -= $width_diff;
                    $image_crop['w'] += $width_diff;
                    // if face canvas cant expand on left side
                    if ($image_crop['x'] < 0) {
                        $image_crop['w'] += $image_crop['x'];
                        $image_crop['x'] = 0;
                    }
                    // if face canvas cant expand on right side
                    if ($image_crop['w'] > $width) {
                        $image_crop['w'] = $width;
                        $image_crop['x'] -= $image_crop['w'] - $width;
                    }
                }
                // for the height
                $height_diff = (int)(($height - $image_crop['h']) / 2);
                if ($height_diff < 0) {
                    // canvas to big
                    // calculate the width with the new height
                    $image_crop['w'] = (int)($image->getWidth() / $image->getHeight() * $image_crop['h']);
                } else {
                    // canvas to small
                    $image_crop['y'] -= $height_diff;
                    $image_crop['h'] += $height_diff;
                    // if face canvas cant expand on top side
                    if ($image_crop['y'] < 0) {
                        $image_crop['h'] += $image_crop['y'];
                        $image_crop['y'] = 0;
                    }
                    // if face canvas cant expand on bottom side
                    if ($image_crop['h'] > $height) {
                        $image_crop['h'] = $height;
                        $image_crop['y'] -= $image_crop['h'] - $height;
                    }
                }
                $image->crop($image_crop['x'], $image_crop['y'], $image_crop['w'], $image_crop['h']);
            }
            $image->scale($width, $height);
            return TRUE;
        } else {
            return FALSE;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state)
    {

        $form['width'] = array(
            '#type' => 'textfield',
            '#title' => t('Width'),
            '#description' => t('The with of the image'),
            '#default_value' => $this->configuration['width'],
        );
        $form['height'] = array(
            '#type' => 'textfield',
            '#title' => t('Height'),
            '#description' => t('The height of the image'),
            '#default_value' => $this->configuration['height'],
        );

        $form['margin'] = [
            '#type' => 'details',
            '#title' => 'Margin',
            '#description' => t('The margin around the cropped face in %'),
            '#open' => FALSE,
            '#tree' => TRUE,
        ];

        $form['margin']['left'] = array(
            '#type' => 'textfield',
            '#title' => t('Left'),
            '#default_value' => $this->configuration['margin']['left'],
        );
        $form['margin']['right'] = array(
            '#type' => 'textfield',
            '#title' => t('Right'),
            '#default_value' => $this->configuration['margin']['right'],
        );
        $form['margin']['top'] = array(
            '#type' => 'textfield',
            '#title' => t('Top'),
            '#default_value' => $this->configuration['margin']['top'],
        );
        $form['margin']['bottom'] = array(
            '#type' => 'textfield',
            '#title' => t('Bottom'),
            '#default_value' => $this->configuration['margin']['bottom'],
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitConfigurationForm($form, $form_state);

        $this->configuration['width'] = $form_state->getValue('width');
        $this->configuration['height'] = $form_state->getValue('height');
        $this->configuration['margin']['left'] = $form_state->getValue('margin')['left'];
        $this->configuration['margin']['right'] = $form_state->getValue('margin')['right'];
        $this->configuration['margin']['top'] = $form_state->getValue('margin')['top'];
        $this->configuration['margin']['bottom'] = $form_state->getValue('margin')['bottom'];
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        return [
            'width' => NULL,
            'height' => NULL,
            'margin' => [
                'left' => 10,
                'right' => 10,
                'top' => 10,
                'bottom' => 10,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function transformDimensions(array &$dimensions, $uri)
    {
        if ($dimensions['width'] && $dimensions['height']) {
            Image::scaleDimensions($dimensions, $this->configuration['width'], $this->configuration['height']);
        }
    }
}
