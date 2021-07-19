<?php

namespace Drupal\migration_group\Plugin\migrate\source;

use Drupal\migrate\Row;

/**
 * Migrate source plugin for Drupal 7 {og_membership}.
 *
 * @MigrateSource(
 *   id = "migration_group_d7_og_membership_users",
 *   source_module = "og"
 * )
 *
 * @internal
 */
class D7OgMembershipUsers extends D7OgMembership {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row): bool {

    $results = $this->select('og_users_roles', 'ogur')
      ->fields('ogur', ['rid'])
      ->condition('ogur.uid', $row->getSourceProperty('etid'))
      ->condition('ogur.gid', $row->getSourceProperty('gid'))
      ->condition('ogur.group_type', $row->getSourceProperty('group_type'))
      ->execute()
      ->fetchAllKeyed(0, 0);

    if (!empty($results)) {
      $rids = array_values($results);
      $roles = array_map(function($rid) { return ['rid' => $rid]; }, $rids);
      $row->setSourceProperty('roles', $roles);
    }

    return parent::prepareRow($row);
  }

  protected function getContentPluginId(Row $row): string {
    return 'group_membership';
  }

}
