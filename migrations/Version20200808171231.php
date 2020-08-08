<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 * @codeCoverageIgnore
 */
final class Version20200808171231 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, created_at DATETIME NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, is_done TINYINT(1) NOT NULL, INDEX IDX_527EDB25F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(60) NOT NULL, role JSON DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('INSERT INTO `user` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, \'Exal\', \'$argon2id$v=19$m=65536,t=4,p=1$MUguc0Vhb053THZWOTAzbg$wY5Cr5pEcMHlAWG9Ja1k0HtVSkckYHNTeeITTHzvA7o\', \'exal456@email.com\', \'[\"ROLE_ADMIN\"]\'),
(2, \'Nico\', \'$argon2id$v=19$m=65536,t=4,p=1$LlFOYWlMV29DNGVVWVNZVg$McN6gGSTf9zfVxKCPa39J/Mb7l/MnzX6wn/C4GhooS0\', \'nico123@email.com\', \'[\"ROLE_USER\"]\'),
(3, \'anonymous\', \'123\', \'123\', NULL),
(4, \'Xav\', \'$argon2id$v=19$m=65536,t=4,p=1$R2Qzbkh1UGx3Q2JCL3BvOA$urX5dlDLXykNoogjnh8uf+BNUXpFb8gI1RNNsMcdigI\', \'xav123@email.com\', \'[\"ROLE_USER\"]\');');
        $this->addSql('INSERT INTO `task` (`id`, `author_id`, `created_at`, `title`, `content`, `is_done`) VALUES
(2, 3, \'2020-03-02 18:09:59\', \'Sample task 1\', \'This task is a fixture.\', 0),
(4, 1, \'2020-03-17 17:00:44\', \'Task done 1\', \'This task is a fixture.\', 1),
(5, 4, \'2020-03-17 17:32:16\', \'Task done 2\', \'This task is a fixture.\', 1),
(6, 4, \'2020-03-18 13:56:31\', \'Task done 3\', \'This task is a fixture.\', 1),
(7, 1, \'2020-08-08 18:29:45\', \'Sample task 2\', \'This task is a fixture.\', 0),
(8, 1, \'2020-08-08 18:29:56\', \'Sample task 3\', \'This task is a fixture.\', 0),
(9, 4, \'2020-08-08 18:35:21\', \'Sample task 4\', \'This task is a fixture.\', 0),
(10, 2, \'2020-08-08 18:36:04\', \'Sample task 5\', \'This task is a fixture.\', 0),
(11, 2, \'2020-08-08 18:36:28\', \'Task done 4\', \'This task is a fixture.\', 1);
');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25F675F31B');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE user');
    }
}
