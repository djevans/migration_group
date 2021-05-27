<?php

namespace Drupal\migration_group\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a 'HashedId' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "migration_group_hashed_id"
 * )
 */
class HashedId extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (strlen($value) > EntityTypeInterface::BUNDLE_MAX_LENGTH) {
      // Return a hashed ID if the readable ID would exceed the maximum length.
      $value = join('_', [
        $row->getSourceProperty('group_bundle'),
        md5($value),
      ]);
      $value = substr($value, 0, EntityTypeInterface::BUNDLE_MAX_LENGTH);
    }
    return $value;
  }

}
