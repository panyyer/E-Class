<?php
namespace common\models;

use yii\db\ActiveRecord;

class Token extends ActiveRecord
{
	//过期时间 3600*24*30 = 2592000
	const TIME = 2592000;

    /*
     * 检验access_token的合法性
     * @param $token $type(授权的角色)
     */
	public static function Auth($token)
	{
		$result = ['status' => false, 'code' => 4004]; //未知错误
		$_token = Token::find()->where('access_token=:token', [':token'=>$token])->one();
        if(!empty($_token)) {
            if($_token->expire_time >= time()) {
                //更新access_token过期时间
                $_token->expire_time = time() + $_token::TIME;
                $_token->update();
                $result = ['status' => true, 'code' => 4000, 'token' => $_token];
            } else {
                //删掉过期的access_token
                $_token->delete();
                $result = ['status' => false, 'code' => 4007];
            }
        } else {
            //找不到access_token
            $result = ['status' => false, 'code' => 4007];
        }
        return $result;
	}
}
