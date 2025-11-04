<?php

namespace App\Repositories;

use App\Models\Admin;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

class DynamoDbAdminRepository implements AdminRepositoryInterface
{
    private DynamoDbClient $dynamoDb;
    private string $tableName;

    public function __construct()
    {
        $this->dynamoDb = new DynamoDbClient([
            'region'  => $_ENV['AWS_REGION'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_DYNAMO_USER'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY_DYNAMO_USER'],
            ],
        ]);
        $this->tableName = 'Language-Advisory-Platform-admins';
    }

    public function findByUsername(string $username): ?Admin
    {
        try {
            $result = $this->dynamoDb->getItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'username' => ['S' => $username],
                ],
            ]);

            if (isset($result['Item'])) {
                $item = $result['Item'];
                return new Admin(
                    $item['username']['S'],
                    $item['password_hash']['S'],
                    $item['name']['S'],
                    $item['id']['S']
                );
            }
        } catch (AwsException $e) {
            error_log('DynamoDbAdminRepository Error - findByUsername: ' . $e->getMessage());
        }

        return null;
    }

    public function create(string $username, string $passwordHash, string $name): ?Admin
    {
        $id = $this->generateUuidV4();
        try {
            $this->dynamoDb->putItem([
                'TableName' => $this->tableName,
                'Item' => [
                    'id'            => ['S' => $id],
                    'username'      => ['S' => $username],
                    'password_hash' => ['S' => $passwordHash],
                    'name'          => ['S' => $name],
                ],
            ]);

            return new Admin($username, $passwordHash, $name, $id);
        } catch (AwsException $e) {
            error_log('DynamoDbAdminRepository Error - create: ' . $e->getMessage());
        }

        return null;
    }

    private function generateUuidV4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
