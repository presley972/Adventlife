<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230203171439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD privacy_policy BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD show_email BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD show_phone_number BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP privacy_policy');
        $this->addSql('ALTER TABLE "user" DROP show_email');
        $this->addSql('ALTER TABLE "user" DROP show_phone_number');
    }
}
