<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240824082405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_product (
          id INT AUTO_INCREMENT NOT NULL,
          order_id INT NOT NULL,
          sku VARCHAR(255) NOT NULL,
          quantity INT NOT NULL,
          price INT NOT NULL,
          name VARCHAR(255) NOT NULL,
          INDEX IDX_2530ADE68D9F6D38 (order_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (
          id INT AUTO_INCREMENT NOT NULL,
          external_order_id VARCHAR(255) NOT NULL,
          uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
          payment_gateway VARCHAR(16) NOT NULL,
          amount INT NOT NULL,
          currency VARCHAR(8) NOT NULL,
          description VARCHAR(255) DEFAULT NULL,
          return_url LONGTEXT DEFAULT NULL,
          STATUS VARCHAR(16) NOT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          UNIQUE INDEX UNIQ_E52FFDEED17F50A6 (uuid),
          UNIQUE INDEX external_order_payment (
            external_order_id, payment_gateway
          ),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_processing (
          id INT AUTO_INCREMENT NOT NULL,
          order_id INT NOT NULL,
          request_name VARCHAR(255) DEFAULT NULL,
          request_params JSON DEFAULT NULL,
          response_name VARCHAR(255) DEFAULT NULL,
          response_data JSON DEFAULT NULL,
          response_success TINYINT(1) NOT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          INDEX IDX_BD9A2AFE8D9F6D38 (order_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (
          id BIGINT AUTO_INCREMENT NOT NULL,
          body LONGTEXT NOT NULL,
          headers LONGTEXT NOT NULL,
          queue_name VARCHAR(190) NOT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          INDEX IDX_75EA56E0FB7336F0 (queue_name),
          INDEX IDX_75EA56E0E3BD61CE (available_at),
          INDEX IDX_75EA56E016BA31DB (delivered_at),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE
          order_product
        ADD
          CONSTRAINT FK_2530ADE68D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE
          payment_processing
        ADD
          CONSTRAINT FK_BD9A2AFE8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE68D9F6D38');
        $this->addSql('ALTER TABLE payment_processing DROP FOREIGN KEY FK_BD9A2AFE8D9F6D38');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE payment_processing');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
