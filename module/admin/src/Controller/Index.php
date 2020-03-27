<?php

declare(strict_types=1);

namespace Ypfd\admin\Controller;


class Index extends \Ypf\Controller\Controller {
    public function list_bar() {
        $menu = [
            [
                'name' => '系统', 
                'icon' => 'ivu-icon ivu-icon-md-settings',
                'key'=> '1', 
                'children' => [
                    [
                        'type' => 'group',
                        'name' => '系统管理',
                        'child' => [
                            [
                                'key' => 2,
                                'url' => '/admin/system/menu/index',
                                'name' => '菜单管理'
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => '用户', 
                'key'=> '1', 
                'children' => [
                    [
                        'type' => 'group',
                        'name' => '内容管理',
                        'child' => [
                            [
                                'key' => 2,
                                'url' => '/admin/system/menu2/index',
                                'name' => '文章管理'
                            ],
                            [
                                'key' => 3,
                                'name' => '评论管理'
                            ],
                        ]
                    ],
                    [
                        'type' => 'item',
                        'name' => '用户管理',
                    ],
                    [
                        'type' => 'submenu',
                        'key' => 4,
                        'name' => '统计分析',
                        'icon' => 'ios-stats',
                        'child' => [
                            [
                                'key' => 2,
                                'name' => '文章管理'
                            ],
                            [
                                'key' => 3,
                                'name' => '评论管理'
                            ],
                        ]
                    ]
                ]
            ]
        ];
        $tdata['menu'] = json_encode($menu);
        return $this->view->render('admin/theme/index.html', $tdata);
    }
}