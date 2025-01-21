<?php

class Common{
    public function get_emoji($num){
        $emojis = ["呵呵","哈哈","吐舌","啊","酷","怒","开心","汗","泪","黑线","鄙视","不高兴","真棒","钱","疑问","阴险","吐","咦","委屈","花心","呼~","笑眼","冷","太开心","滑稽","勉强","狂汗","乖","睡觉","惊哭","升起","惊讶","喷","爱心","心碎","玫瑰","礼物","彩虹","星星月亮","太阳","铅笔","灯泡","茶杯","蛋糕","音乐","haha","胜利","大拇指","弱","OK","赖皮","感动","十分惊讶","怒气","哭泣","吃惊","嘲弄","飘过","转圈哭","神经病","揪耳朵","惊汗","隐身","不要嘛","遁","不公平","爬来了","蛋花哭","温柔","点头","撒钱","献花","寒","傻笑","扭扭","疯","抓狂","抓","蜷","挠墙","狂笑","抱枕","吼叫","嚷","唠叨","捏脸","爆笑","郁闷","潜水","十分开心","冷笑话","顶!","潜","画圈圈","玩电脑","狂吐","哭着跑","阿狸侠","冷死了","惆怅","摸头","蹭","打滚","叩拜","摸","数钱","拖走","热","加1","压力","表逼我","人呢","摇晃","打地鼠","这个屌","恐慌","晕乎乎","浮云","给力","杯具了"];
        if ($num > count($emojis)) {
            return false;
        }
        $emos = '';
        shuffle($emojis);
        $emoji =  array_slice($emojis, 0, $num);
        foreach ($emoji as $emo){
            $emos .= '['.$emo.']';
        }
        return $emos;
    }

    public function get_yiyan(){
        $url = "https://v1.hitokoto.cn/";
        $res = $this->get_link($url);
        $return = json_decode($res,true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $sentences = [
                "掌握坚持的人是成功的，是永不言弃的。",
                "任何你的不足，在你成功的那刻，都会被人说为特色。",
                "我们在经历旅途中丰富了自己的人生，让生命变得更强壮。",
                "与其战胜敌人一万次，不如战胜自己一次。",
                "天行健，君子以自强不息。",
                "人生难得几回搏，此时不搏待何时。",
                "路漫漫其修远兮，吾将上下而求索。",
                "当时间的主人，命运的主宰，灵魂的舵手。",
                "笔落惊风雨，诗成泣鬼神。",
                "人生如棋，落子无悔。",
                "星光不问赶路人，时光不负有心人。",
                "与其抱怨，不如改变。",
                "坚持就是胜利。",
            ];
            $return['hitokoto'] = $sentences[array_rand($sentences)];
        }
        return str_replace([".", "。", "！", "梯子", "翻墙","!","?","？"], ["", "", "", "", "", "", "", ""], $return['hitokoto']);
    }

    public static function get_link($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Encoding: gzip";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_USERAGENT,'okhttp/3.8.1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    public function post_link($url,$post_data){
        $header=[
            'X-FORWARDED-FOR:'.$this->randIp(),
            'CLIENT-IP:'.$this->randIp(),
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "okhttp/3.8.1");   // 伪造ua
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function randIp(){
        return mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
    }
}