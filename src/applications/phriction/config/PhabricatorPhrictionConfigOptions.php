<?php

final class PhabricatorPhrictionConfigOptions
  extends PhabricatorApplicationConfigOptions {

  public function getName() {
    return pht('Phriction');
  }

  public function getDescription() {
    return pht('Configure Phriction.');
  }

  public function getIcon() {
    return 'fa-book';
  }

  public function getGroup() {
    return 'apps';
  }

  public function getApplicationClassName() {
    return PhabricatorPhrictionApplication::class;
  }

  public function getOptions() {
    return array(
      $this->newOption('phriction.document-actions', 'enum', 'curtain')
        ->setEnumOptions(
          array(
            'curtain' => pht('Visible Side Menu'),
            'dropdown' => pht('Header Dropdown'),
          ))
        ->setSummary(
          pht('Choose how Phriction document actions are displayed.'))
        ->setDescription(
          pht(
            'Choose how actions like edit, history, move, and delete are '.
            'presented on Phriction document pages. Use the visible side menu '.
            'to keep actions always on the page, or use the header dropdown '.
            'to keep the document layout more compact.'))
        ->addExample('curtain', pht('Show actions in a visible side menu.'))
        ->addExample('dropdown', pht('Show actions in a header dropdown.')),
    );
  }

}
