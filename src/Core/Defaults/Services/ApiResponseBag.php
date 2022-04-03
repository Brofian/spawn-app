<?php

namespace SpawnCore\Defaults\Services;


class ApiResponseBag {

    protected array $errors = [];

    public function getResponseData(): array {
        $data =  [
            'success' => empty($this->errors)
        ];

        if(!empty($this->errors)) {
            $data['errors'] = $this->errors;
        }

        return $data;
    }

    public function addError(string $error): void {
        $this->errors[] = $error;
    }


    public function getErrors(): array {
        return $this->errors;
    }
}