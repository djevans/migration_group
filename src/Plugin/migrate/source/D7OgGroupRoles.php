<?php

namespace Drupal\migration_group\Plugin\migrate\source;

use Drupal\Core\Language\LanguageInterface;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Provides a 'D7OgGroupRoles' migrate source.
 *
 * @MigrateSource(
 *  id = "migration_group_d7_og_group_roles",
 *  source_module = "og"
 * )
 */
class D7OgGroupRoles extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('og_role', 'role')
      ->condition('name', ['non-member', 'member'], 'NOT IN')
      ->fields('role');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'rid' => $this->t('Role ID'),
      'group_type' => $this->t("The group's entity type"),
      'group_bundle' => $this->t("The group's bundle"),
      'name' => $this->t("The role's name"),
    ];
    return $fields;
  }

  public function prepareRow(Row $row) {

    $name = $row->getSourceProperty('name');
    $transliterated = \Drupal::transliteration()->transliterate($name, LanguageInterface::LANGCODE_DEFAULT, '_');
    $transliterated = mb_strtolower($transliterated);
    $transliterated = preg_replace('@[^a-z0-9_.]+@', '_', $transliterated);
    $row->setSourceProperty('role_machine_name', $transliterated);

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'rid' => [
        'type' => 'integer'
      ]
    ];
  }

}
