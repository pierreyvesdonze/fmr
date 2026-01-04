<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260104151823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX name ON brand');
        $this->addSql('DROP INDEX name_2 ON category');
        $this->addSql('DROP INDEX name ON category');
        $this->addSql('DROP INDEX name ON color');
        $this->addSql('DROP INDEX name ON gender_category');
        $this->addSql('ALTER TABLE gender_category ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4690E13989D9B62 ON gender_category (slug)');
        $this->addSql('DROP INDEX name ON size');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C4690E13989D9B62 ON gender_category');
        $this->addSql('ALTER TABLE gender_category DROP slug');
        $this->addSql('CREATE UNIQUE INDEX name ON gender_category (name)');
        $this->addSql('CREATE UNIQUE INDEX name ON color (name)');
        $this->addSql('CREATE UNIQUE INDEX name_2 ON category (name)');
        $this->addSql('CREATE UNIQUE INDEX name ON category (name)');
        $this->addSql('CREATE UNIQUE INDEX name ON size (name)');
        $this->addSql('CREATE UNIQUE INDEX name ON brand (name)');
    }
}
