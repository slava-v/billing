<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220710123525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the invoice table, change the "company" table with 2 new fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE company ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE company ALTER status SET DEFAULT \'NEW\'');
        $this->addSql('COMMENT ON COLUMN company.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company.updated_at IS \'(DC2Type:datetime_immutable)\'');


        $this->addSql('CREATE TABLE invoice
(
    id          UUID                      NOT NULL,
    debtor_id   UUID        DEFAULT NULL,
    creditor_id UUID        DEFAULT NULL,
    total       INT                       NOT NULL,
    status      VARCHAR(20) DEFAULT \'NEW\' NOT NULL,
    paid_at     TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    source      VARCHAR(255)              NOT NULL,
    PRIMARY KEY (id)
)');
        $this->addSql('CREATE INDEX IDX_90651744B043EC6B ON invoice (debtor_id)');
        $this->addSql('CREATE INDEX IDX_90651744DF91AC92 ON invoice (creditor_id)');
        $this->addSql('COMMENT ON COLUMN invoice.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice.debtor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice.creditor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice.paid_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoice.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoice.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744B043EC6B FOREIGN KEY (debtor_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744DF91AC92 FOREIGN KEY (creditor_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744B043EC6B');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744DF91AC92');
        $this->addSql('DROP TABLE invoice');
    }
}
