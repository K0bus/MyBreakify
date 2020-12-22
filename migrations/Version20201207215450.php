<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201207215450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, n1_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, logged_at DATETIME NOT NULL, is_verified TINYINT(1) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D64931B7BDD7 (n1_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_break (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, date DATE NOT NULL, time TIME NOT NULL, requested_at DATETIME NOT NULL, INDEX IDX_EEBEAB829D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_recovery (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, date DATE NOT NULL, time_from TIME NOT NULL, time_to TIME NOT NULL, status INT NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_13CE77C99D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64931B7BDD7 FOREIGN KEY (n1_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_break ADD CONSTRAINT FK_EEBEAB829D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_recovery ADD CONSTRAINT FK_13CE77C99D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64931B7BDD7');
        $this->addSql('ALTER TABLE user_break DROP FOREIGN KEY FK_EEBEAB829D86650F');
        $this->addSql('ALTER TABLE user_recovery DROP FOREIGN KEY FK_13CE77C99D86650F');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_break');
        $this->addSql('DROP TABLE user_recovery');
    }
}
