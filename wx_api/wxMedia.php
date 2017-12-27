<?php

namespace weixin;


class wxMedia extends wxCURL
{
    private $upload_temp_url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=';
    private $upload_media_url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=';
    private $upload_news_url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=';
    private $get_temp_url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=';
    private $get_media_url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=';
    private $remove_media_url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=';
    private $set_news_url = 'https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=';
    private $media_list_url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=';
    private $media_total_url = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=';
    private $cur_api = '';
    private $token='';

    public function __construct(){
        $this->token = wxToken::getToken();
        //如果获取失败则退出并返回错信息
        if (empty($this->token)) {
            exit('Error: get token failed.');
        }
    }
    //上传永久素材
    public function uploadMedia($file,$type,$attach='')
    {
        $this->cur_api = $this->upload_media_url . $this->token . '&type=' . $type;
        $post_data = '';
        if ($type=='video') {
            $post_data = ['description' => $attach];
        }
        $r = $this->upload($this->cur_api,$file,'media', $post_data);
        return $r;
    }

    //上传图文素材
    public function uploadNews($news)
    {
        //如果是数组变量则转换JSON编码字符串
        if( is_array($news) ){
            $media = json_encode($news,JSON_UNESCAPED_UNICODE);
        }
        $cur_api = $this->upload_news_url  . $this->token;
        $ret = $this->post($cur_api,$media);
        return $ret;
    }

    //删除永久素材
    public function removeMedia($media_id)
    {
        $this->cur_api = $this->remove_media_url . $this->token;
        $post_data = '{"media_id":"'.$media_id.'"}';
        $r = $this->post($this->cur_api, $post_data);
        return $r;
    }

    //获取永久素材
    public function getMedia($media_id)
    {
        $this->cur_api = $this->get_media_url . $this->token;
        $r = $this->rawPost($this->cur_api,'{"media_id":"'.$media_id.'"}');
        return json_decode($r,true);
    }



    public function getTempMedia($media_id)
    {
        $this->cur_api = $this->get_temp_url . $this->token;
        $r = $this->rawPost($this->cur_api,'{"media_id":"'.$media_id.'"}');
        return json_decode($r,true);
    }

    //设置永久图文素材
    public function setNews($news)
    {
        $this->cur_api = $this->set_news_url . $this->token;
        return $this->post($this->cur_api, json_encode($news));
    }

    //获取素材总数
    public function getMediaTotal()
    {
        $this->cur_api = $this->media_total_url . $this->token;
        return $this->get($this->cur_api);
    }

    //获取素材列表
    public function getMediaList($type, $offset = 0, $count = 20)
    {
        $this->cur_api = $this->media_list_url . $this->token;
        $data = [
            'type'=>$type,
            'offset'=>$offset,
            'count'=>$count
        ];
        $r = $this->rawPost( $this->cur_api, json_encode($data) );
        return $r;
    }

    public function getMediaImage($media_id, $file_name, $to_path = './')
    {
        $this->cur_api = $this->get_media_url . $this->token;
        $data = '{"media_id":"'.$media_id.'"}';
        $r = $this->rawPost($this->cur_api,$data);

        if( !is_dir($to_path) ) {
            mkdir($to_path);
        }
        $fd = fopen($to_path . '/' . $file_name,'w+');
        /*$curl = curl_init($this->cur_api);
        $curl_opts = [
            CURLOPT_HTTPHEADER=>[
                'Content-Type: text/plain',
                //'Content-Length: ' . strlen($data)
            ],
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>$data,
            CURLOPT_FILE=>$fd
        ];
        curl_setopt_array($curl, $curl_opts);
        curl_exec($curl);
        curl_close($curl);*/

        $request = $this->_curl->newRawRequest('post',$this->cur_api,$data)
            ->setHeader('Accept-Charset', 'utf-8')
            ->setOptions([
                CURLOPT_SSL_VERIFYPEER=>false,
                CURLOPT_POST=>true,
                CURLOPT_RETURNTRANSFER=>false,
                CURLOPT_HEADER=>false,
                CURLOPT_FILE=>$fd
            ]);
        $request->send();
        fclose($fd);

    }

}
