<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216211600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prayer ADD place_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prayer ADD CONSTRAINT FK_47C95386DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_47C95386DA6A219 ON prayer (place_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE prayer DROP CONSTRAINT FK_47C95386DA6A219');
        $this->addSql('DROP INDEX IDX_47C95386DA6A219');
        $this->addSql('ALTER TABLE prayer DROP place_id');
    }
}
