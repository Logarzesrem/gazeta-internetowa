<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial database schema migration.
 */
final class Version20250701000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial database schema with all tables';
    }

    public function up(Schema $schema): void
    {
        // Create admin_user table
        $this->addSql(<<<'SQL'
            CREATE TABLE admin_user (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                roles JSON NOT NULL,
                UNIQUE INDEX UNIQ_AD8A54A9E7927C74 (email),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Create user table
        $this->addSql(<<<'SQL'
            CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(255) NOT NULL,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                roles JSON NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                last_login_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                is_active TINYINT(1) NOT NULL,
                avatar VARCHAR(255) DEFAULT NULL,
                bio LONGTEXT DEFAULT NULL,
                discr VARCHAR(255) DEFAULT 'user' NOT NULL,
                UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
                UNIQUE INDEX UNIQ_8D93D649F85E0677 (username),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Create category table
        $this->addSql(<<<'SQL'
            CREATE TABLE category (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                UNIQUE INDEX UNIQ_64C19C15E237E06 (name),
                UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Create article table
        $this->addSql(<<<'SQL'
            CREATE TABLE article (
                id INT AUTO_INCREMENT NOT NULL,
                author_id INT NOT NULL,
                category_id INT DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX IDX_23A0E66F675F31B (author_id),
                INDEX IDX_23A0E6612469DE2 (category_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Create comment table
        $this->addSql(<<<'SQL'
            CREATE TABLE comment (
                id INT AUTO_INCREMENT NOT NULL,
                article_id INT NOT NULL,
                author_id INT NOT NULL,
                content LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX IDX_9474526C7294869C (article_id),
                INDEX IDX_9474526CF675F31B (author_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);


        // Add foreign key constraints
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES admin_user (id)
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id)
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraints
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7294869C
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E6612469DE2
        SQL);

        // Drop tables
        $this->addSql(<<<'SQL'
            DROP TABLE migration_versions
        SQL);

        $this->addSql(<<<'SQL'
            DROP TABLE comment
        SQL);

        $this->addSql(<<<'SQL'
            DROP TABLE article
        SQL);

        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL);

        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);

        $this->addSql(<<<'SQL'
            DROP TABLE admin_user
        SQL);
    }
}
