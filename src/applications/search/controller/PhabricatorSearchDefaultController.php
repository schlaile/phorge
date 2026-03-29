<?php

final class PhabricatorSearchDefaultController
  extends PhabricatorSearchBaseController {

  public function handleRequest(AphrontRequest $request) {
    $viewer = $this->getViewer();
    $viewer_phid = $viewer->getPHID();
    $engine_class = $request->getURIData('engine');

    $base_class = PhabricatorApplicationSearchEngine::class;
    if (!is_subclass_of($engine_class, $base_class)) {
      return new Aphront400Response();
    }

    $engine = newv($engine_class, array());
    $engine->setViewer($viewer);

    $key = $request->getURIData('queryKey');

    $user_phids = array(
      PhabricatorNamedQuery::SCOPE_GLOBAL,
    );
    if ($viewer_phid) {
      array_unshift($user_phids, $viewer_phid);
    }

    $named_query = id(new PhabricatorNamedQueryQuery())
      ->setViewer($viewer)
      ->withEngineClassNames(array($engine_class))
      ->withQueryKeys(array($key))
      ->withUserPHIDs($user_phids)
      ->executeOne();

    if (!$named_query && $engine->isBuiltinQuery($key)) {
      $named_query = $engine->getBuiltinQuery($key);
    }

    if (!$named_query) {
      return new Aphront404Response();
    }

    $return_uri = $engine->getQueryManagementURI();

    $builtin = null;
    if ($engine->isBuiltinQuery($key)) {
      $builtin = $engine->getBuiltinQuery($key);
    }

    if ($request->isFormPost()) {
      if (!$viewer_phid) {
        return new Aphront403Response();
      }

      $config = id(new PhabricatorNamedQueryConfigQuery())
        ->setViewer($viewer)
        ->withEngineClassNames(array($engine_class))
        ->withScopePHIDs(array($viewer_phid))
        ->executeOne();
      if (!$config) {
        $config = PhabricatorNamedQueryConfig::initializeNewQueryConfig()
          ->setEngineClassName($engine_class)
          ->setScopePHID($viewer_phid);
      }

      $config->setConfigProperty(
        PhabricatorNamedQueryConfig::PROPERTY_PINNED,
        $key);

      $config->save();

      return id(new AphrontRedirectResponse())->setURI($return_uri);
    }

    if ($named_query->getIsBuiltin()) {
      $query_name = $builtin->getQueryName();
    } else {
      $query_name = $named_query->getQueryName();
    }

    $title = pht('Set Default Query');
    $body = pht(
      'This query will become your default query in the current application.');
    $button = pht('Set Default Query');

    return $this->newDialog()
      ->setTitle($title)
      ->appendChild($body)
      ->addCancelButton($return_uri)
      ->addSubmitButton($button);
  }

}
