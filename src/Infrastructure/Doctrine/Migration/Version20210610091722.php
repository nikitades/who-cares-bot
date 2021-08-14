<?php

declare(strict_types=1);

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610091722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_message_record ALTER message_id TYPE BIGINT');
        $this->addSql('ALTER TABLE user_message_record ALTER message_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_message_record ALTER reply_to_message_id TYPE BIGINT');
        $this->addSql('ALTER TABLE user_message_record ALTER reply_to_message_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_message_record ALTER chat_id TYPE BIGINT');
        $this->addSql('ALTER TABLE user_message_record ALTER chat_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_message_record ALTER user_id TYPE BIGINT');
        $this->addSql('ALTER TABLE user_message_record ALTER user_id DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_message_record ALTER message_id TYPE INT');
        $this->addSql('ALTER TABLE user_message_record ALTER message_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_message_record ALTER reply_to_message_id TYPE INT');
        $this->addSql('ALTER TABLE user_message_record ALTER reply_to_message_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_message_record ALTER chat_id TYPE INT');
        $this->addSql('ALTER TABLE user_message_record ALTER chat_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_message_record ALTER user_id TYPE INT');
        $this->addSql('ALTER TABLE user_message_record ALTER user_id DROP DEFAULT');
    }
}
