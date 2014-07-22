<?php
/**
 * Created by PhpStorm.
 * User: zrsh
 * Date: 14-7-21
 * Time: 下午2:29
 */
abstract class TrackStarActiveRecord extends CActiveRecord
{
    /*
     * prepare create_time + create_user_id + update_time + update_user_id
     */
    protected function beforeValidate()
    {
        if($this->isNewRecord)
        {
            $this->create_time = $this->update_time = new CDbExpression('NOW()');
            $this->create_user_id = $this->update_user_id = Yii::app()->user->id;

            //$this->associateUserToRole("owner", Yii::app()->user->id);
            //$auth = Yii::app()->authManager;
            //$bizRule='Return isset($params["project"])&& $params["project"]->isUserInRole("'.'owner'.'");';
            //$auth->assign("owner", Yii::app()->user->id, $bizRule);
        }
        else
        {
            $this->update_time = new CDbExpression('NOW()');
            $this->update_user_id = Yii::app()->user->id;
        }

        return parent::beforeValidate();
    }

    protected function afterValidate()
    {
        parent::afterValidate();
        if($this->isNewRecord)
        {
            if(($this instanceof Project))
            {
                //$this->associateUserToProject(Yii::app()->user);
            }
        }
        if(isset($this->password))
            $this->password = $this->encrypt($this->password);
    }

    public function encrypt($value)
    {
        return md5($value);
    }
}