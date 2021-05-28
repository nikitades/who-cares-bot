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

        for ($k = 0; $k < 15; ++$k) {
            $messageId = null;
            for ($n = 0; $n < 25; ++$n) {
                $replyToMessageId = $messageId;
                $userId = $faker->randomNumber();
                $userNickname = $faker->name;
                $createdAt = DateTime::createFromInterface($faker->dateTimeBetween('-1 month', '-3 days'));
                $text = $faker->realText();
                $textLength = mb_strlen($text);
                $attachType = 'text';
                $stickerId = null;

                for ($m = 0; $m < $faker->numberBetween(30, 50); ++$m) {
                    $messageId = $faker->randomNumber();
                    $manager->persist(new UserMessageRecord(
                        $this->uuidProvider->provide(),
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
