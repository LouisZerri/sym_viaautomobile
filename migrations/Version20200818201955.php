<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200818201955 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site ADD users_id INT NOT NULL');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E467B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_694309E467B3B43D ON site (users_id)');
        $this->addSql('ALTER TABLE user DROP site_rattachement');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E467B3B43D');
        $this->addSql('DROP INDEX UNIQ_694309E467B3B43D ON site');
        $this->addSql('ALTER TABLE site DROP users_id');
        $this->addSql('ALTER TABLE user ADD site_rattachement VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
