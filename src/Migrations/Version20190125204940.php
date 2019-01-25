<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190125204940 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE note ADD COLUMN created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD COLUMN created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD COLUMN updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD COLUMN updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD COLUMN deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD COLUMN deleted_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_737A9763BAD26311');
        $this->addSql('DROP INDEX IDX_737A976326ED0855');
        $this->addSql('CREATE TEMPORARY TABLE __temp__note_tag AS SELECT note_id, tag_id FROM note_tag');
        $this->addSql('DROP TABLE note_tag');
        $this->addSql('CREATE TABLE note_tag (note_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(note_id, tag_id), CONSTRAINT FK_737A976326ED0855 FOREIGN KEY (note_id) REFERENCES note (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_737A9763BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO note_tag (note_id, tag_id) SELECT note_id, tag_id FROM __temp__note_tag');
        $this->addSql('DROP TABLE __temp__note_tag');
        $this->addSql('CREATE INDEX IDX_737A9763BAD26311 ON note_tag (tag_id)');
        $this->addSql('CREATE INDEX IDX_737A976326ED0855 ON note_tag (note_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__note AS SELECT id, title, body, background_color FROM note');
        $this->addSql('DROP TABLE note');
        $this->addSql('CREATE TABLE note (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(100) NOT NULL, body CLOB NOT NULL, background_color VARCHAR(10) NOT NULL)');
        $this->addSql('INSERT INTO note (id, title, body, background_color) SELECT id, title, body, background_color FROM __temp__note');
        $this->addSql('DROP TABLE __temp__note');
        $this->addSql('DROP INDEX IDX_737A976326ED0855');
        $this->addSql('DROP INDEX IDX_737A9763BAD26311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__note_tag AS SELECT note_id, tag_id FROM note_tag');
        $this->addSql('DROP TABLE note_tag');
        $this->addSql('CREATE TABLE note_tag (note_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(note_id, tag_id))');
        $this->addSql('INSERT INTO note_tag (note_id, tag_id) SELECT note_id, tag_id FROM __temp__note_tag');
        $this->addSql('DROP TABLE __temp__note_tag');
        $this->addSql('CREATE INDEX IDX_737A976326ED0855 ON note_tag (note_id)');
        $this->addSql('CREATE INDEX IDX_737A9763BAD26311 ON note_tag (tag_id)');
    }
}
