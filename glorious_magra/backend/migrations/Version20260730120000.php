<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260730120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create community groups, memberships, and activities tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE community_groups (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, invitation_code VARCHAR(16) NOT NULL, creator_id VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE group_memberships (id VARCHAR(36) NOT NULL, group_id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, joined_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE activities (id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, group_id VARCHAR(36) DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE activities');
        $this->addSql('DROP TABLE group_memberships');
        $this->addSql('DROP TABLE community_groups');
    }
}
