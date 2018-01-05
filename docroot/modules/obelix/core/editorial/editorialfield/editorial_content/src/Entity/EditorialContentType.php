<?php

/**
 * @file
 * Contains \Drupal\editorial_content\Entity\EditorialContentType.
 */

namespace Drupal\editorial_content\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\editorial_content\EditorialContentTypeInterface;

/**
 * Defines the Editorial content type entity.
 *
 * @ConfigEntityType(
 *   id = "editorial_content_type",
 *   label = @Translation("Editorial content type"),
 *   handlers = {
 *     "list_builder" = "Drupal\editorial_content\EditorialContentTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\editorial_content\Form\EditorialContentTypeForm",
 *       "edit" = "Drupal\editorial_content\Form\EditorialContentTypeForm",
 *       "delete" = "Drupal\editorial_content\Form\EditorialContentTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\editorial_content\EditorialContentTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "editorial_content_type",
 *   admin_permission = "administer editorial_content_type entities",
 *   bundle_of = "editorial_content",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/editorial-content/{editorial_content_type}",
 *     "add-form" = "/admin/structure/editorial-content/add",
 *     "edit-form" = "/admin/structure/editorial-content/{editorial_content_type}/edit",
 *     "delete-form" = "/admin/structure/editorial-content/{editorial_content_type}/delete",
 *     "collection" = "/admin/structure/editorial-content"
 *   }
 * )
 */
class EditorialContentType extends ConfigEntityBundleBase implements EditorialContentTypeInterface {
  /**
   * The Editorial content type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Editorial content type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Editorial content type category.
   *
   * @var string
   */
  protected $category;

  /**
   * The Editorial content type description.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getCategory() {
    return $this->get('category');
  }

  /**
   * {@inheritdoc}
   */
  public function setCategory($category) {
    $this->set('category', $category);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description');
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->set('description', $description);
    return $this;
  }


}
