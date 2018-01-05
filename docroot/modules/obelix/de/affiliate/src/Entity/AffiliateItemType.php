<?php

namespace Drupal\affiliate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\affiliate\AffiliateItemTypeInterface;

/**
 * Defines the Affiliate type entity.
 *
 * @ConfigEntityType(
 *   id = "affiliate_type",
 *   label = @Translation("Affiliate type"),
 *   handlers = {
 *     "list_builder" = "Drupal\affiliate\AffiliateItemTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\affiliate\Form\AffiliateItemTypeForm",
 *       "edit" = "Drupal\affiliate\Form\AffiliateItemTypeForm",
 *       "delete" = "Drupal\affiliate\Form\AffiliateItemTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\affiliate\AffiliateItemTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "affiliate_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "affiliate",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/affiliate_type/{affiliate_type}",
 *     "add-form" = "/admin/structure/affiliate_type/add",
 *     "edit-form" = "/admin/structure/affiliate_type/{affiliate_type}/edit",
 *     "delete-form" = "/admin/structure/affiliate_type/{affiliate_type}/delete",
 *     "collection" = "/admin/structure/affiliate_type"
 *   }
 * )
 */
class AffiliateItemType extends ConfigEntityBundleBase implements AffiliateItemTypeInterface {
  /**
   * The Affiliate type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Affiliate type label.
   *
   * @var string
   */
  protected $label;

}
