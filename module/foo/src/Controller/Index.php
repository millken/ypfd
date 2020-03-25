<?php

namespace Ypfd\foo\Controller;

class Index extends \Ypf\Controller\Controller{

  /**
   * Displays a list of materias.
   */
  public function list_foo() {
    return $this->json(array('status' => true, 'name' => 'Foot--list'));
  }

}
