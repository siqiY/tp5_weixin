<?php
namespace app\admin\controller;

use app\common\model\Menu;
use think\Controller;
use think\View;

class BaseController extends Controller
{
    public function _initialize()
    {
        View::share('menus', Menu::all());
    }
}
