<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190117233352 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE tag (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B7835E237E06 ON tag (name)');
        $this->addSql('CREATE TABLE note (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(100) NOT NULL, body CLOB NOT NULL, background_color VARCHAR(10) NOT NULL)');
        $this->addSql('CREATE TABLE note_tag (note_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(note_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_737A976326ED0855 ON note_tag (note_id)');
        $this->addSql('CREATE INDEX IDX_737A9763BAD26311 ON note_tag (tag_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE note_tag');
    }
}
