<?php

namespace App\Repositories;

use App\Models\Request;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

class DynamoDbRequestRepository implements RequestRepositoryInterface
{
    private DynamoDbClient $dynamoDb;
    private string $tableName;

    public function __construct()
    {
        $this->dynamoDb = new DynamoDbClient([
            'region'  => $_ENV['AWS_REGION'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ],
        ]);
        $this->tableName = 'requests';
    }

    public function save(Request $request): ?Request
    {
        $id = $this->generateUuidV4();
        try {
            $this->dynamoDb->putItem([
                'TableName' => $this->tableName,
                'Item' => [
                    'id'        => ['S' => $id],
                    'createdAt' => ['S' => date('Y-m-d H:i:s')],
                    'nombre'    => ['S' => $request->getNombre()],
                    'correo'    => ['S' => $request->getCorreo()],
                    'telefono'  => ['S' => $request->getTelefono()],
                    'estado'    => ['S' => 'pending'],
                    'idiomas'   => ['S' => $request->getIdiomas()],
                ],
            ]);

            return $this->findById($id);
        } catch (AwsException $e) {
            error_log('DynamoDbRequestRepository Error - save: ' . $e->getMessage());
        }

        return null;
    }

    public function findAll(): array
    {
        $requests = [];
        try {
            $result = $this->dynamoDb->scan([
                'TableName' => $this->tableName,
            ]);

            foreach ($result['Items'] as $item) {
                $requests[] = new Request(
                    $item['nombre']['S'],
                    $item['correo']['S'],
                    $item['telefono']['S'],
                    $item['id']['S'],
                    $item['estado']['S'],
                    $item['createdAt']['S'],
                    $item['idiomas']['S']
                );
            }
        } catch (AwsException $e) {
            error_log('DynamoDbRequestRepository Error - findAll: ' . $e->getMessage());
        }

        return $requests;
    }

    public function findById(int $id): ?Request
    {
        try {
            $result = $this->dynamoDb->getItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'id' => ['S' => (string)$id],
                ],
            ]);

            if (isset($result['Item'])) {
                $item = $result['Item'];
                return new Request(
                    $item['nombre']['S'],
                    $item['correo']['S'],
                    $item['telefono']['S'],
                    $item['id']['S'],
                    $item['estado']['S'],
                    $item['createdAt']['S'],
                    $item['idiomas']['S']
                );
            }
        } catch (AwsException $e) {
            error_log('DynamoDbRequestRepository Error - findById: ' . $e->getMessage());
        }

        return null;
    }

    public function findSummaryById(int $id): ?array
    {
        $request = $this->findById($id);
        if (!$request) {
            return null;
        }

        return [
            'nombreSolicitante' => $request->getNombre(),
            'estado' => $request->getEstado(),
            'idiomasSolicitados' => explode(',', $request->getIdiomas()),
            'requestId' => $request->getId()
        ];
    }

    public function updateStatus(int $id, bool $contactado): bool
    {
        try {
            $this->dynamoDb->updateItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'id' => ['S' => (string)$id],
                ],
                'UpdateExpression' => 'set estado = :s',
                'ExpressionAttributeValues' => [
                    ':s' => ['S' => $contactado ? 'contacted' : 'pending'],
                ],
            ]);

            return true;
        } catch (AwsException $e) {
            error_log('DynamoDbRequestRepository Error - updateStatus: ' . $e->getMessage());
        }

        return false;
    }

    public function updateField(int $requestId, string $field, string $newValue): bool
    {
        $allowedFields = ['nombre', 'correo', 'telefono'];
        if (!in_array($field, $allowedFields)) {
            error_log("DynamoDbRequestRepository Error - updateField: Attempt to update a non-whitelisted field: $field");
            return false;
        }

        try {
            $this->dynamoDb->updateItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'id' => ['S' => (string)$requestId],
                ],
                'UpdateExpression' => "set $field = :v",
                'ExpressionAttributeValues' => [
                    ':v' => ['S' => $newValue],
                ],
            ]);

            return true;
        } catch (AwsException $e) {
            error_log('DynamoDbRequestRepository Error - updateField: ' . $e->getMessage());
        }

        return false;
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
