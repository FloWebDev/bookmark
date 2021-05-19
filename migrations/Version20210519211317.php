<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519211317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_1F1B251ED4619D1A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__item AS SELECT id, listing_id, title, url, z, note, created_at FROM item');
        $this->addSql('DROP TABLE item');
        $this->addSql('CREATE TABLE item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, listing_id INTEGER DEFAULT NULL, title VARCHAR(512) NOT NULL COLLATE BINARY, z INTEGER NOT NULL, note CLOB DEFAULT NULL COLLATE BINARY, created_at DATETIME NOT NULL, url VARCHAR(2048) DEFAULT NULL, CONSTRAINT FK_1F1B251ED4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO item (id, listing_id, title, url, z, note, created_at) SELECT id, listing_id, title, url, z, note, created_at FROM __temp__item');
        $this->addSql('DROP TABLE __temp__item');
        $this->addSql('CREATE INDEX IDX_1F1B251ED4619D1A ON item (listing_id)');
        $this->addSql('DROP INDEX IDX_CB0048D4C4663E4');
        $this->addSql('CREATE TEMPORARY TABLE __temp__listing AS SELECT id, page_id, title, z, created_at FROM listing');
        $this->addSql('DROP TABLE listing');
        $this->addSql('CREATE TABLE listing (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, page_id INTEGER DEFAULT NULL, title VARCHAR(64) NOT NULL COLLATE BINARY, z INTEGER NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_CB0048D4C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO listing (id, page_id, title, z, created_at) SELECT id, page_id, title, z, created_at FROM __temp__listing');
        $this->addSql('DROP TABLE __temp__listing');
        $this->addSql('CREATE INDEX IDX_CB0048D4C4663E4 ON listing (page_id)');
        $this->addSql('DROP INDEX IDX_140AB620A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__page AS SELECT id, user_id, title, z, created_at FROM page');
        $this->addSql('DROP TABLE page');
        $this->addSql('CREATE TABLE page (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, title VARCHAR(64) NOT NULL COLLATE BINARY, z INTEGER NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_140AB620A76ED395 FOREIGN KEY (user_id) REFERENCES "app_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO page (id, user_id, title, z, created_at) SELECT id, user_id, title, z, created_at FROM __temp__page');
        $this->addSql('DROP TABLE __temp__page');
        $this->addSql('CREATE INDEX IDX_140AB620A76ED395 ON page (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_1F1B251ED4619D1A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__item AS SELECT id, listing_id, title, url, z, note, created_at FROM item');
        $this->addSql('DROP TABLE item');
        $this->addSql('CREATE TABLE item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, listing_id INTEGER DEFAULT NULL, title VARCHAR(512) NOT NULL, z INTEGER NOT NULL, note CLOB DEFAULT NULL, created_at DATETIME NOT NULL, url VARCHAR(2048) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO item (id, listing_id, title, url, z, note, created_at) SELECT id, listing_id, title, url, z, note, created_at FROM __temp__item');
        $this->addSql('DROP TABLE __temp__item');
        $this->addSql('CREATE INDEX IDX_1F1B251ED4619D1A ON item (listing_id)');
        $this->addSql('DROP INDEX IDX_CB0048D4C4663E4');
        $this->addSql('CREATE TEMPORARY TABLE __temp__listing AS SELECT id, page_id, title, z, created_at FROM listing');
        $this->addSql('DROP TABLE listing');
        $this->addSql('CREATE TABLE listing (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, page_id INTEGER DEFAULT NULL, title VARCHAR(64) NOT NULL, z INTEGER NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO listing (id, page_id, title, z, created_at) SELECT id, page_id, title, z, created_at FROM __temp__listing');
        $this->addSql('DROP TABLE __temp__listing');
        $this->addSql('CREATE INDEX IDX_CB0048D4C4663E4 ON listing (page_id)');
        $this->addSql('DROP INDEX IDX_140AB620A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__page AS SELECT id, user_id, title, z, created_at FROM page');
        $this->addSql('DROP TABLE page');
        $this->addSql('CREATE TABLE page (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, title VARCHAR(64) NOT NULL, z INTEGER NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO page (id, user_id, title, z, created_at) SELECT id, user_id, title, z, created_at FROM __temp__page');
        $this->addSql('DROP TABLE __temp__page');
        $this->addSql('CREATE INDEX IDX_140AB620A76ED395 ON page (user_id)');
    }
}
