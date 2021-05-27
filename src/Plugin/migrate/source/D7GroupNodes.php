<?php

namespace Drupal\migration_group\Plugin\migrate\source;

use Drupal\node\Plugin\migrate\source\d7\Node;

/**
 * Provides a 'D7GroupNodes' migrate source.
 *
 * @MigrateSource(
 *  id = "migration_group_d7_group_nodes",
 *  source_module = "og"
 * )
 */
class D7GroupNodes extends Node {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();
    $query->innerJoin('field_data_group_group', 'gg', "n.nid = gg.entity_id AND gg.entity_type = 'node'");
    $query->fields('gg', ['group_group_value']);
    $query->condition('gg.group_group_value', 1);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'group_group' => $this->t('Whether the node is an OG group node'),
    ];
    return parent::fields() + $fields;
  }

}
