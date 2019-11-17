<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191112160942 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_2FB3D0EEF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, author_id, title, date, slug, content, date_end FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, date DATETIME NOT NULL, slug VARCHAR(255) DEFAULT NULL COLLATE BINARY, content CLOB NOT NULL COLLATE BINARY, date_end DATETIME NOT NULL, CONSTRAINT FK_2FB3D0EEF675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project (id, author_id, title, date, slug, content, date_end) SELECT id, author_id, title, date, slug, content, date_end FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEF675F31B ON project (author_id)');
        $this->addSql('DROP INDEX IDX_D6680DC13DA5256D');
        $this->addSql('DROP INDEX IDX_D6680DC1166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_image AS SELECT project_id, image_id FROM project_image');
        $this->addSql('DROP TABLE project_image');
        $this->addSql('CREATE TABLE project_image (project_id INTEGER NOT NULL, image_id INTEGER NOT NULL, PRIMARY KEY(project_id, image_id), CONSTRAINT FK_D6680DC1166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D6680DC13DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project_image (project_id, image_id) SELECT project_id, image_id FROM __temp__project_image');
        $this->addSql('DROP TABLE __temp__project_image');
        $this->addSql('CREATE INDEX IDX_D6680DC13DA5256D ON project_image (image_id)');
        $this->addSql('CREATE INDEX IDX_D6680DC1166D1F9C ON project_image (project_id)');
        $this->addSql('DROP INDEX IDX_527EDB25F675F31B');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, author_id, project_id, content, place, done FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, project_id INTEGER NOT NULL, content CLOB NOT NULL COLLATE BINARY, place VARCHAR(255) DEFAULT NULL COLLATE BINARY, done BOOLEAN NOT NULL, date_end_task DATETIME NOT NULL, CONSTRAINT FK_527EDB25F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, author_id, project_id, content, place, done) SELECT id, author_id, project_id, content, place, done FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25F675F31B ON task (author_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('DROP INDEX IDX_D7B9FD9A292E8AE2');
        $this->addSql('DROP INDEX IDX_D7B9FD9AA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_profile_picture AS SELECT user_id, profile_picture_id FROM user_profile_picture');
        $this->addSql('DROP TABLE user_profile_picture');
        $this->addSql('CREATE TABLE user_profile_picture (user_id INTEGER NOT NULL, profile_picture_id INTEGER NOT NULL, PRIMARY KEY(user_id, profile_picture_id), CONSTRAINT FK_D7B9FD9AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D7B9FD9A292E8AE2 FOREIGN KEY (profile_picture_id) REFERENCES profile_picture (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_profile_picture (user_id, profile_picture_id) SELECT user_id, profile_picture_id FROM __temp__user_profile_picture');
        $this->addSql('DROP TABLE __temp__user_profile_picture');
        $this->addSql('CREATE INDEX IDX_D7B9FD9A292E8AE2 ON user_profile_picture (profile_picture_id)');
        $this->addSql('CREATE INDEX IDX_D7B9FD9AA76ED395 ON user_profile_picture (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_2FB3D0EEF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, author_id, title, date, date_end, content, slug FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, date_end DATETIME NOT NULL, content CLOB NOT NULL, slug VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO project (id, author_id, title, date, date_end, content, slug) SELECT id, author_id, title, date, date_end, content, slug FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEF675F31B ON project (author_id)');
        $this->addSql('DROP INDEX IDX_D6680DC1166D1F9C');
        $this->addSql('DROP INDEX IDX_D6680DC13DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_image AS SELECT project_id, image_id FROM project_image');
        $this->addSql('DROP TABLE project_image');
        $this->addSql('CREATE TABLE project_image (project_id INTEGER NOT NULL, image_id INTEGER NOT NULL, PRIMARY KEY(project_id, image_id))');
        $this->addSql('INSERT INTO project_image (project_id, image_id) SELECT project_id, image_id FROM __temp__project_image');
        $this->addSql('DROP TABLE __temp__project_image');
        $this->addSql('CREATE INDEX IDX_D6680DC1166D1F9C ON project_image (project_id)');
        $this->addSql('CREATE INDEX IDX_D6680DC13DA5256D ON project_image (image_id)');
        $this->addSql('DROP INDEX IDX_527EDB25F675F31B');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, author_id, project_id, content, place, done FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, project_id INTEGER NOT NULL, content CLOB NOT NULL, place VARCHAR(255) DEFAULT NULL, done BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO task (id, author_id, project_id, content, place, done) SELECT id, author_id, project_id, content, place, done FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25F675F31B ON task (author_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('DROP INDEX IDX_D7B9FD9AA76ED395');
        $this->addSql('DROP INDEX IDX_D7B9FD9A292E8AE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_profile_picture AS SELECT user_id, profile_picture_id FROM user_profile_picture');
        $this->addSql('DROP TABLE user_profile_picture');
        $this->addSql('CREATE TABLE user_profile_picture (user_id INTEGER NOT NULL, profile_picture_id INTEGER NOT NULL, PRIMARY KEY(user_id, profile_picture_id))');
        $this->addSql('INSERT INTO user_profile_picture (user_id, profile_picture_id) SELECT user_id, profile_picture_id FROM __temp__user_profile_picture');
        $this->addSql('DROP TABLE __temp__user_profile_picture');
        $this->addSql('CREATE INDEX IDX_D7B9FD9AA76ED395 ON user_profile_picture (user_id)');
        $this->addSql('CREATE INDEX IDX_D7B9FD9A292E8AE2 ON user_profile_picture (profile_picture_id)');
    }
}
