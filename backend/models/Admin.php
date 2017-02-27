<?php
/**
 * Created by PhpStorm.
 * User: home
 * Date: 2016/11/26
 * Time: 22:19
 */

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

class Admin extends ActiveRecord implements \yii\web\IdentityInterface
{
    public $auth_key;


    public static function tableName()
    {
        return '{{%teacher}}';
    }
    
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($phone)
    {
        return static::findOne(['phone' => $phone]);
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
        return $this->auth_key;
    }
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
        return $this->auth_key == $authKey;
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
        return static::findOne(['accessToken'=>$token]);
    }

}