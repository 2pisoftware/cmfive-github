<?php

use \Phinx\Db\Adapter\MysqlAdapter;

class GithubInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		/**
		 * REPOSITORY TABLE
		 */
		if (!$this->hasTable('github_repo')) {
			$this->table('github_repo', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('owner', 'string', ['limit' => 1024])
					->addColumn('repo', 'string', ['limit' => 1024])
					->addColumn('url', 'string', ['limit' => 1024])
					->addColumn('access_token', 'string', ['limit' => 1024])
					->addColumn('taskgroup_id', 'biginteger')
					->addColumn('issue_creator', 'string', ['limit' => 1024])
					->addColumn('task_type', 'string', ['limit' => 1024])
        				->addColumn('description', 'text')
					->addCmfiveParameters()
					->create();
		}
		
		/**
		 * USER TABLE
		 */
		if (!$this->hasTable('github_user')) {
			$this->table('github_user', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('login', 'string', ['limit' => 1024])
					->addColumn('local_user_id', 'biginteger')
					->addCmfiveParameters()
					->create();
		}
		
	}

	public function down() {
		$this->hasTable('github_repo') ? $this->dropTable('github_repo') : null;
		$this->hasTable('github_user') ? $this->dropTable('github_user') : null;
	}

}
