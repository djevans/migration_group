<?php

namespace Drupal\migration_group\Plugin\migrate\destination;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\group\Entity\GroupType;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\destination\EntityConfigBase;

/**
 * @MigrateDestination(
 *   id = "migration_group:group_content_type"
 * )
 */
class GroupContentType extends EntityConfigBase {

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $entity_ids = [];
    if ($bundle = $row->getDestinationProperty('id')) {
      $plugin_id = join(self::DERIVATIVE_SEPARATOR, ['group_node', $bundle]);
      // Load all GroupTypes.
      $group_types = GroupType::loadMultiple();
      foreach ($group_types as $group_type) {
        /** @var \Drupal\group\Entity\GroupContentTypeInterface $group_content_type */
        $group_content_type = $this->storage->createFromPlugin($group_type, $plugin_id);
        try {
          $group_content_type->save();
          $entity_ids[] = $group_content_type->id();
        } catch (EntityStorageException $e) {
          break;
        }
      }
    }
    return (count($entity_ids) > 0);
  }
}
