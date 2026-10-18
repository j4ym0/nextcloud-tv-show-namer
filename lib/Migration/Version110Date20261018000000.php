<?php

declare(strict_types=1);

namespace OCA\TvShowNamer\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version110Date20261018000000 extends SimpleMigrationStep
{
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper
    {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if ($schema->hasTable('tvshownamer')) {
            $schema->dropTable('tvshownamer');
            $output->info('Dropped table tvshownamer');
        }

        return $schema;
    }
}