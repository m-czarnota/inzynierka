<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210715152702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE match_history DROP FOREIGN KEY FK_501C2F2462A63A29');
        $this->addSql('ALTER TABLE match_history DROP FOREIGN KEY FK_501C2F24EE3E832A');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_719ECBBC08AE9AD');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_719ECBBD96FB922');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_3299375199E6F5DF');
        $this->addSql('ALTER TABLE winned_combination DROP FOREIGN KEY FK_D6D59BBA99E6F5DF');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, game_room_id INT DEFAULT NULL, game_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, nick VARCHAR(255) DEFAULT NULL, is_blocked TINYINT(1) NOT NULL, is_banned TINYINT(1) NOT NULL, roles JSON NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649290B2F37 (nick), INDEX IDX_8D93D649C1D50FBC (game_room_id), INDEX IDX_8D93D649E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C1D50FBC FOREIGN KEY (game_room_id) REFERENCES game_room (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('ALTER TABLE game CHANGE users users JSON NOT NULL');
        $this->addSql('ALTER TABLE game_room CHANGE users users JSON NOT NULL');
        $this->addSql('DROP INDEX IDX_501C2F2462A63A29 ON match_history');
        $this->addSql('DROP INDEX IDX_501C2F24EE3E832A ON match_history');
        $this->addSql('ALTER TABLE match_history ADD user_win_id INT NOT NULL, ADD user_lose_id INT NOT NULL, DROP user_win_id, DROP user_lose_id');
        $this->addSql('ALTER TABLE match_history ADD CONSTRAINT FK_501C2F24146C0EC9 FOREIGN KEY (user_win_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE match_history ADD CONSTRAINT FK_501C2F245B5FDB66 FOREIGN KEY (user_lose_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_501C2F24146C0EC9 ON match_history (user_win_id)');
        $this->addSql('CREATE INDEX IDX_501C2F245B5FDB66 ON match_history (user_lose_id)');
        $this->addSql('DROP INDEX UNIQ_3299375199E6F5DF ON score');
        $this->addSql('ALTER TABLE score CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32993751A76ED395 ON score (user_id)');
        $this->addSql('DROP INDEX IDX_D6D59BBA99E6F5DF ON winned_combination');
        $this->addSql('ALTER TABLE winned_combination CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE winned_combination ADD CONSTRAINT FK_D6D59BBAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D6D59BBAA76ED395 ON winned_combination (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE match_history DROP FOREIGN KEY FK_501C2F24146C0EC9');
        $this->addSql('ALTER TABLE match_history DROP FOREIGN KEY FK_501C2F245B5FDB66');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751A76ED395');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('ALTER TABLE winned_combination DROP FOREIGN KEY FK_D6D59BBAA76ED395');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, game_room_id INT DEFAULT NULL, game_id INT DEFAULT NULL, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nick VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, is_blocked TINYINT(1) DEFAULT \'0\' NOT NULL, is_banned TINYINT(1) DEFAULT \'0\' NOT NULL, roles JSON NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_98197A65290B2F37 (nick), INDEX IDX_98197A65E48FD905 (game_id), UNIQUE INDEX UNIQ_98197A65E7927C74 (email), INDEX IDX_98197A65C1D50FBC (game_room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_719ECBBC08AE9AD (user_source), INDEX IDX_719ECBBD96FB922 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_98197A65C1D50FBC FOREIGN KEY (game_room_id) REFERENCES game_room (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_719ECBBC08AE9AD FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_719ECBBD96FB922 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('ALTER TABLE game CHANGE users users JSON NOT NULL');
        $this->addSql('ALTER TABLE game_room CHANGE users users JSON NOT NULL');
        $this->addSql('DROP INDEX IDX_501C2F24146C0EC9 ON match_history');
        $this->addSql('DROP INDEX IDX_501C2F245B5FDB66 ON match_history');
        $this->addSql('ALTER TABLE match_history ADD user_win_id INT NOT NULL, ADD user_lose_id INT NOT NULL, DROP user_win_id, DROP user_lose_id');
        $this->addSql('ALTER TABLE match_history ADD CONSTRAINT FK_501C2F2462A63A29 FOREIGN KEY (user_lose_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE match_history ADD CONSTRAINT FK_501C2F24EE3E832A FOREIGN KEY (user_win_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_501C2F2462A63A29 ON match_history (user_lose_id)');
        $this->addSql('CREATE INDEX IDX_501C2F24EE3E832A ON match_history (user_win_id)');
        $this->addSql('DROP INDEX UNIQ_32993751A76ED395 ON score');
        $this->addSql('ALTER TABLE score CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_3299375199E6F5DF FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3299375199E6F5DF ON score (user_id)');
        $this->addSql('DROP INDEX IDX_D6D59BBAA76ED395 ON winned_combination');
        $this->addSql('ALTER TABLE winned_combination CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE winned_combination ADD CONSTRAINT FK_D6D59BBA99E6F5DF FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D6D59BBA99E6F5DF ON winned_combination (user_id)');
    }
}
