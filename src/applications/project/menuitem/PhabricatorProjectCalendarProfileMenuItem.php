<?php

final class PhabricatorProjectCalendarProfileMenuItem
  extends PhabricatorProfileMenuItem {

  const MENUITEMKEY = 'project.calendar';

  public function getMenuItemTypeName() {
    return pht('Project Calendar');
  }

  private function getDefaultName() {
    return pht('Calendar');
  }

  public function getMenuItemTypeIcon() {
    return 'fa-calendar';
  }

  public function shouldEnableForObject($object) {
    $viewer = $this->getViewer();

    $class = PhabricatorCalendarApplication::class;
    return PhabricatorApplication::isClassInstalledForViewer($class, $viewer);
  }

  public function getDisplayName(
    PhabricatorProfileMenuItemConfiguration $config) {
    $name = $config->getMenuItemProperty('name');

    if (phutil_nonempty_string($name)) {
      return $name;
    }

    return $this->getDefaultName();
  }

  public function buildEditEngineFields(
    PhabricatorProfileMenuItemConfiguration $config) {
    return array(
      id(new PhabricatorTextEditField())
        ->setKey('name')
        ->setLabel(pht('Name'))
        ->setPlaceholder($this->getDefaultName())
        ->setValue($config->getMenuItemProperty('name')),
    );
  }

  protected function newMenuItemViewList(
    PhabricatorProfileMenuItemConfiguration $config) {
    $project = $config->getProfileObject();

    $uri = urisprintf(
      '/calendar/?isCancelled=active&projects=%s#R',
      $project->getPHID());

    $item = $this->newItemView()
      ->setURI($uri)
      ->setName($this->getDisplayName($config))
      ->setIcon('fa-calendar');

    return array(
      $item,
    );
  }

}
