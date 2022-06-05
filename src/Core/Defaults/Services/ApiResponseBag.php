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
            $data['errors'] = $this->getErrors();
        }

        if(!empty($this->data)) {
            $data['data'] = $this->getData();
        }

        return $data;
    }

    public function addError(string $error, bool $isSecure = false): void {
        $this->errors[] = ($isSecure || MODE === 'dev') ? $error : 'Something went wrong!';
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

    public static function getSimpleSuccessData(): array {
        return (new static())->getResponseData();
    }
}