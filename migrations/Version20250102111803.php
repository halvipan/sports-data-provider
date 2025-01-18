<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102111803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, static_data_keys LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', static_data_values LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', evolving_data_keys LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', evolving_data_values LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE model (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, root_property_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, route VARCHAR(255) NOT NULL, start_time DATETIME DEFAULT NULL, INDEX IDX_D79572D971F7E88B (event_id), UNIQUE INDEX UNIQ_D79572D9733511E1 (root_property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, property_key VARCHAR(255) NOT NULL, simple_value VARCHAR(255) DEFAULT NULL, route_value VARCHAR(255) DEFAULT NULL, event_data_key_value VARCHAR(255) DEFAULT NULL, INDEX IDX_8BF21CDE727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE model ADD CONSTRAINT FK_D79572D971F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE model ADD CONSTRAINT FK_D79572D9733511E1 FOREIGN KEY (root_property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE727ACA70 FOREIGN KEY (parent_id) REFERENCES property (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE model DROP FOREIGN KEY FK_D79572D971F7E88B');
        $this->addSql('ALTER TABLE model DROP FOREIGN KEY FK_D79572D9733511E1');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE727ACA70');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
