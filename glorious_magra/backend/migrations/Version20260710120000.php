<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260710120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create measurements table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE measurements (id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, measured_at DATETIME NOT NULL, weight NUMERIC(6, 2) NOT NULL, height NUMERIC(6, 2) NOT NULL, neck NUMERIC(6, 2) NOT NULL, waist NUMERIC(6, 2) NOT NULL, hip NUMERIC(6, 2) NOT NULL, calculated_fat_percentage NUMERIC(5, 2) NOT NULL, created_at DATETIME NOT NULL, INDEX idx_measurements_user_measured_at (user_id, measured_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE measurements');
    }
}
