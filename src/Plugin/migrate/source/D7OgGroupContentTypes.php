<?php


namespace Drupal\migration_group\Plugin\migrate\source;


use Drupal\node\Plugin\migrate\source\d7\NodeType;

/**
 * Provides a 'D7OgGroupContentTypes' migrate source.
 *
 * @MigrateSource(
 *  id = "migration_group_d7_og_group_content_types",
 *  source_module = "og"
 * )
 */
class D7OgGroupContentTypes extends NodeType {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();
    $query->innerJoin('field_config_instance', 'fci', "fci.entity_type = 'node' AND t.type = fci.bundle");
    $query->condition('fci.field_name', 'og_group_ref');
    $query->fields('fci', ['bundle']);
    return $query;
  }

}
