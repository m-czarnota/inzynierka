<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210715150311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD is_verified TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98197A65E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98197A65290B2F37 ON user (nick)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_98197A65E7927C74 ON user');
        $this->addSql('DROP INDEX UNIQ_98197A65290B2F37 ON user');
        $this->addSql('ALTER TABLE user DROP roles, DROP is_verified');
    }
}
