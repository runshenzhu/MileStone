<?php
class ProjectTest extends CDbTestCase
{
    public $fixtures=array(
        'projects'=>'Project',
        'users' => 'User',
        'projUsrAssign' => ':tb1_project_user_assignment',
        'projUserRole' => ':tb1_project_user_role',
        'authAssign'=>':AuthAssignment',
    );

    public function testGetUserRoleOptions()
    {
        $options = Project::getUserRoleOptions();
        //$this->assertEquals(count($options),3);
        $this->assertTrue(isset($options['reader']));
        $this->assertTrue(isset($options['member']));
        $this->assertTrue(isset($options['owner']));
    }

    public function testAssociateUserToProject()
    {
        $user = $this->users('user2');
        $project = $this->projects("project2");
        $result = $project->associateUserToProject($user);
        $this->assertEquals(1,$result);
    }

    public function testIsUserInProject()
    {
        $project = $this->projects("project1");
        $user = $this->users("user1");
        $this->assertTrue($project->IsUserInProject($user)==1);
    }

    public function testUserAccessBasedOnProjectRole()
    {
        $row1 = $this->projUserRole['row1'];
        Yii::app()->user->setId($row1['user_id']);
        $project = Project::model()->findByPk($row1['project_id']);
        $auth = Yii::app()->authManager;
        $bizRule='return isset($params["project"]) && $params["project"]->isUserInRole("member");';
        $auth->assign('member',$row1['user_id'], $bizRule);
        $params=array('project'=>$project);
        $this->assertTrue(Yii::app()->user->checkAccess('updateIssue', $params));
        $this->assertTrue(Yii::app()->user->checkAccess('readIssue',$params));
        $this->assertFalse(Yii::app()->user->checkAccess('updateProject',$params));
    }

    public function testIsInRole()
    {
        $row1 = $this->projUserRole['row1'];
        Yii::app()->user->setId($row1['user_id']);
        $project = Project::model()->findByPk($row1['project_id']);
        $this->assertTrue($project->isUserInRole('member'));
    }
    public  function testUserRoleAssignment()
    {
        $project = $this->projects('project1');
        $user = $this->users('user1');
        $this->assertEquals(1, $project->associateUserToRole('owner', $user->id));
        $this->assertEquals(1, $project->removeUserFromRole('owner', $user->id));
    }

    public function testGetUserOpts()
    {
        $project = $this->projects('project1');
        $opt = $project->userOpts;
        $this->assertTrue(is_array($opt));
        $this->assertTrue(count($opt) > 0);
    }

    public function testCreate()
    {
        //CREATE a new Project
        $newProject=new Project;
        $newProjectName = 'Test Project Creation';
        $newProject->setAttributes(
            array(
                'name' => $newProjectName,
                'description' => 'This is a test for new project creation',

                //'createTime' => '2009-09-09 00:00:00',
                //'createUser' => '1',
                //'updateTime' => '2009-09-09 00:00:00',
                //'updateUser' => '1',
            )
        );
        Yii::app()->user->setId($this->users('user1')->id);

        $this->assertTrue($newProject->save());

        $retrievedProject = Project::model()->findByPk($newProject->id);
        $this->assertTrue($retrievedProject instanceof Project);
        $this->assertEquals($newProjectName, $retrievedProject->name);
        $this->assertEquals(Yii::app()->user->id, $retrievedProject->create_user_id);
    }

    public function testRead()
    {
        //read
        $retrivedProject = $this->projects('project1');
        $this->assertTrue($retrivedProject instanceof Project);
        $this->assertEquals($retrivedProject->name, 'test project1');
    }

    public function testUpdate()
    {
        $project = $this->projects('project2');
        $updatedProjectName = 'Updated Test Project 2';
        $project->name = $updatedProjectName;
        $this->assertTrue($project->save(false));
        //read back the record again to ensure the update worked
        $updatedProject=Project::model()->findByPk($project->id);
        $this->assertTrue($updatedProject instanceof Project);
        $this->assertEquals($updatedProjectName,$updatedProject->name);
    }

    public function testDelete()
    {
        $project = $this->projects('project1');
        $savedProjectId = $project->id;
        $this->assertTrue($project->delete());
        $deletedProject=Project::model()->findByPk($savedProjectId);
        $this->assertEquals(NULL,$deletedProject);
    }


}