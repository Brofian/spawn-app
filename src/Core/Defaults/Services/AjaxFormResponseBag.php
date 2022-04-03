<?php

namespace SpawnCore\Defaults\Services;


class AjaxFormResponseBag extends ApiResponseBag {

    protected array $invalidFields = [];
    protected bool $triggerReload = false;
    protected ?string $redirect = null;
    protected ?string $script = null;
    protected array $events = [];
    protected bool $triggerEventsOnFail = false;
    protected bool $executeScriptOnFail = false;

    public function getResponseData(): array {
        $data = parent::getResponseData();

        if(!empty($this->invalidFields)) {
            $data['invalidFields'] = $this->invalidFields;
        }

        if($this->redirect) {
            $data['redirect'] = $this->redirect;
        }
        elseif($this->triggerReload && $data['success']) {
            $data['reload'] = $this->triggerReload;
        }

        if(!empty($this->events) && ($data['success'] || $this->triggerEventsOnFail)) {
            $data['events'] = $this->events;
        }

        if($this->script && ($data['success'] || $this->executeScriptOnFail)) {
            $data['script'] = $this->script;
        }


        return $data;
    }


    public function addInvalidField(string $fieldName, string $message = 'Invalid'): void {
        $this->invalidFields[$fieldName] = $message;
    }

    public function triggerReload(): void {
        $this->triggerReload = true;
    }

    public function setRedirect(string $redirectUrl): void {
        $this->redirect = $redirectUrl;
    }

    public function setScript(string $script): void {
        $this->script = $script;
    }

    public function executeScriptOnFail(): void {
        $this->executeScriptOnFail = true;
    }

    public function triggerJSEvent(string $event): void {
        $this->events[] = $event;
    }

    public function triggerEventsOnFail(): void {
        $this->triggerEventsOnFail = true;
    }









    public function getEvents(): array {
        return $this->events;
    }

    public function getInvalidFields(): array {
        return $this->invalidFields;
    }

}