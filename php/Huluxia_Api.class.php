<?php
class Huluxia_Api{
    private $config;

    private $userid;

    private $key;

    private $Common;

    public function __construct($config){
        $this->Common = new Common();
        $this->config = $config;

        if(file_exists('user.json')){
            $jsonContent = file_get_contents('user.json');
            $userData = json_decode($jsonContent, true);

            if($this->islogin($userData['_key'])){
                $this->key = $userData['_key'];
                $this->userid = $userData['userid'];
            }else{
                $login = $this->login();
                if (is_array($login)){
                    exit(array('code'=>$login['code'],'msg'=>$login['msg']));
                }
            }
        }else{
            $login = $this->login();
            if (is_array($login)){
                exit(array('code'=>$login['code'],'msg'=>$login['msg']));
            }
        }

    }

    public function islogin($_key){
        $url = "http://floor.huluxia.com/account/security/info/ANDROID/4.2.2?platform=2&gkey=000000&app_version={$this->config['application']['app_version']}&versioncode={$this->config['application']['versioncode']}&market_id={$this->config['application']['market_id']}&_key={$_key}";
        $info = json_decode($this->Common->get_link($url),true);
        $islogin = $info["msg"];
        if($islogin=="未登录"){
            return false;
        }else{
            return true;
        }
    }

    public function login(){
        $url = "https://floor.huluxia.com/account/login/ANDROID/4.1.8";
        $sign_tags = ['account','device_code','password','voice_code'];
        $params = array(
            'device_code' => $this->config['device_code'],
            'voice_code' => '',
            'account' => $this->config['phone'],
            'login_type' => '2',
            'password' => $this->config['password']
        );
        $sign = $this->ToSign($params, $sign_tags);
        if($sign){
            $params_send = http_build_query(array_merge($params, ['sign' => $sign]));
        }else{
            return false;
        }
        $callback = $this->Common->get_link($url . "?" . $params_send);
        $user = json_decode($callback,true);
        if($user['mag'] == ''){
            $user_save = json_encode(array(
                'userid' => $user['user']['userID'],
                '_key' => $user['_key']
            ));
            file_put_contents("user.json", $user_save);
            $this->key = $user['_key'];
            $this->userid = $user['user']['userID'];
            return $this->key;
        }else{
            return array('code'=>101,'msg'=>$user['mag']);
        }

    }

    public function userinfo(){
        $url = "http://floor.huluxia.com/user/info/ANDROID/2.1?platform=2&gkey=000000&app_version={$this->config['application']['app_version']}&versioncode={$this->config['application']['versioncode']}&market_id={$this->config['application']['market_id']}&_key={$this->key}&device_code={$this->config['device_code']}&user_id={$this->userid}";
        $info = json_decode($this->Common->get_link($url),true);
        $islogin = $info["msg"];
        if($islogin=="未登录"){
            return false;
        }else{
            return true;
        }
    }

    public function create_post($title,$detail,$images,$cat_id,$tag_id,$recommendTopics = ''){
        $url = "http://floor.huluxia.com/post/create/ANDROID/4.1.8";
        $params = array(
            'platform' => '2',
            'gkey' => '000000',
            'app_version' => $this->config['application']['app_version'],
            'versioncode' => $this->config['application']['versioncode'],
            'market_id' => $this->config['application']['market_id'],
            'device_code' => $this->config['device_code'],
            '_key' => $this->key,
            //以上为 URL 构造，下方为 请求 参数
            'cat_id' => $cat_id,
            'tag_id' => $tag_id,
            'type' => '0',
            'title' => $title,
            'detail' => $detail,
            'patcha' => '',
            'voice' => '',
            'lng' => '0.0',
            'lat' => '0.0',
            'images' => $images,
            'user_ids' => '',
            'recommendTopics' => $recommendTopics,
            'is_app_link' =>'4'
        );
        $sign_tags = ['_key','detail','device_code','images','title','voice'];
        $sign = $this->ToSign($params, $sign_tags);
        if($sign){
            $params_send = http_build_query(array_merge($params, ['sign' => $sign]));
        }else{
            return false;
        }

        return json_decode($this->Common->get_link($url .'?'. $params_send),true);
    }

    public function create_comment($post_id,$text){
        $url = "http://floor.huluxia.com/comment/create/ANDROID/4.1.8";
        $params = array(
            'platform' => '2',
            'gkey' => '000000',
            'app_version' => $this->config['application']['app_version'],
            'versioncode' => $this->config['application']['versioncode'],
            'market_id' => $this->config['application']['market_id'],
            'device_code' => $this->config['device_code'],
            '_key' => $this->key,
            //以上为 URL 构造，下方为 请求 参数
            'comment_id' => '0',
            'post_id' => $post_id,
            'text' => $text,
            'patcha' => '',
            'images' => '',
            'remindUsers' => '',
        );

        $sign_tags = ['_key','comment_id','device_code','images','post_id','text'];
        $sign = $this->ToSign($params, $sign_tags);
        if($sign){
            $params_send = http_build_query(array_merge($params, ['sign' => $sign]));
        }else{
            return false;
        }

        return json_decode($this->Common->get_link($url .'?'. $params_send),true);
    }

    public function uploadImage($image_path) {
        if (!file_exists($image_path)) {
            return false;
        }
        $params = array(
            '_key' => $this->key,
            'app_version' => $this->config['application']['app_version'],
            'device_code' => $this->config['device_code'],
            'gkey' => '000000',
            'market_id' => $this->config['application']['market_id'],
            'nonce_str' => $this->generateNonceStr(32),
            'platform' => '2',
            'timestamp' => $this->msectime(),
            'use_type' => '2',
            'versioncode' => $this->config['application']['versioncode'],
        );
        $sign_tags = ['_key','app_version','device_code','gkey','market_id','nonce_str','platform','timestamp','use_type','versioncode'];
        $sign = $this->ToSign_image($params, $sign_tags);
        if($sign){
            $params_send = http_build_query(array_merge($params, ['sign' => $sign]));
        }else{
            return false;
        }

        $url = 'http://upload.huluxia.com/upload/v3/image?' . $params_send;
        $post_data = array(
            '_key' => 'key_10',
            'file' => new \CURLFile(realpath($image_path), 'image/jpg', basename($image_path)),
        );

        return json_decode($this->Common->post_link($url, $post_data));
    }

    public function categoryTosign(){
        $url = "http://floor.huluxia.com/user/signin/ANDROID/4.1.8";
        $catArray = json_decode($this->Common->get_link('http://floor.huluxia.com/category/list/ANDROID/2.0'),true);
        $cats = $catArray["categories"];
        $cats[count($cats)+1]["categoryID"] = 15;	//葫芦山
        $cats[count($cats)+1]["categoryID"] = 34;	//审核部
        $cats[count($cats)+1]["categoryID"] = 94;	//三楼活动
        $cats[count($cats)+1]["categoryID"] = 84;	//三楼精选
        $cats[count($cats)+1]["categoryID"] = 69;	//优秀资源
        $cats[count($cats)+1]["categoryID"] = 67;	//MC帖子
        $cats[count($cats)+1]["categoryID"] = 68;	//资源审核
        $cats[count($cats)+1]["categoryID"] = 119;	//爱国爱党
        $cats[count($cats)+1]["categoryID"] = 0;	//我的关注
        $experience = 0;

        for ($catOid = 0; $catOid < count($cats); $catOid++) {
            $cid = $cats[$catOid]["categoryID"];
            $time = time();
            $params = array(
                'cat_id' => $cid,
                'time' => $time,
                'platform' => '2',
                'gkey' => '000000',
                'market_id' => $this->config['application']['market_id'],
                '_key' => $this->key,
                'app_version' => $this->config['application']['app_version'],
                'device_code' => $this->config['device_code'],
                'versioncode' => $this->config['application']['versioncode'],
            );
            $sign = $this->ToSign($params, ['cat_id','time'],true);

            if($sign){
                $params_send = http_build_query(array_merge($params, ['sign' => $sign]));
            }else{
                return false;
            }
            $returnJson = json_decode($this->Common->get_link($url .'?'. $params_send),true);

            $msg = $returnJson["msg"];
            $experience = $experience + $returnJson["experienceVal"];
            if ($msg == null) {
                $data[] = array("categoryID" => $cid,"time"=>$time,"msg" => "签到成功，经验+".$returnJson["experienceVal"]);
            } else {
                $data[] = array("categoryID" => $cid,"time"=>$time,"msg" => $msg);
            }
            usleep(rand(400,800));
        }
        return array(
            "code" => "ok",
            "msg" => "执行成功！",
            "time"=> time(),
            "totalDay"=>$returnJson['continueDays'],
            "totalExperience"=> $experience,
            "detailInfo" => $data,
        );
    }

    public function ToSign($args, $tags, $big = false) {
        $sign="";
        foreach($tags as $k){
            if(array_key_exists($k, $args)){
                $sign .= $k . $args[$k];
            }else{
                return false;
            }
        }
        if ($big) {
            return strtoupper(md5($sign . "fa1c28a5b62e79c3e63d9030b6142e4b"));
        }else{
            return md5($sign.'fa1c28a5b62e79c3e63d9030b6142e4b');
        }

    }

    public function ToSign_image($args, $tags, $big = true) {
        $sign="";
        foreach($tags as $k){
            if(array_key_exists($k, $args)){
                $sign .= $k . '=' . $args[$k] . '&';
            }else{
                return false;
            }
        }
        $sign .= 'secret=my_sign@huluxia.com';
        if ($big) {
            return strtoupper(md5($sign));
        }else{
            return md5($sign);
        }

    }

    public function generateNonceStr($length)
    {
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($charset) - 1);
            $result .= $charset[$randomIndex];
        }
        return $result;
    }

    function msectime() {
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }
}