<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260904120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Prefix GloriousMagra tables for shared database use';
    }

    public function up(Schema $schema): void
    {
        $this->renameIfPresent('users', 'glorious_magra_users');
        $this->renameIfPresent('measurements', 'glorious_magra_measurements');
        $this->renameIfPresent('community_groups', 'glorious_magra_community_groups');
        $this->renameIfPresent('group_memberships', 'glorious_magra_group_memberships');
        $this->renameIfPresent('activities', 'glorious_magra_activities');
        $this->renameIfPresent('refresh_tokens', 'glorious_magra_refresh_tokens');
    }

    public function down(Schema $schema): void
    {
        $this->renameIfPresent('glorious_magra_refresh_tokens', 'refresh_tokens');
        $this->renameIfPresent('glorious_magra_activities', 'activities');
        $this->renameIfPresent('glorious_magra_group_memberships', 'group_memberships');
        $this->renameIfPresent('glorious_magra_community_groups', 'community_groups');
        $this->renameIfPresent('glorious_magra_measurements', 'measurements');
        $this->renameIfPresent('glorious_magra_users', 'users');
    }

    private function renameIfPresent(string $from, string $to): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist([$from]) && !$schemaManager->tablesExist([$to])) {
            $this->addSql(sprintf('RENAME TABLE `%s` TO `%s`', $from, $to));
        }
    }
}
