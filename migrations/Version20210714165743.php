<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210714165743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, game_room_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, users JSON NOT NULL, UNIQUE INDEX UNIQ_232B318CC1D50FBC (game_room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_room (id INT AUTO_INCREMENT NOT NULL, link VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, users JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE match_history (id INT AUTO_INCREMENT NOT NULL, user_win_id INT NOT NULL, user_lose_id INT NOT NULL, season_id INT DEFAULT NULL, date_of_start DATETIME NOT NULL, date_of_end DATETIME NOT NULL, match_type VARCHAR(255) NOT NULL, shots_history JSON NOT NULL, INDEX IDX_501C2F24EE3E832A (user_win_id), INDEX IDX_501C2F2462A63A29 (user_lose_id), INDEX IDX_501C2F244EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, game_room_id INT DEFAULT NULL, game_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, nick VARCHAR(255) NOT NULL, is_blocked TINYINT(1) NOT NULL, is_banned TINYINT(1) NOT NULL, INDEX IDX_98197A65C1D50FBC (game_room_id), INDEX IDX_98197A65E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_719ECBBC08AE9AD (user_source), INDEX IDX_719ECBBD96FB922 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE score (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, season_id INT DEFAULT NULL, ranks DOUBLE PRECISION NOT NULL, with_computer DOUBLE PRECISION NOT NULL, with_friend DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_3299375199E6F5DF (user_id), INDEX IDX_329937514EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, date_of_start DATE NOT NULL, date_of_end DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE winned_combination (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, combination JSON NOT NULL, INDEX IDX_D6D59BBA99E6F5DF (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC1D50FBC FOREIGN KEY (game_room_id) REFERENCES game_room (id)');
        $this->addSql('ALTER TABLE match_history ADD CONSTRAINT FK_501C2F24EE3E832A FOREIGN KEY (user_win_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE match_history ADD CONSTRAINT FK_501C2F2462A63A29 FOREIGN KEY (user_lose_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE match_history ADD CONSTRAINT FK_501C2F244EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_98197A65C1D50FBC FOREIGN KEY (game_room_id) REFERENCES game_room (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_719ECBBC08AE9AD FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_719ECBBD96FB922 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_3299375199E6F5DF FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_329937514EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE winned_combination ADD CONSTRAINT FK_D6D59BBA99E6F5DF FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_98197A65E48FD905');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CC1D50FBC');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_98197A65C1D50FBC');
        $this->addSql('ALTER TABLE match_history DROP FOREIGN KEY FK_501C2F24EE3E832A');
        $this->addSql('ALTER TABLE match_history DROP FOREIGN KEY FK_501C2F2462A63A29');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_719ECBBC08AE9AD');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_719ECBBD96FB922');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_3299375199E6F5DF');
        $this->addSql('ALTER TABLE winned_combination DROP FOREIGN KEY FK_D6D59BBA99E6F5DF');
        $this->addSql('ALTER TABLE match_history DROP FOREIGN KEY FK_501C2F244EC001D1');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_329937514EC001D1');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_room');
        $this->addSql('DROP TABLE match_history');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE score');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE winned_combination');
    }
}
