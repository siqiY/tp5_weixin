<?php

namespace app\admin\controller;
use app\common\model\Menu;
use think\Controller;
use think\Request;

class MenuController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $menu=Menu::where('delete_time','NULL')->order('Id asc')->paginate('3');
        $this->assign('menu',$menu);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
    }
    public function getMenu(){
        $wxm = new \weixin\wxMenu();
        return $wxm->getMenu();
    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $condition1=[];
        $condition1['name']=input('omenuname');
        $condition1['url']=input('ourl');
        $condition1['type']=input('otype');
        $condition2=[];
        $condition2['name']=input('tmenuname');
        $condition2['url']=input('turl');
        $condition2['type']=input('ttype');
        $condition3=[];
        $condition3['name']=input('thmenuname');
        $condition3['url']=input('thourl');
        $condition3['type']=input('thotype');
        $menu=new Menu();
        $o=$menu->save($condition1);
        $t=$menu->save($condition2);
        $th=$menu->save($condition3);
        if($o&&$t){
            $menu_date =
                [
                    'button'=>
                        [
                            [
                                'type'=>'view',
                                'name'=>input('omenuname'),
                                'url'=>input('ourl')
                            ],
                            [
                            'type'=>'view',
                            'name'=>input('tmenuname'),
                            'url'=>input('turl')
                            ],
                            [
                                'type'=>'view',
                                'name'=>input('thmenuname'),
                                'url'=>input('thurl')
                            ]
                        ]
                ];

            $wxm = new \weixin\wxMenu();
            return $wxm->createMenu($menu_date);
        }else{
            return $this->error('添加菜单失败，请重试');
        }


//         $menu_date =
//         [
//             'button'=>
//             [
//                 [
//                     'type'=>'view',
//                     'name'=>input('menuname'),
//                     'url'=>input('url')
//                 ]
//             ]
//         ];
//
//         $wxm = new \weixin\wxMenu();
//         return $wxm->createMenu($menu_date);
//         $wxMenu=new wxMenu();
//         $wxMenu->name=input('menuname');
//         $wxMenu->url=input('url');
//         $wxMenu->type=input('view');
//         $wxMenu->cerateMenu();





    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $wxMenu=new wxMenu();


    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $menu = Menu::get($id);
        if ($menu->delete()) {
            return "ok";
        } else {
            return "error";
        }

    }
}
