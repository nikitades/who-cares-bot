<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210518002202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_message_record ADD user_nickname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_message_record RENAME COLUMN author_id TO user_id');
        $this->addSql('ALTER TABLE user_message_record RENAME COLUMN sent_at TO created_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_message_record DROP user_nickname');
        $this->addSql('ALTER TABLE user_message_record RENAME COLUMN user_id TO author_id');
        $this->addSql('ALTER TABLE user_message_record RENAME COLUMN created_at TO sent_at');
    }
}
