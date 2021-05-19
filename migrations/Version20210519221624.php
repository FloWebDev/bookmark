<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519221624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_user ADD COLUMN wallpaper VARCHAR(64) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_1F1B251ED4619D1A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__item AS SELECT id, listing_id, title, z, note, created_at, url FROM item');
        $this->addSql('DROP TABLE item');
        $this->addSql('CREATE TABLE item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, listing_id INTEGER DEFAULT NULL, title VARCHAR(512) NOT NULL COLLATE BINARY, z INTEGER NOT NULL, note CLOB DEFAULT NULL COLLATE BINARY, created_at DATETIME NOT NULL, url VARCHAR(2048) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_1F1B251ED4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO item (id, listing_id, title, z, note, created_at, url) SELECT id, listing_id, title, z, note, created_at, url FROM __temp__item');
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
        $this->addSql('DROP INDEX UNIQ_88BDF3E9F85E0677');
        $this->addSql('DROP INDEX UNIQ_88BDF3E9E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__app_user AS SELECT id, username, password, email, role, created_at FROM "app_user"');
        $this->addSql('DROP TABLE "app_user"');
        $this->addSql('CREATE TABLE "app_user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(256) NOT NULL, role VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO "app_user" (id, username, password, email, role, created_at) SELECT id, username, password, email, role, created_at FROM __temp__app_user');
        $this->addSql('DROP TABLE __temp__app_user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9F85E0677 ON "app_user" (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON "app_user" (email)');
        $this->addSql('DROP INDEX IDX_1F1B251ED4619D1A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__item AS SELECT id, listing_id, title, url, z, note, created_at FROM item');
        $this->addSql('DROP TABLE item');
        $this->addSql('CREATE TABLE item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, listing_id INTEGER DEFAULT NULL, title VARCHAR(512) NOT NULL, url VARCHAR(2048) DEFAULT NULL, z INTEGER NOT NULL, note CLOB DEFAULT NULL, created_at DATETIME NOT NULL)');
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
