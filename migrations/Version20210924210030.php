<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210924210030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE matchmaking_storage (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_game_info JSON NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2C9385ECA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE matchmaking_storage ADD CONSTRAINT FK_2C9385ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD kind_of_game INT DEFAULT NULL, ADD game_state INT NOT NULL, ADD game_info JSON NOT NULL, CHANGE modified_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE game_room ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE matchmaking_storage');
        $this->addSql('ALTER TABLE game DROP kind_of_game, DROP game_state, DROP game_info, CHANGE updated_at modified_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE game_room DROP created_at, DROP updated_at');
    }
}
