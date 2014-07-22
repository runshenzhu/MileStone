<?php

class ProjectController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'adduser'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


    public function actionAdduser()
    {
        //$id = $_GET['id'];
        if(isset($_GET['id']))
            $id = $_GET['id'];
        else if (isset($_POST['ID']))
            $id = $_POST['id'];

        $form = new ProjectUserForm();

        $project = $this->loadModel($id);

        if(!Yii::app()->user->checkAccess("createUser", array("project"=>$project)))
        {
            throw new CHttpException(403, 'not authorized');
        }

        if(isset($_POST['ProjectUserForm']))
        {
            $form->attributes=$_POST['ProjectUserForm'];
            $form->project = $project;
            if($form->validate())
            {
                Yii::app()->user->setFlash("success", $form->username." has been added to the project");
                $form=new ProjectUserForm();
            }
        }

        $users = User::model()->findAll();
        $usernames = array();
        foreach($users as $user)
        {
            $usernames[] = $user->username;
        }

        $form->project = $project;
        $this->render('adduser', array('model'=>$form, 'usernames'=>$usernames));
    }
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        $id = $_GET['id'];
        $issueDataProvider = new CActiveDataProvider('Issue', array(
            'criteria' => array(
                'condition' => 'project_id=:projectId',
                'params' => array(':projectId' => $this->loadModel($id)->id),
            ),
            'pagination' => array('pageSize' => 4),
        ));
		$this->render('view',array(
			'model'=>$this->loadModel($id),
            'issueDataProvider' => $issueDataProvider,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Project;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Project']))
		{
			$model->attributes=$_POST['Project'];
			if($model->save())
            {
                $project = $this->loadModel($model->id);
                $project->associateUserToProject(Yii::app()->user);
                $project->associateUserToRole("owner", $project->create_user_id);
                //$auth = Yii::app()->authManager;
                //$bizRule='Return isset($params["project"])&& $params["project"]->isUserInRole("'.'owner'.'");';
                //$auth->assign("owner", $project->create_user_id, $bizRule);
				$this->redirect(array('view','id'=>$model->id));
            }
            //$this->project->associateUserToRole($this->role, $model->project->user_id);
            //$auth = Yii::app()->authManager;
            //$bizRule='Return isset($params["project"])&& $params["project"]->isUserInRole("'.'owner'.'");';
            //$auth->assign("owner", $model->project->user_id, $bizRule);
            //echo $this->role;
            //echo $model->project->user_id;
		}


		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Project']))
		{
			$model->attributes=$_POST['Project'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        $project=loadModel($id);
        if(!Yii::app()->user->checkAccess("deleteProject", array("project"=>$project)))
        {
            throw new CHttpException(403, 'not authorized');
        }
        $project->deleteRelatedUser();
		$project->delete();
        $this->redirect(array('index'));
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		//if(!isset($_GET['ajax']))
			//$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Project');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Project('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Project']))
			$model->attributes=$_GET['Project'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Project the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Project::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Project $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}


class ProjectUserForm extends CFormModel
{
    public $username;
    public $role;
    public $project;

    public function rules()
    {
        return array(
            array('username, role', 'required'),
            array('username', 'exist', 'className'=>'User'),
            array('username', 'verify'),
        );
    }

    public function verify($attribute, $params)
    {
        //echo "here-2";
        if(!$this->hasErrors())
        {
            //echo "here-1";
            $user = User::model()->findByAttributes(array('username'=>$this->username));
            //echo "here0";
            if($this->project->isUserInProject($user))
            {

                $this->addError('username', 'This user has already been added to the project');
            }
            else
            {
                //echo "here1";
                $this->project->associateUserToProject($user);
                //echo "here2";
                $this->project->associateUserToRole($this->role, $user->id);
                $auth = Yii::app()->authManager;
                $bizRule='Return isset($params["project"])&& $params["project"]->isUserInRole("'.$this->role.'");';
                $auth->assign($this->role, $user->id, $bizRule);
            }
        }
    }
}
