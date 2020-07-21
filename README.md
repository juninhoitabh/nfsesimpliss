# sped-nfse-bhiss

Biblioteca para comunicação com webservices do Projeto NFSe nfsesimpliss

> NOTA: Este repositório foi separado do sped-nfe-nacional devido a diferenças de estruturas entre os projetos nfsesimpliss e Nacional.

## BETHA TESTES

[![License][ico-license]][link-packagist]

Este pacote é aderente com os [PSR-1], [PSR-2] e [PSR-4]. Se você observar negligências de conformidade, por favor envie um patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

Não deixe de se cadastrar no [grupo de discussão do NFePHP](http://groups.google.com/group/nfephp) para acompanhar o desenvolvimento e participar das discussões e tirar duvidas!


## Cidades já atendidas (podem ainda ser necessários alguns ajustes)

- João Monlevade (MG)

## Dependências

- PHP >= 7.1
- ext-curl
- ext-soap
- ext-zlib
- ext-dom
- ext-openssl
- ext-json
- ext-simplexml
- ext-libxml

### Outras Libs

- juninhoitabh/nfsesimpliss
- justinrainbow/json-schema

## Contribuindo
Este é um projeto totalmente *OpenSource*, para usa-lo e modifica-lo você não paga absolutamente nada. Porém para continuarmos a mante-lo é necessário qua alguma contribuição seja feita, seja auxiliando na codificação, na documentação ou na realização de testes e identificação de falhas e BUGs.

**Este pacote esta listado no [Packgist](https://packagist.org/) foi desenvolvido para uso do [Composer](https://getcomposer.org/), portanto não será explicitada nenhuma alternativa de instalação.**

*Durante a fase de desenvolvimento e testes este pacote deve ser instalado com:*
```bash
composer require juninhoitabh/nfsesimpliss:dev-master
```

*Ou ainda,*
```bash
composer require juninhoitabh/nfsesimpliss:dev-master --prefer-dist
```

*Ou ainda alterando o composer.json do seu aplicativo inserindo:*
```json
"require": {
    "juninhoitabh/nfsesimpliss" : "dev-master"
}
```

> NOTA: Ao utilizar este pacote ainda na fase de desenvolvimento não se esqueça de alterar o composer.json da sua aplicação para aceitar pacotes em desenvolvimento, alterando a propriedade "minimum-stability" de "stable" para "dev".
> ```json
> "minimum-stability": "dev",
> "prefer-stable": true
> ```

*Após os stable realeases estarem disponíveis, este pacote poderá ser instalado com:*
```bash
composer require juninhoitabh/nfsesimpliss
```
Ou ainda alterando o composer.json do seu aplicativo inserindo:
```json
"require": {
    "juninhoitabh/nfsesimpliss" : "^1.0"
}
```

## Forma de uso
vide a pasta *Examples*

## Log de mudanças e versões
Acompanhe o [CHANGELOG](CHANGELOG.md) para maiores informações sobre as alterações recentes.

## Testing

Todos os testes são desenvolvidos para operar com o PHPUNIT

## Security

Caso você encontre algum problema relativo a segurança, por favor envie um email diretamente aos mantenedores do pacote ao invés de abrir um ISSUE.

## Credits

Roberto L. Machado (owner and developer)

## License

Este pacote está diponibilizado sob LGPLv3 ou MIT License (MIT). Leia  [Arquivo de Licença](LICENSE.md) para maiores informações.


[ico-stable]: https://poser.pugx.org/juninhoitabh/nfsesimpliss/version
[ico-stars]: https://img.shields.io/github/stars/juninhoitabh/nfsesimpliss.svg?style=flat-square
[ico-forks]: https://img.shields.io/github/forks/juninhoitabh/nfsesimpliss.svg?style=flat-square
[ico-issues]: https://img.shields.io/github/issues/juninhoitabh/nfsesimpliss.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/juninhoitabh/nfsesimpliss/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/juninhoitabh/nfsesimpliss.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/juninhoitabh/nfsesimpliss.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/juninhoitabh/nfsesimpliss.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/juninhoitabh/nfsesimpliss.svg?style=flat-square
[ico-license]: https://poser.pugx.org/nfephp-org/nfephp/license.svg?style=flat-square
[ico-gitter]: https://img.shields.io/badge/GITTER-4%20users%20online-green.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/juninhoitabh/nfsesimpliss
[link-travis]: https://travis-ci.org/juninhoitabh/nfsesimpliss
[link-scrutinizer]: https://scrutinizer-ci.com/g/juninhoitabh/nfsesimpliss/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/juninhoitabh/nfsesimpliss
[link-downloads]: https://packagist.org/packages/juninhoitabh/nfsesimpliss
[link-author]: https://github.com/nfephp-org
[link-issues]: https://github.com/juninhoitabh/nfsesimpliss/issues
[link-forks]: https://github.com/juninhoitabh/nfsesimpliss/network
[link-stars]: https://github.com/juninhoitabh/nfsesimpliss/stargazers
