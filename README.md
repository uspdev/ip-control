# ip-control

Biblioteca que limita o acesso aos endereços da aplicação com base no IP de orígem.

Em alguns casos queremos limitar nossa aplicação ou parte dela do acesso público e irrestrito. Essa biblioteca permite restringir o acesso por meio de uma lista de ips e máscaras autorizados por meio de uma diretiva simples nas rotas a serem protegidas.

    Ipcontrol::proteger();

Ao colocar essa linha no seu código, a biblioteca verificará o endereço de orígem da requisição e caso seja negado retornará uma mensagem "HTTP 403 Forbidden". Caso queira que sua aplicação trate o bloqueio chame o método com parâmetro false

    Ipcontrol::proteger(false);

Como a lista de ips liberados é guardado em um arquivo texto, a biblioteca não foi pensada em regras complexas. Ela é bastante útil quando temos aplicações de uso interno e que queremos restringir aos ips da unidade, por exemplo.

## Dependências

Esta biblioteca foi testada somente em Linux e em PHP 7.2 e 7.3 usando apache.


## Instalação e configuração

Para instalar use o composer

```bash
composer require uspdev/ip-control
```

A biblioteca usa as seguintes configurações

```php
putenv('USPDEV_IP_CONTROL=whitelist');
putenv('USPDEV_IP_CONTROL_LOCAL=pasta/onde/guardar/os/arquivos');
```
USPDEV_IP_CONTROL pode assumir o valor 'localhost', dessa forma restringindo o acesso somente à máquina local.

Se USPDEV_IP_CONTROL não for definido no ambiente, o acesso será liberado por padrão.

O arquivo uspdev_ip_control_whitelist.txt será criado na pasta indicada acima e deve ser preenchido adequadamente. Ele tem o seguinte formato

    ip,mascara,comentário

Exemplo

    200.144.248.41, 32, meu 1o. endereço liberado
    143.107.225.0, 24, minha 1a. rede liberada

Caso o arquivo esteja vazio, a biblioteca vai negar todos os acessos, menos localhost.

O acesso local (localhost) é sempre liberado por padrão.

## Exemplo de utilização

```php
putenv('USPDEV_IP_CONTROL=whitelist');
putenv('USPDEV_IP_CONTROL_LOCAL='.__DIR__.'/local');

use Uspdev\Ipcontrol\Ipcontrol;
Ipcontrol::proteger();
```

## Testes

Em linha de comando, rode o teste em

    php vendor/uspdev/ip-control/test/test.php
