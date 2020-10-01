<center>解密</center>



此脚本用于冰蝎流量的解密。

> 暂时只支持将冰蝎流量解析为php原始代码。
>
> 后续有人用的话，就把该代码处理一下。直接表示为执行的命令操作。
>
>  
>
> (ps.冰蝎通信过程中 请求中的内容实际是代码。响应的内容实际上是json字符串，需要注意的是，json的value值被base64编码了)
>
> 







暂只支持php，测试环境behinder 3.0 Beta6没有问题。



直接使用php文件即可解析冰蝎流量。

```
php decropt.php -a 后面接要解密的字符串
```



```
php decropt.php -f 解密的字符串的文件
```

> 此处的密文字符串文件只允许存在密文，不允许有http请求体。



```
php decropt.php -k 秘钥 -a 解密字符串
```

> 默认的key值为冰蝎默认密码。



使用py文件可以支持解析pcap包流量。(ps.需要注意的是：一个长post包是由多个tcp组成的，需要将该http请求的tcp包截取完整，否则可能会造成解析出错。)



使用示例：

```
python3 py_decrypt.py -f /tmp/test.pcap -k qwertyuioplkjhgf
```



