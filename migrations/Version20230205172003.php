<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230205172003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD user_profil_picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image DROP user_profil_picture');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F2D9C5D6A FOREIGN KEY (user_profil_picture_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045F2D9C5D6A ON image (user_profil_picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE image DROP CONSTRAINT FK_C53D045F2D9C5D6A');
        $this->addSql('DROP INDEX UNIQ_C53D045F2D9C5D6A');
        $this->addSql('ALTER TABLE image ADD user_profil_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE image DROP user_profil_picture_id');
    }
}
