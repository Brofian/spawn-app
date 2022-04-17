<?php

namespace SpawnCore\Defaults\Services;


class ApiResponseBag {

    protected array $errors = [];
    protected array $data = [];

    public function getResponseData(): array {
        $data =  [
            'success' => empty($this->errors)
        ];

        if(!empty($this->errors)) {
            $data['errors'] = $this->errors;
        }

        if(!empty($this->data)) {
            $data['data'] = $this->data;
        }

        return $data;
    }

    public function addError(string $error): void {
        $this->errors[] = $error;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function addData(string $key, $data): void {
        $this->data[$key] = $data;
    }

    public function getData(): array {
        return $this->data;
    }
}