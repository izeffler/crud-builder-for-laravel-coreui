# CrudBuilderForLaravelCoreUI

Um pacote para ajudar na construção de CRUDs no Laravel 11 com COREUI.

## Instalação

Você pode instalar o pacote via Composer:

```
composer require izeffler/crud-builder-for-laravel-coreui
```

## Publicação de Arquivos

Para publicar os arquivos de visualização, execute o comando:

```bash
php artisan vendor:publish --tag=views
```

[//]: # (## Uso)

[//]: # ()
[//]: # (### Helpers)

[//]: # ()
[//]: # (O pacote fornece dois helpers principais: `FormHelper` e `ListHelper`. Você pode usá-los da seguinte forma:)

[//]: # ()
[//]: # (```php)

[//]: # (use CrudBuilderForLaravelCoreUI\FormHelper;)

[//]: # (use CrudBuilderForLaravelCoreUI\ListHelper;)

[//]: # ()
[//]: # (// Exemplo de uso do FormHelper)

[//]: # ($formHelper = new FormHelper&#40;&#41;;)

[//]: # ()
[//]: # (// Exemplo de uso do ListHelper)

[//]: # ($listHelper = new ListHelper&#40;&#41;;)

[//]: # (```)

[//]: # ()
[//]: # (### Views)

[//]: # ()
[//]: # (As views podem ser carregadas usando o namespace `crud-builder-for-laravel-core-ui`:)

[//]: # ()
[//]: # (```php)

[//]: # (return view&#40;'crud-builder-for-laravel-core-ui::form'&#41;;)

[//]: # (return view&#40;'crud-builder-for-laravel-core-ui::list'&#41;;)

[//]: # (```)

## Contribuição

Se você encontrar algum problema ou tiver sugestões, sinta-se à vontade para abrir uma issue ou enviar um pull request.

## Licença

Este projeto está licenciado sob a licença MIT.

## Autor

- **Lucas Izeffler** - [lucas123izeffler@gmail.com](mailto:lucas123izeffler@gmail.com)