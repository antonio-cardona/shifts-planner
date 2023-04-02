<?php

class ShiftController implements ControllerBase
{
    public function __construct(private ShiftGateway $gateway)
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
        $shift = $this->gateway->get($id);

        if (!$shift) {
            http_response_code(404);
            echo json_encode(["message" => "Shift not found"]);
            return;
        }

        switch ($method) {
            case 'GET':
                echo json_encode($shift);
                break;

            case 'PATCH':
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $rows = $this->gateway->update($shift, $data);

                echo json_encode([
                    "message" => "Shift $id updated",
                    "rows" => $rows
                ]);
                break;

            case 'DELETE':
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Shift $id deleted",
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

                if ($id){
                    http_response_code(201);
                    echo json_encode([
                        "message" => "Shift created",
                        "id" => $id
                    ]);
                } else {
                    http_response_code(422);
                    echo json_encode(["errors" => ["The Worker {$data['worker_id']} already has a Shift assigned to the requested date ({$data['shift_date']})."]]);
                }

                
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
            if (empty($data["worker_id"])) {
                $errors[] = "worker_id is required";
            }

            if (empty($data["shift_type"])) {
                $errors[] = "shift_type is required";
            }

            if (empty($data["shift_date"])) {
                $errors[] = "shift_date is required";
            }
        }

        if (array_key_exists("worker_id", $data)) {
            if (filter_var($data["worker_id"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "worker_id must be an integer";
            }
        }

        if (array_key_exists("shift_type", $data)) {
            if (filter_var($data["shift_type"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "shift_type must be an integer";
            } else if ((int) $data["shift_type"] < 1 || (int) $data["shift_type"] > 3) {
                $errors[] = "shift_type must be an integer between 1-3";
            }
        }

        return $errors;
    }
}