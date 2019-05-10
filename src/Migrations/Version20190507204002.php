<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190507204002 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation ADD start_borrowing_date DATE DEFAULT NULL, ADD end_borrowing_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user CHANGE first_name first_name VARCHAR(100) DEFAULT NULL, CHANGE last_name last_name VARCHAR(100) DEFAULT NULL, CHANGE birthday birthday DATE DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(30) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user CHANGE first_name first_name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE birthday birthday VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE reservation DROP start_borrowing_date, DROP end_borrowing_date');
    }
}
