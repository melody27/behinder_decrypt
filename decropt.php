<?php
$arr = getopt('a:k:f:t:d:');
echo "进入php文件\n\n\n\n\n";
// exit();
// echo "进行php 脚本中";
// try {
//     $key = $arr['k'];
// } catch (\Throwable $e) {
//     //日志方法，具体自己实现
//     $key="e45e329feb5d925b";
//  }
isset($arr['k'])?:$key='e45e329feb5d925b';
isset($arr['t'])?$http_type=$arr['t']:$http_type='requests';
// echo "this is \$key:".$key."\n this is \$http_type:".$http_type;
// echo "\$post is :".$post."\n\n";
if (isset($arr['f'])){
    $file=$arr['f'];
    $post_raw = file_get_contents($file);
    if(isset($arr['d'])){
        $arr['d'] = 't';
        unlink($file);
    }

}else{
    if (isset($arr['a'])){
        $post_raw= $arr['a'];
    }
}

function aes_convert_str($post_raw,$key){
    // preg_match('//');
    preg_match('/[a-zA-Z0-9\+\=\/]{24,}/i',$post_raw,$post);
    // echo "\$post is :".$post[0];
    // echo "aes的数据为：";
    if (count($post) === 0){
        return 'no';
    }
    // echo "进入intoaes函数：".$post[0];
    if (count($post) === 0){
        return 'no';
    }
    
    try {
        $post=openssl_decrypt($post[0], "AES128", $key);
        } catch (\Throwable $e) {
        //日志方法，具体自己实现
        // CoreHelper::write(json_encode(['eventName','order_id'=>$order->order_id??'',$e->getMessage()], JSON_UNESCAPED_UNICODE)); 
        return 'no';
    }

    // var_dump($post);
    if ($post == ''){
        return 'no';
    }
    return $post;
}


function xor_convert_str($post_raw,$key,$http_type){
    // echo "进入intoxor函数1,raw_data is :".$post_raw."xixixi\n\n\n\n\n\n\n";
    if ($http_type == 'requests'){
        // echo "this is :".$http_type."\n";
		$t="base64_"."decode";
        $post=$t($post_raw."");
        $post_raw = $post;
        // echo "this is \033[[95mrequests\033[[1m";
    }
    // echo "thisresponse";
        $post = $post_raw;
        $pattern = '<b>Warning</b>:  session_start(): Cannot send session cache limiter - headers already sent in <b>D:\phpstudy_pro\WWW\sqli-labs\shell.php</b> on line <b>3</b><br />';
        // $pattern = 'on line <b>3</b><br />';
        // echo "hereis\$post".$post."\n\n\n\n";
        // var_dump(strpos($post,$pattern));
        if(strpos($post,$pattern) != false){
            $result = substr($post,strpos($post,$pattern)+strlen($pattern)+1);
            // echo "thatshouldberesult".$result."tty";
            if($result != ''){
                $post = $result;
                // echo "intotheconvert";
            }
        }
        

		for($i=0;$i<strlen($post);$i++) {
    			 $post[$i] = $post[$i]^$key[$i+1&15]; 
                }
    // echo "进入intoxor函数2".$post."xixixi";
    // echo "this is \$post:".$post;
    return $post;
}

$post = aes_convert_str($post_raw,$key);
// echo "aes函数complate".$post."\n";
$post === 'no'?$post = xor_convert_str($post_raw,$key,$http_type):$post;
// echo "this is \$post".$post;
if(preg_match('/^\{/i',$post)){
    // echo $post;
    // exit();
    // echo "error is ".$post;
    $raw_data = json_decode($post);
    $result = [];
    try
    {
        foreach ($raw_data as $key => $value){
            $result[$key] = base64_decode($value);
        };
        echo json_encode($result);
    }
    // 捕获异常
    catch(Exception $e)
    {
        echo 'Message: ' .$e->getMessage();
    }

    exit();
}

$arr_2 = explode('|',$post);
$func = $arr_2[0];
isset($arr_2[1])?$parm=$arr_2[1]:$parm=$func;      # 解决tcp请求包截取不完整导致aes解密后没有
// print("intotheexplode:".$parm);
if ($parm === ''){
    $parm='Y29udGVudCBpcyBlbXB0eQ==';       # 解决 http截取的tcp包不完整的情况
}

preg_match('/[a-zA-Z0-9\+\=\/]{24,}/i',$parm,$last_result);
// echo "匹配到的内容为".$result[0];


if (count($last_result) > 0){
    echo base64_decode($last_result[0]);
}
else{
    echo $parm;
}
?>