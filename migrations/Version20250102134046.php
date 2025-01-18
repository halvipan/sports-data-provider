<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102134046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_data (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, time_elapsed DATETIME NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_B238BFC871F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_data ADD CONSTRAINT FK_B238BFC871F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event ADD static_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_data DROP FOREIGN KEY FK_B238BFC871F7E88B');
        $this->addSql('DROP TABLE event_data');
        $this->addSql('ALTER TABLE event DROP static_data');
    }
}
