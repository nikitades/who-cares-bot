#!/bin/bash
vendor/bin/php-cs-fixer fix --config ../common/codequality/phpcsfixer.dist.php ./src ./tests
vendor/bin/phpstan analyse src tests --memory-limit 256M
vendor/bin/phpunit tests