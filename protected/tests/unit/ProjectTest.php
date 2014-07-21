<?php
class ProjectTest extends CDbTestCase
{
    public $fixtures=array(
        'projects'=>'Project',
        'users' => 'User',
        'projUsrAssign' => ':tb1_project_user_assignment',
    );
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
                'createTime' => '2009-09-09 00:00:00',
                'createUser' => '1',
                'updateTime' => '2009-09-09 00:00:00',
                'updateUser' => '1',
            )
        );
        $this->assertTrue($newProject->save(false));
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
        $project = $this->projects('project2');
        $savedProjectId = $project->id;
        $this->assertTrue($project->delete());
        $deletedProject=Project::model()->findByPk($savedProjectId);
        $this->assertEquals(NULL,$deletedProject);
    }

}