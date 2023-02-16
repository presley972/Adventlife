<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214165814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE group_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE group_category (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE group_category_group (group_category_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY(group_category_id, group_id))');
        $this->addSql('CREATE INDEX IDX_1D7EC2FE37FE8223 ON group_category_group (group_category_id)');
        $this->addSql('CREATE INDEX IDX_1D7EC2FEFE54D947 ON group_category_group (group_id)');
        $this->addSql('ALTER TABLE group_category_group ADD CONSTRAINT FK_1D7EC2FE37FE8223 FOREIGN KEY (group_category_id) REFERENCES group_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_category_group ADD CONSTRAINT FK_1D7EC2FEFE54D947 FOREIGN KEY (group_id) REFERENCES "group" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE group_category_group DROP CONSTRAINT FK_1D7EC2FE37FE8223');
        $this->addSql('DROP SEQUENCE group_category_id_seq CASCADE');
        $this->addSql('DROP TABLE group_category');
        $this->addSql('DROP TABLE group_category_group');
    }
}
