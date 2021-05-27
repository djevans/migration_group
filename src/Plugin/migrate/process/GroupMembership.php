<?php

namespace Drupal\migration_group\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a 'D7 OG Group Membership' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "migration_group_d7_og_membership_process"
 * )
 */
class GroupMembership extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    switch ($row->getSourceProperty('entity_type')) {
      case 'node':
        $value = 'group_node';
        break;
      case 'user':
        $value = 'group_membership';
        break;
      default:
        break;
    }
    return $value;
  }

}
