<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220708194136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the company entity table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company (id UUID NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, iban VARCHAR(34) NOT NULL, balance INT NOT NULL, debtor_limit INT NOT NULL, access_token VARCHAR(100) NOT NULL, status VARCHAR(15) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN company.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE company');
    }
}
