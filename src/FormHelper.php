<?php

namespace CrudBuilderForLaravelCoreUI;

class FormHelper
{
    protected array $fields = [];
    protected string $actionUrl = '';
    protected string $method = 'POST';
    protected string $saveButtonLabel = 'Salvar';
    protected string $cancelButtonLabel = 'Cancelar';
    protected string $cancelUrl = '';
    protected bool $useAjax = true;
    protected array $formData = [];
    protected string $currentPanel = 'Opções Gerais';
    protected array $panels = [];

    public function __construct()
    {
        $this->panels[$this->currentPanel] = ['label' => $this->currentPanel, 'fields' => []];
    }

    public function setAction(string $url, string $method = 'POST'): self
    {
        $this->actionUrl = $url;
        $this->method = $method;
        return $this;
    }

    public function setSaveButtonLabel(string $label): self
    {
        $this->saveButtonLabel = $label;
        return $this;
    }

    public function setCancelButtonLabel(string $label): self
    {
        $this->cancelButtonLabel = $label;
        return $this;
    }

    public function setCancelButtonUrl(string $url): self
    {
        $this->cancelUrl = $url;
        return $this;
    }

    public function setFormData(array $data): self
    {
        $this->formData = $data;
        return $this;
    }

    public function disableAjax(): self
    {
        $this->useAjax = false;
        return $this;
    }

    public function panel(string $label, array $options = []): self
    {
        $this->currentPanel = $label; // Atualiza o painel atual
        if (!isset($this->panels[$this->currentPanel])) {
            $this->panels[$this->currentPanel] = [
                'label' => $label,
                'fields' => [],
                'options' => $options,
            ];
        }
        return $this;
    }

    public function text(string $name, string $label, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'text', 'options' => $options];
        return $this;
    }

    public function email(string $name, string $label, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'email', 'options' => $options];
        return $this;
    }

    public function date(string $name, string $label, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'date', 'options' => $options];
        return $this;
    }

    public function number(string $name, string $label, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'number', 'options' => $options];
        return $this;
    }

    public function password(string $name, string $label, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'password', 'options' => $options];
        return $this;
    }

    public function textarea(string $name, string $label, int $maxChars, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'textarea', 'maxChars' => $maxChars, 'options' => $options];
        return $this;
    }

    public function select(string $name, string $label, array $values, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'select', 'values' => $values, 'options' => $options];
        return $this;
    }

    public function multiselect(string $name, string $label, array $values, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'multiselect', 'values' => $values, 'options' => $options];
        return $this;
    }

    public function radio(string $name, string $label, array $values = [1 => 'Sim', 0 => 'Não'], array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'radio', 'values' => $values, 'options' => $options];
        return $this;
    }

    public function show(string $name, string $label, string $data, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'show', 'data' => $data, 'options' => $options];
        return $this;
    }

    public function file(string $name, string $label, array $options = []): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['label' => $label, 'type' => 'file', 'options' => $options];
        return $this;
    }

    public function hidden(string $name): self
    {
        $this->panels[$this->currentPanel]['fields'][$name] = ['type' => 'hidden'];
        return $this;
    }

    public function render(): string
    {
        return view('crud-builder-for-laravel-core-ui::form', [
            'actionUrl' => $this->actionUrl,
            'method' => $this->method,
            'saveButtonLabel' => $this->saveButtonLabel,
            'cancelButtonLabel' => $this->cancelButtonLabel,
            'cancelUrl' => $this->cancelUrl,
            'useAjax' => $this->useAjax,
            'panels' => $this->panels,
            'formData' => $this->formData,
        ])->toHtml();
    }
}
