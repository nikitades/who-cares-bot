<?php

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecord;
use Nikitades\WhoCaresBot\WebApi\Domain\UuidProviderInterface;
use Safe\DateTime;

class UserMessageRecordFixtures extends Fixture
{
    public function __construct(private UuidProviderInterface $uuidProvider)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $chatId = -515375295;
        $messageId = null;

        for ($n = 0; $n < 500; ++$n) {
            $replyToMessageId = $messageId;
            $userId = $faker->randomNumber();
            $userNickname = $faker->name;
            $text = $faker->realText();
            $textLength = mb_strlen($text);
            $attachType = 'text';
            $stickerId = null;
            $date = DateTime::createFromInterface($faker->dateTimeBetween('-15 day'));
            for ($m = 0; $m < $faker->numberBetween(10, 25); ++$m) {
                $messageId = $faker->randomNumber();
                $manager->persist(new UserMessageRecord(
                        $this->uuidProvider->provide(),
                        $messageId,
                        $replyToMessageId,
                        $chatId,
                        $userId,
                        $userNickname,
                        $date,
                        $text,
                        $textLength,
                        $attachType,
                        $stickerId
                    ));
            }

            $manager->flush();
        }

        for ($n = 0; $n < 6; ++$n) {
            $replyToMessageId = $messageId;
            $userId = $faker->randomNumber();
            $userNickname = $faker->name;
            $text = $faker->realText();
            $textLength = mb_strlen($text);
            $attachType = 'text';
            $stickerId = null;

            for ($m = 0; $m < $faker->numberBetween(75, 150); ++$m) {
                $messageId = $faker->randomNumber();
                $manager->persist(new UserMessageRecord(
                    $this->uuidProvider->provide(),
                    $messageId,
                    $replyToMessageId,
                    $chatId,
                    $userId,
                    $userNickname,
                    DateTime::createFromInterface($faker->dateTimeBetween('-12 hour')),
                    $text,
                    $textLength,
                    $attachType,
                    $stickerId
                ));
            }

            $manager->flush();
        }
    }
}
