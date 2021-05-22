<?php

namespace Nikitades\WhoCaresBot\WebApi\Infrastructure\Doctrine\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Nikitades\WhoCaresBot\WebApi\Domain\UserMessageRecord\UserMessageRecord;
use Symfony\Component\Uid\Uuid;

class UserMessageRecordFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($k = 0; $k < 5; ++$k) {
            $chatId = $faker->randomNumber();
            $messageId = null;
            for ($n = 0; $n < 25; ++$n) {
                $replyToMessageId = $messageId;
                $userId = $faker->randomNumber();
                $userNickname = $faker->name;
                $createdAt = $faker->dateTimeBetween('-12 hours');
                $text = $faker->realText();
                $textLength = mb_strlen($text);
                $attachType = 'text';
                $stickerId = null;

                for ($m = 0; $m < $faker->numberBetween(30, 50); ++$m) {
                    $messageId = $faker->randomNumber();
                    $manager->persist(new UserMessageRecord(
                        Uuid::v4(),
                        $messageId,
                        $replyToMessageId,
                        $chatId,
                        $userId,
                        $userNickname,
                        $createdAt,
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
}
