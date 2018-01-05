<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\datasourcehandler;

use Drupal\child\Entity\Child;

/**
 * Interface DatasourceChildInterface
 * @package Drupal\datasourcehandler
 */
interface DatasourceChildInterface
{
    /**
     * Returns a list of free children for sponsorship.
     * Every child should have the necessary child entity fields.
     *
     * @param $number
     * Amount of children
     * @return Child
     */
    public static function getChildrenForSponsorship($number = NULL);

    /**
     * Creates the Child image and saves the image to the children folder.
     * Sets the image as child image in the Child Entity.
     *
     * @param $child Child
     * @return Child
     */
    public static function getChildImage(Child &$child);
}
