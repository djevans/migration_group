<?php


namespace Drupal\migration_group\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a 'D7 OG Group Content Type' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "migration_group_content_type_process"
 * )
 */
class GroupContentType extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Load a GroupContentType entity by the group_type and content_plugin.
    $storage = \Drupal::entityTypeManager()->getStorage('group_content_type');
    /** @var \Drupal\group\Entity\GroupContentTypeInterface $plugin */
    $plugin = $storage->loadByProperties([
      'group_type' => '',
      'content_plugin' => '',
    ]);
    if (count($plugin)) {
      return $plugin->id();
    }
  }

}
