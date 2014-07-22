<?php

/**
 * This is the model class for table "tb1_project".
 *
 * The followings are the available columns in table 'tb1_project':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $create_time
 * @property integer $create_user_id
 * @property string $update_time
 * @property integer $update_user_id
 */
class Project extends TrackStarActiveRecord
{
    public static function getUserRoleOptions()
    {
        return CHtml::listData(Yii::app()->authManager->getRoles(),'name','name');
    }

    public function deleteRelatedUser()
    {
        $sql = "DELETE FROM tb1_project_user_role WHERE project_id=:projectId";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":projectId", $this->id, PDO::PARAM_INT);
        $command->execute();
        $sql = "DELETE FROM tb1_project_user_assignment WHERE project_id=:projectId";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":projectId", $this->id, PDO::PARAM_INT);
        $command->execute();
    }

    public function associateUserToProject($user)
    {
        $sql="INSERT INTO tb1_project_user_assignment (project_id, user_id) VALUE (:projectId, :userId)";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":projectId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", $user->id, PDO::PARAM_INT);
        return $command->execute();
    }

    public function isUserInProject($user)
    {
        $sql="SELECT user_id from tb1_project_user_assignment WHERE project_id=:projectId AND user_id=:userId";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":projectId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", $user->id, PDO::PARAM_INT);
        return $command->execute()==1;
    }

    public function isUserInRole($role)
    {
        $sql = "SELECT role from tb1_project_user_role WHERE project_id=:projectId AND user_id=:userId AND role=:role";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":projectId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", Yii::app()->user->getId(), PDO::PARAM_INT);
        $command->bindValue(":role", $role, PDO::PARAM_STR);
        return $command->execute()==1? true: false;
    }
    public function associateUserToRole($role, $userId)
    {
        $sql = "INSERT INTO tb1_project_user_role (project_id, user_id, role) VALUE (:projectId, :userId, :role)";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":projectId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", $userId, PDO::PARAM_INT);
        $command->bindValue(":role", $role, PDO::PARAM_STR);
        return $command->execute();
    }

    public function removeUserFromRole($role, $userId)
    {
        $sql = "DELETE FROM tb1_project_user_role WHERE project_id=:projectId and user_id=:userId and role=:role";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":projectId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", $userId, PDO::PARAM_INT);
        $command->bindValue(":role", $role, PDO::PARAM_STR);
        return $command->execute();
    }
    public function getUserOpts()
    {
        //$usersArrary = array();
        $usersArrary = CHtml::listData($this->users, 'id', 'username');
        return $usersArrary;
    }
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tb1_project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'length', 'max'=>128),
			array('description, create_time, update_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, create_time, create_user_id, update_time, update_user_id', 'safe', 'on'=>'search'),
            array('name, description', 'required'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'issue' => array(self::HAS_MANY, 'Issue', 'project_id'),
            'users' => array(self::MANY_MANY, 'User', 'tb1_project_user_assignment(project_id, user_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
			'create_time' => 'Create Time',
			'create_user_id' => 'Create User',
			'update_time' => 'Update Time',
			'update_user_id' => 'Update User',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('create_user_id',$this->create_user_id);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('update_user_id',$this->update_user_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Project the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
