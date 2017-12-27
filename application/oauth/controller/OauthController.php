<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2017/12/6
 * Time: 15:47
 */

namespace app\Oauth\controller;

use app\common\model\Oauth;
use think\Controller;
use Think\Db;

class OauthController extends Controller
{
//    public function __construct(){
//        $this->token = wxToken::getToken();
//        //如果获取失败则退出并返回错信息
//        if (empty($this->token)) {
//            exit('Error: get token failed.');
//        }
//    }
//    public function test()
//    {
//        $wxcfg = \weixin\wxBasic::getConfig();
//        echo ($wxcfg['appid']);
//    }
    public function oauth_userinfo()
    {
        $infos=new Oauth();
        $infos=$infos->order('Id desc')->limit('1')->find();
        $this->assign('infos',$infos);
        return $this->fetch();
    }
    public function oauth_userinfo_error()
    {
        return $this->fetch();
    }
    public function getCode()
    {
        $wxcfg = \weixin\wxBasic::getConfig();
        $return_uri = 'http://ysqi.wywwwxm.com/tp5_weixin/public/index.php/oauth/oauth/returncode';
        $get_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        $get_code_url .= '?appid='.$wxcfg['appid'] .
            '&redirect_uri='. urlencode($return_uri) .
            '&response_type=code' .
            '&scope=snsapi_userinfo' .
            '&state='. request_data('get','redirect','none') .
            '#wechat_redirect';

        return $this->redirect($get_code_url);
    }

    public function returnCode()
    {
        $wxcfg = \weixin\wxBasic::getConfig();

        $code = isset($_GET['code']) ? $_GET['code'] : '';
        if (empty($code)) {
            exit('Error: code is empty!');
        }

        $oauth_token_api = 'https://api.weixin.qq.com/sns/oauth2/access_token' .
            '?appid=' . $wxcfg['appid'] .
            '&secret=' . $wxcfg['secret'] .
            '&code=' . $code .
            '&grant_type=authorization_code';

        $wxcurl = new \weixin\wxCURL;
        $response = $wxcurl->get($oauth_token_api);

        $oauth_token = json_decode($response, true);

        $oauth_info_api = 'https://api.weixin.qq.com/sns/userinfo' .
            '?access_token=' . $oauth_token['access_token'] .
            '&openid=' . $oauth_token['openid'] .
            '&lang=zh_CN';

        $response = $wxcurl->get($oauth_info_api);
        $info = json_decode($response, true);

//echo $response;
        //echo $info;
        $view = [];

//判断是否成功获取用户信息，失败则退出并返回错误信息
        if (isset($info['errcode'])){
            $view['errorinfo']='获取用户信息失败.<br>' . $info['errmsg'];
            include('http://ysqi.wywwwxm.com/tp5_weixin/public/index.php/oauth/oauth/oauth_userinfo_error');
            exit();
        }else{
            $infos = new Oauth();
            $infos->headimgurl = $info['headimgurl'];
            $infos->nickname = $info['nickname'];
            $infos->sex=$info['sex'];
            $infos->country=$info['country'];
            $infos->province=$info['province'];
            $infos->city=$info['city'];
            $infos->where = $info['country'].$info['province'].$info['city'];
            $infos->openid = $info['openid'];
//            return $infos->fetch();
            $ret=$infos->where('nickname',$info['nickname'])->find();
            if(!$ret){
                if($infos->save()){
                    session('LoginedAdminUser',$info['nickname']);
                    return $this->success('数据存储','http://ysqi.wywwwxm.com/tp5_weixin/public/index.php/oauth/oauth/oauth_userinfo');
                } else {
                    return $this->error('写入数据库失败，请重试！');
                }
            }else{
                Db::table('oauth')->where('nickname',$info['nickname'])->order('Id desc')->limit('1')->update();
                session('LoginedAdminUser',$info['nickname']);
                return $this->success('数据更新','http://ysqi.wywwwxm.com/tp5_weixin/public/index.php/oauth/oauth/oauth_userinfo');
            }


        }

    }



}//class