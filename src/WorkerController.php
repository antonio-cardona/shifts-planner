<?php

class WorkerController implements ControllerBase
{
    public function __construct(private WorkerGateway $gateway)
    {
    }

    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    public function processResourceRequest(string $method, string $id): void
    {
        $worker = $this->gateway->get($id);

        if (!$worker) {
            http_response_code(404);
            echo json_encode(["message" => "Worker not found"]);
            return;
        }

        switch ($method) {
            case 'GET':
                echo json_encode($worker);
                break;

            case 'PATCH':
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $rows = $this->gateway->update($worker, $data);

                echo json_encode([
                    "message" => "Worker $id updated",
                    "rows" => $rows
                ]);
                break;

            case 'DELETE':
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Worker $id deleted",
                    "rows" => $rows
                ]);
                break;

            default:
                // ERROR: Method not allowed.
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }

    public function processCollectionRequest(string $method)
    {
        switch ($method) {
            case 'GET':
                echo json_encode($this->gateway->getAll());
                break;

            case 'POST':
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->gateway->create($data);

                http_response_code(201);
                echo json_encode([
                    "message" => "Worker created",
                    "id" => $id
                ]);
                break;

            default:
                // ERROR: Method not allowed.
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

    public function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if ($is_new) {
            if (empty($data["name"])) {
                $errors[] = "name is required";
            }

            if (empty($data["document"])) {
                $errors[] = "document is required";
            }
        }

        return $errors;
    }
}