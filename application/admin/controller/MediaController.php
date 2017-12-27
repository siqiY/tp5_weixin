<?php
/**
 * Created by PhpStorm.
 * User: dzdn
 * Date: 2017/11/22
 * Time: 20:26
 */

namespace app\admin\controller;
use app\common\model\Media;
use app\common\model\Media_0;
use app\common\model\Media_1;
use think\Controller;
use think\Db;
use think\Request;
use think\Upload;


class MediaController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $media=Media::where('delete_time','NULL')->order('Id asc')->paginate('3');
        $this->assign('media',$media);
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
    public function getMedia(){
        $wxm = new \weixin\wxMedia();
        return $wxm->getMedia();
    }
    public function uploadmedia(Request $request)
    {
        $file=$request->file('image');
        if($file) {
            $info = $file->move('uploads', '');
            $image =ROOT_PATH.'public/uploads/' . $info->getSaveName();
            $wxm = new \weixin\wxMedia();
            $ret = $wxm->uploadMedia($image, 'image');
            $re = json_decode($ret, true);
            $condition['type'] = input('type');
            $condition['name']=$info->getSaveName();
            $condition['media_id'] = $re['media_id'];
            $condition['url'] = $re['url'];
            $condition['address'] = 'http://ysqi.wywwwxm.com/tp5_weixin/public/static/uploads/' . $info->getSaveName();
            $new = new Media_0();
            $fo = $new->save($condition);
            if ($fo) {
                return $this->success('成功', '/admin/media/create');
            } else {
                return $this->error('失败，请重试');
            }
        }else{
            return $this->error('没有获取图片');
        }
    }

    public function uploadNews()
    {
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $condition1=[];
        $condition1['title']=input('title');
        $condition1['thumb_media_id']=input('thumb_media_id');
        $condition1['author']=input('author');
        $condition1['digest']=input('digest');
        $condition1['show_cover_pic']=input('show_cover_pic');
        $condition1['content']=input('content');
        $condition1['content_source_url']=input('content_source_url');
        $media=new Media();
        $o=$media->save($condition1);
        if($o){
            //设置图文数据
            $news_article = [
                'articles'=>[
                    [
                        'title'=>input('title'),
                        'thumb_media_id'=>
                            input('thumb_media_id'),
                        'author'=>input('author'),
                        'digest'=>input('digest'),
                        'show_cover_pic'=>input('1'),
                        'content'=>input('content'),
                        'content_source_url'=>input('')
                    ]
                ]
            ];
            $wxm = new \weixin\wxMedia();
            $a= $wxm->uploadNews($news_article);
            $a=json_decode($a,true);

            if($a['media_id']){
                return $this->success('上传成功！');
            }else{
                return $this->error('微信端上传图文素材错误！');
            }
        }
        else{
            return $this->error('上传图文素材失败，请重试！');
        }

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $wxMenu=new wxMedia();


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
        $media = Media::get($id);
        if ($media->delete()) {
            return "ok";
        } else {
            return "error";
        }
    }
}