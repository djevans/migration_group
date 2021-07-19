<?php

namespace Drupal\migration_group\Plugin\migrate\source;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\migrate\Plugin\Exception\BadPluginDefinitionException;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Migrate source plugin base for Drupal 7 {og_membership}.
 */
abstract class D7OgMembership extends DrupalSqlBase implements ConfigurableInterface {

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

    $query = $this->select('og_membership', 'ogm');
    $query->condition('entity_type', $entity_type)
          ->condition('group_type', $group_type);
    $query->fields('ogm');

    return $query;
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
      'group_type' => $this->t('The group\'s entity type'),
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

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row): bool {

    $storage = $this->entityTypeManager->getStorage('group_content_type');
    $content_plugin_id = $this->getContentPluginId($row);

    /** @var \Drupal\group\Entity\GroupContentTypeInterface[] $plugins */
    $plugins = $storage->loadByProperties([
      'group_type' => $this->configuration['group_type'],
      'content_plugin' => $content_plugin_id,
    ]);
    if ($plugins) {
      $row->setSourceProperty('group_content_type_id', reset($plugins)->id());
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
   * Get the correct group content plugin ID for a given result row.
   *
   * @param \Drupal\migrate\Row $row
   *   The source row.
   *
   * @return string
   *   The ID of the group content plugin.
   */
  abstract protected function getContentPluginId(Row $row): string;

}
