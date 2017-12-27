<?php
namespace app\admin\controller;

use app\common\model\User;
use think\Controller;
use think\Request;

class UserController extends BaseController
{
    public function index()
    {
        $user=new User();
        $user = $user->where('id','0')->order('id asc')->paginate('2');
        $this->assign('user',$user);
        return $this->fetch();
    }
    public function register()
    {
        return $this->fetch();
    }

    public function doregister(Request $request)
    {
        $captcha=input('captcha');
        //校验验证码的有效性
        if(!captcha_check($captcha,'register')){
            //验证码输入错误
            return $this->error('验证码输入错误，请重试！');
        }
        $user = new User();
        // 获取表单数据
        $user->username = $request->param('username');
        $user->password = md5(input('password'));
        // 插入到数据库中
        if ($user->save()) { // 注册成功
            return $this->success('注册成功！', '/admin/user/login');
        } else {    // 注册失败
            return $this->error('注册失败，请重试！');
        }
    }

    public function login()
    {
        return $this->fetch();
    }

    public function dologin()
    {
        $captcha=input('captcha');
        //校验验证码的有效性
        if(!captcha_check($captcha,'login')){
            //验证码输入错误
            return $this->error('验证码输入错误，请重试！');
        }
        $condition = [];
        // 获取表单数据
        $condition['username'] = input('username');
        $condition['password'] = md5(input('password'));
        // 获取匹配记录
        $user = User::where($condition)->find();
        // 判断
        if ($user) {    // 登录成功
            // 写入session
            session('loginedAdminUser', $user->username);
            // 跳转
            return $this->success('用户登录成功！', 'admin/index/index');
        } else {
            return $this->error('用户名或密码错误！');
        }
    }

    public function logout()
    {
        session('loginedUser', null);
        return $this->redirect('/');
    }
}
