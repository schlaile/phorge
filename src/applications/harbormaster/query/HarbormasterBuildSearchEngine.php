<?php

final class HarbormasterBuildSearchEngine
  extends PhabricatorApplicationSearchEngine {

  public function getResultTypeDescription() {
    return pht('Harbormaster Builds');
  }

  public function getApplicationClassName() {
    return PhabricatorHarbormasterApplication::class;
  }

  public function newQuery() {
    return new HarbormasterBuildQuery();
  }

  protected function buildCustomSearchFields() {
    return array(
      id(new PhabricatorSearchDatasourceField())
        ->setLabel(pht('Build Plans'))
        ->setKey('plans')
        ->setAliases(array('plan'))
        ->setDescription(
          pht('Search for builds running a given build plan.'))
        ->setDatasource(new HarbormasterBuildPlanDatasource()),
      id(new PhabricatorPHIDsSearchField())
        ->setLabel(pht('Buildables'))
        ->setKey('buildables')
        ->setAliases(array('buildable'))
        ->setDescription(
          pht('Search for builds running against particular buildables.')),
      id(new PhabricatorSearchDatasourceField())
        ->setLabel(pht('Statuses'))
        ->setKey('statuses')
        ->setAliases(array('status'))
        ->setDescription(
          pht('Search for builds with given statuses.'))
        ->setDatasource(new HarbormasterBuildStatusDatasource()),
      id(new PhabricatorSearchDatasourceField())
        ->setLabel(pht('Initiators'))
        ->setKey('initiators')
        ->setAliases(array('initiator'))
        ->setDescription(
          pht(
            'Search for builds started by someone or something in particular.'))
        ->setDatasource(new HarbormasterBuildInitiatorDatasource()),
    );
  }

  protected function getHiddenFields() {
    return array(
      'buildables',
    );
  }

  protected function buildQueryFromParameters(array $map) {
    $query = $this->newQuery();

    if ($map['plans']) {
      $query->withBuildPlanPHIDs($map['plans']);
    }

    if ($map['buildables']) {
      $query->withBuildablePHIDs($map['buildables']);
    }

    if ($map['statuses']) {
      $query->withBuildStatuses($map['statuses']);
    }

    if ($map['initiators']) {
      $query->withInitiatorPHIDs($map['initiators']);
    }

    return $query;
  }

  protected function getURI($path) {
    return '/harbormaster/build/'.$path;
  }

  protected function getBuiltinQueryNames() {
    $names = array();

    if ($this->requireViewer()->isLoggedIn()) {
      $names['initiated'] = pht('My Builds');
    }

    $names['all'] = pht('All Builds');
    $names['waiting'] = pht('Waiting');
    $names['active'] = pht('Active');
    $names['completed'] = pht('Completed');

    return $names;
  }

  public function buildSavedQueryFromBuiltin($query_key) {
    $query = $this->newSavedQuery();
    $query->setQueryKey($query_key);
    $viewer = $this->requireViewer();
    $viewer_phid = $viewer->getPHID();

    switch ($query_key) {
      case 'initiated':
        if (!$viewer_phid) {
          return $query;
        }
        return $query->setParameter('initiators', array($viewer_phid));
      case 'all':
        return $query;
      case 'waiting':
        return $query
          ->setParameter(
            'statuses',
            HarbormasterBuildStatus::getWaitingStatusConstants());
      case 'active':
        return $query
          ->setParameter(
            'statuses',
            HarbormasterBuildStatus::getActiveStatusConstants());
      case 'completed':
        return $query
          ->setParameter(
            'statuses',
            HarbormasterBuildStatus::getCompletedStatusConstants());
    }

    return parent::buildSavedQueryFromBuiltin($query_key);
  }

  /**
   * @param array<HarbormasterBuild> $builds
   * @param PhabricatorSavedQuery $query
   * @param array<PhabricatorObjectHandle> $handles
   */
  protected function renderResultList(
    array $builds,
    PhabricatorSavedQuery $query,
    array $handles) {
    assert_instances_of($builds, HarbormasterBuild::class);

    $viewer = $this->requireViewer();

    $list = id(new HarbormasterBuildView())
      ->setViewer($viewer)
      ->setBuilds($builds)
      ->newObjectList();

    return id(new PhabricatorApplicationSearchResultView())
      ->setObjectList($list)
      ->setNoDataString(pht('No builds found.'));
  }

}
