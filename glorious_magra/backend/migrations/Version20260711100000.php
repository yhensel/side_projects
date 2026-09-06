<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260711100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow male measurements without hip value';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE measurements CHANGE hip hip NUMERIC(6, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE measurements CHANGE hip hip NUMERIC(6, 2) NOT NULL');
    }
}
