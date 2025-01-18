<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105200104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE route (id INT AUTO_INCREMENT NOT NULL, route_template VARCHAR(255) NOT NULL, regex_pattern VARCHAR(255) DEFAULT NULL, variables LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE model ADD route_entity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE model ADD CONSTRAINT FK_D79572D92D67A855 FOREIGN KEY (route_entity_id) REFERENCES route (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79572D92D67A855 ON model (route_entity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE model DROP FOREIGN KEY FK_D79572D92D67A855');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP INDEX UNIQ_D79572D92D67A855 ON model');
        $this->addSql('ALTER TABLE model DROP route_entity_id');
    }
}
