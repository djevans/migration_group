<?php

namespace Drupal\migration_group\Plugin\migrate\source;

use Drupal\Core\Database\Query\SelectInterface;
use Drupal\migrate\Row;

/**
 * Migrate source plugin for Drupal 7 {og_membership}.
 *
 * @MigrateSource(
 *   id = "migration_group_d7_og_membership_nodes",
 *   source_module = "og"
 * )
 *
 * @internal
 */
class D7OgMembershipNodes extends D7OgMembership {

  public function query(): SelectInterface {
    $query = parent::query();
    $query->innerJoin('node', 'n', 'ogm.etid = n.nid');
    $query->addField('n', 'type', 'node_type');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(): array {
    return parent::fields() + [
      'node_type' => $this->t('The type of the group content node'),
    ];
  }

  protected function getContentPluginId(Row $row): string {
    return 'group_node:' . $row->getSourceProperty('node_type');
  }

}
