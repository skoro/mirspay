<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829145604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscribers (
          id INT AUTO_INCREMENT NOT NULL,
          uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
          hash VARCHAR(255) NOT NULL,
          notify_type VARCHAR(255) NOT NULL,
          order_status VARCHAR(255) NOT NULL,
          params JSON NOT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          UNIQUE INDEX UNIQ_2FCD16ACD17F50A6 (uuid),
          UNIQUE INDEX UNIQ_2FCD16ACD1B862B8 (hash),
          INDEX IDX_2FCD16ACF8B08F9FB88F75C9 (notify_type, order_status),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE subscribers');
    }
}
