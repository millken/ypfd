<?php

declare(strict_types=1);

namespace Ypfd\foo\Controller;


class Index extends \Ypf\Controller\Controller {
    public function list_foo() {
        return $this->json(['status'=>true, 'body' => foo()]);
    }
}