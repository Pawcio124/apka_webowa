<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191031184950 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE profile_picture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL)');
        $this->addSql('DROP INDEX IDX_2FB3D0EEF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, author_id, title, date, slug, content FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, date DATETIME NOT NULL, slug VARCHAR(255) DEFAULT NULL COLLATE BINARY, content CLOB NOT NULL COLLATE BINARY, CONSTRAINT FK_2FB3D0EEF675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project (id, author_id, title, date, slug, content) SELECT id, author_id, title, date, slug, content FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEF675F31B ON project (author_id)');
        $this->addSql('DROP INDEX IDX_527EDB25F675F31B');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, author_id, project_id, content, place FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, project_id INTEGER NOT NULL, content CLOB NOT NULL COLLATE BINARY, place VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_527EDB25F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, author_id, project_id, content, place) SELECT id, author_id, project_id, content, place FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25F675F31B ON task (author_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, email, roles, password_change_date, enabled, confirmation_token FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL COLLATE BINARY, password VARCHAR(255) NOT NULL COLLATE BINARY, email VARCHAR(255) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:simple_array)
        , password_change_date INTEGER DEFAULT NULL, confirmation_token VARCHAR(40) DEFAULT NULL COLLATE BINARY, enabled BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO user (id, username, password, email, roles, password_change_date, enabled, confirmation_token) SELECT id, username, password, email, roles, password_change_date, enabled, confirmation_token FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE profile_picture');
        $this->addSql('DROP INDEX IDX_2FB3D0EEF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, author_id, title, date, content, slug FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, content CLOB NOT NULL, slug VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO project (id, author_id, title, date, content, slug) SELECT id, author_id, title, date, content, slug FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEF675F31B ON project (author_id)');
        $this->addSql('DROP INDEX IDX_527EDB25F675F31B');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, author_id, project_id, content, place FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, project_id INTEGER NOT NULL, content CLOB NOT NULL, place VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO task (id, author_id, project_id, content, place) SELECT id, author_id, project_id, content, place FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25F675F31B ON task (author_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, email, roles, password_change_date, enabled, confirmation_token FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:simple_array)
        , password_change_date INTEGER DEFAULT NULL, confirmation_token VARCHAR(40) DEFAULT NULL, enabled BOOLEAN DEFAULT \'FALSE\' NOT NULL)');
        $this->addSql('INSERT INTO user (id, username, password, email, roles, password_change_date, enabled, confirmation_token) SELECT id, username, password, email, roles, password_change_date, enabled, confirmation_token FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }
}
