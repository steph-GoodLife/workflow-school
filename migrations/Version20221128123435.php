<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128123435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD status TEXT NOT NULL');
        $this->addSql('ALTER TABLE message DROP created_at');
        $this->addSql('ALTER TABLE message RENAME COLUMN message TO texte');
        $this->addSql('COMMENT ON COLUMN message.status IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307FFB88E14F ON message (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FFB88E14F');
        $this->addSql('DROP INDEX IDX_B6BD307FFB88E14F');
        $this->addSql('ALTER TABLE message ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE message DROP utilisateur_id');
        $this->addSql('ALTER TABLE message DROP status');
        $this->addSql('ALTER TABLE message RENAME COLUMN texte TO message');
        $this->addSql('COMMENT ON COLUMN message.created_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
