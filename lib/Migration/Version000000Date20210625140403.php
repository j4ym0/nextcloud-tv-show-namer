<?php

declare(strict_types=1);

namespace OCA\TVShowNamer\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20210625140403 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('tvshownamer')) {
			$table = $schema->createTable('tvshownamer');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('path', 'string', [
				'notnull' => true,
				'length' => 256,
			]);
			$table->addColumn('orig_name', 'string', [
				'notnull' => false,
				'length' => 256,
			]);
			$table->addColumn('name', 'string', [
				'notnull' => false,
				'length' => 256,
			]);
			$table->addColumn('file_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('m_id', 'integer', [
				'notnull' => false,
			]);
			$table->addColumn('e_id', 'integer', [
				'notnull' => false,
			]);
			$table->addColumn('poster', 'string', [
				'notnull' => false,
				'length' => 256,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['file_id'], 'tvshownamer_file_id_index');
		}
		return $schema;
	}
}
