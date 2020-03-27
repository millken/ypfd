<?php

declare(strict_types=1);

namespace Ypfd\admin\Controller;


class Menu extends \Ypf\Controller\Controller {
    public function index() {
        $tdata = [];
        sleep(3);
        return $this->view->render('admin/theme/menu.html', $tdata);
    }
}