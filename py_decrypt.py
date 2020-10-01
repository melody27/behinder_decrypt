# import scapy
from scapy.all import *
# from scapy.utils import PcapReader
from scapy.layers.http import *
from subprocess import Popen,PIPE,STDOUT
import time
import json
import ast 
import base64
import re
import argparse


def extract_data(type_http,raw_data):
    # print('raw_data is :',raw_data)
    filename = str(int(time.time()))

    open(filename,'a+').write(raw_data)
    b = Popen('php decropt.php -f '+filename+' -t '+type_http+' -d t', shell=True, stdout=PIPE, stderr=STDOUT)

    result = b.stdout.read()
    # print("the result is :",result)
    if result.startswith(b'{'):
        # print(json.loads(get_safe_str(result),strict=False))
        print(get_safe_str(result))
    else:
        print(get_safe_str(result))

def get_safe_str(in_str) -> str:
    try:
        return in_str.decode('utf-8').strip()
    except Exception as l:
        return in_str.decode('latin1')


def main(file_path):
    raw_result = {}
    load_layer('http')
    pkts = sniff(offline=file_path,session=TCPSession)
    # pkts = sniff(offline='/tmp/true_curl_demo.pcap',session=TCPSession)
    # for pkt in pkts:
    # ls('1')
    # print(pkts[10]['HTTP']['Raw'].load.decode('utf-8'))    # this demo is avaible

    # raw_data = pkts[7]["HTTP"]['HTTPRequest']['Raw'].load.decode('latin1')
    # type_http = 'requests'
    # filename = str(int(time.time()))
    # tag = str(pkts[7]['IP'].ack)
    # raw_result[tag] = raw_data
    # open(filename,'a+').write(raw_data)
    # b = Popen('php /tmp/test/decropt_3.php -f '+filename+' -t '+type_http, shell=True, stdout=PIPE, stderr=STDOUT)

    # result = b.stdout.read()
    # print('this is result:',result.decode('latin1'))
    # exit('此处退出')

    for pkt in pkts:
        type_http = ''
        conti = False
        try:
            try:
                message = pkt["HTTP"]['HTTPRequest']['Raw'].load.decode('latin1')
                print('这是一个请求')
                type_http = 'requests'
                conti = True
            except IndexError as identifier:

                pass

            if not conti:
                try:
                    message = pkt["HTTP"]['HTTPResponse']['Raw'].load.decode('latin1')
                    print('这是一个响应')
                    type_http = 'response'
                except IndexError as identifier:
                    continue
            # print(message)
            tag = str(pkt['IP'].ack)
            if tag not in raw_result.keys():
                raw_result[tag] = []
                raw_result[tag].append(type_http)
                raw_result[tag].append(message)
            else:
                # print("加入")
                raw_result[tag][1] += message
            # print(raw_result.keys())
        except IndexError as identifier:
            continue

    print("长度为：",len(raw_result))
    # exit()


    for key,value in raw_result.items():
        extract_data(value[0],value[1])
        time.sleep(1)
        # open(key,'a+').write(value)
        print("\n\n\n")


if __name__ == "__main__":

    parse = argparse.ArgumentParser(description="redis利用脚本")
    parse.add_argument('-f','--file',help="输入pcap包文件路径")
    args = parse.parse_args()
    if not args.file:
        print("请输出pcap包路径")
        exit()

    file_path = args.file
    
    main(file_path)
