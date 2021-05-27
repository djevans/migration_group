<?php

namespace Drupal\migration_group\Plugin\migrate\source;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\migrate\Plugin\Exception\BadPluginDefinitionException;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Migrate source plugin for Drupal 7 {og_membership}.
 *
 * @MigrateSource(
 *   id = "migration_group_d7_og_membership",
 *   source_module = "og"
 * )
 *
 * @internal
 */
class D7OgMembership extends DrupalSqlBase implements ConfigurableInterface {

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\migrate\Plugin\Exception\BadPluginDefinitionException
   */
  public function query(): SelectInterface {
    if (!isset($this->configuration['entity_type'])) {
      throw new BadPluginDefinitionException('migration_group_d7_og_membership', 'entity_type');
    }
    if (!isset($this->configuration['group_type'])) {
      throw new BadPluginDefinitionException('migration_group_d7_og_membership', 'group_type');
    }
    $entity_type = $this->configuration['entity_type'];
    $group_type = $this->configuration['og_group_type'];

    return $this->select('og_membership', 'ogm')
      ->fields('ogm')
      ->condition('entity_type', $entity_type)
      ->condition('group_type', $group_type);
  }

  /**
   * {@inheritdoc}
   */
  public function fields(): array {
    return [
      'id' => $this->t('The ID of the membership'),
      'type' => $this->t('The type of the membership'),
      'etid' => $this->t('The member entity\'s ID'),
      'entity_type' => $this->t('The member entity\'s type'),
      'gid' => $this->t('The group ID'),
      'group_type' => $this->t('The ID of the membership'),
      'state' => $this->t('The state of the membership'),
      'created' => $this->t('The time the membership was created'),
      'field_name' => $this->t('The ID of the membership'),
      'language' => $this->t('The language code'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds(): array {
    $ids['id']['type'] = 'integer';
    $ids['id']['alias'] = 'ogm';
    return $ids;
  }

  public function prepareRow(Row $row): bool {

    $storage = \Drupal::entityTypeManager()->getStorage('group_content_type');
    /** @var \Drupal\group\Entity\GroupContentTypeInterface[] $plugins */
    $plugins = $storage->loadByProperties([
      'group_type' => $this->configuration['group_type'],
      'content_plugin' => $this->configuration['content_plugin'],
    ]);
    if ($plugins) {
      $row->setSourceProperty('group_content_type_id', reset($plugins)->id());
    }

    if ($this->isUserMembershipMigration()) {
      $roles = [];
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
    }

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration(): array {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep($this->configuration, $configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'entity_type' => 'node',
      'group_type' => 'node',
    ];
  }
  /**
   * Determines if this source is configured for users or other entities.
   *
   * @return bool
   *   TRUE if the migration is for users.
   */
  protected function isUserMembershipMigration(): bool {
    $entity_type = $this->configuration['entity_type'] ?? 'any';
    return ('user' === $entity_type);
  }

}
