<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210430212807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(512) NOT NULL, url VARCHAR(2048) NOT NULL, z INTEGER NOT NULL, note CLOB DEFAULT NULL)');
        $this->addSql('CREATE TABLE listing (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(64) NOT NULL, z INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE page (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(64) NOT NULL, z INTEGER NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE listing');
        $this->addSql('DROP TABLE page');
    }
}
