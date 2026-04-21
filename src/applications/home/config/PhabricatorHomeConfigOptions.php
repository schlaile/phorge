<?php

final class PhabricatorHomeConfigOptions
  extends PhabricatorApplicationConfigOptions {

  public function getName() {
    return pht('Home');
  }

  public function getDescription() {
    return pht('Configure the home page.');
  }

  public function getIcon() {
    return 'fa-home';
  }

  public function getGroup() {
    return 'apps';
  }

  public function getApplicationClassName() {
    return PhabricatorHomeApplication::class;
  }

  public function getOptions() {
    return array(
      $this->newOption('home.logged-out-mode', 'enum', 'home')
        ->setEnumOptions(
          array(
            'home' => pht('Show Home Page'),
            'login' => pht('Show Login Screen'),
          ))
        ->setSummary(
          pht('Choose what logged-out visitors see at the home URI.'))
        ->setDescription(
          pht(
            'Choose whether logged-out visitors who navigate to the home URI '.
            'should see the home page or be redirected to the login screen. '.
            'Use the home page for public installs, or the login screen for '.
            'private installs where the home page is not useful before login.'))
        ->addExample('home', pht('Show the home page to logged-out users.'))
        ->addExample('login', pht('Redirect logged-out users to login.')),
    );
  }

}
