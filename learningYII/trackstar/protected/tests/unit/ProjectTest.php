<?php
class ProjectTest extends CDbTestCase {
    public $fixtures = array(
        'projects' => 'Project',
        'users' => 'User',
        'projUserAssign' => ':tbl_project_user_assignment',
        );

    public function testCreate() {
        $newProject = new Project();
        $newProjectName = 'Test Project Creation';
        $newProject->setAttributes(
            array(
                'name' => $newProjectName,
                'description' => 'This is a test for new project creation',
                )
            );
        Yii::app()->user->setId($this->users('user1')->id);
        $this->assertTrue($newProject->save());
        $retrievedProject = Project::model()->findByPk($newProject->id);
        $this->assertTrue($retrievedProject instanceof Project);
        $this->assertEquals($newProjectName, $retrievedProject->name);
        // ensure create_user_id is current login user
        $this->assertEquals(
            Yii::app()->user->id,
            $retrievedProject->create_user_id
        );
    }

    public function testRead() {
        $retrievedProject = $this->projects('project1');
        $this->assertTrue($retrievedProject instanceof Project);
        $this->assertEquals('Test Project 1', $retrievedProject->name);
    }

    public function testUpdate() {
        $project = $this->projects('project2');
        $updatedProjectName = 'Updated Test Project 1';
        $project->name = $updatedProjectName;
        $project->save(false);

        $updatedProject = Project::model()->findByPk($project->id);
        $this->assertTrue($project instanceof Project);
        $this->assertEquals($updatedProjectName, $updatedProject->name);
    }

    public function testDelete() {
        $project = $this->projects('project2');
        $savedId = $project->id;
        $this->assertTrue($project->delete());
        $deletedProject = Project::model()->findByPk($savedId);
        $this->assertEquals(NULL,  $deletedProject);
    }

    public function testGetUserOptions()
    {
        $project = $this->projects('project1');
        $options = $project->userOptions;
        $this->assertTrue(is_array($options));
        $this->assertTrue(count($options) > 0);
    }
}