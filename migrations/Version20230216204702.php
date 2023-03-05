<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216204702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE prayer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE prayer (id INT NOT NULL, owner_id INT DEFAULT NULL, groupe_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, modified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, description TEXT NOT NULL, security BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_47C953867E3C61F9 ON prayer (owner_id)');
        $this->addSql('CREATE INDEX IDX_47C953867A45358C ON prayer (groupe_id)');
        $this->addSql('COMMENT ON COLUMN prayer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN prayer.modified_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE prayer ADD CONSTRAINT FK_47C953867E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prayer ADD CONSTRAINT FK_47C953867A45358C FOREIGN KEY (groupe_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE prayer_id_seq CASCADE');
        $this->addSql('DROP TABLE prayer');
    }
}
