<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220708214408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the email and phone number';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE company ADD phone_number VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company DROP email');
        $this->addSql('ALTER TABLE company DROP phone_number');
    }
}
