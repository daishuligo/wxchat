<?php
/**
 * Created by PhpStorm.
 * User: ligo
 * Date: 2019/12/30
 * Time: 11:47
 */

namespace Daishuwx\Wxapp;


use Daishuwx\Base;

/**
 * 生物认证
 * Class Soter
 * @package Daishuwx\Wxapp
 */
class Soter extends Base
{
    /**
     * SOTER 生物认证秘钥签名验证
     * @param $openId 用户 openid
     * @param $string 通过 wx.startSoterAuthentication 成功回调获得的 resultJSON 字段
     * @param $signature 通过 wx.startSoterAuthentication 成功回调获得的 resultJSONSignature 字段
     * @return array
     *User: ligo
     */
    public function verifySignature($openId,$string,$signature){
        $postData = [
            'openid'  => $openId,
            'json_string'  => $string,
            'json_signature'  => $signature,
        ];
        $postData = json_encode($postData,JSON_UNESCAPED_UNICODE);

        $url = 'https://api.weixin.qq.com/cgi-bin/soter/verify_signature?access_token='.$this->token;
        $res = $this->curl_post($url,$postData);
        $res = json_decode($res,true);
        if(isset($res['errcode']) && $res['errcode'] == 0){
            return [
                'status' => true,
                'msg'    => '验证成功',
                'data'   => $res,
            ];
        }else{
            $wxCode = $this->config->get('wxcode');
            if(isset($res['errcode'])){
                $msg = isset($wxCode[$res['errcode']]) ? $wxCode[$res['errcode']] : (isset($res['errmsg']) ? $res['errmsg'] : '请求失败');
            }else{
                $msg = '请求失败';
            }
            return [
                'status' => false,
                'msg'    => $msg,
                'data'   => $res,
            ];
        }

    }
}