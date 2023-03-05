<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216233237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prayer_user (prayer_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(prayer_id, user_id))');
        $this->addSql('CREATE INDEX IDX_16EE3721F0D7D6C6 ON prayer_user (prayer_id)');
        $this->addSql('CREATE INDEX IDX_16EE3721A76ED395 ON prayer_user (user_id)');
        $this->addSql('ALTER TABLE prayer_user ADD CONSTRAINT FK_16EE3721F0D7D6C6 FOREIGN KEY (prayer_id) REFERENCES prayer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prayer_user ADD CONSTRAINT FK_16EE3721A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE prayer_user');
    }
}
