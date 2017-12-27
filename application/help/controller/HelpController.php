<?php
namespace app\help\controller;

use app\common\model\Help;
use think\Controller;

class HelpController extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function help()
    {
        $help=new Help();
        $help->question=input('question');
        if($help->save()){
            return $this->success('提交成功！','/help/help/index');
        }
        else{
            return $this->error('提交失败,请重试！');
        }
    }
}