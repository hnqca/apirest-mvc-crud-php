
<h1 align="center">
    <img src="https://miro.medium.com/v2/resize:fit:800/1*Jj3L5aY6_7c0R9a8U0d_Qw.png" width="230px"/><br/>
</h1>
<p align="center">
     Simples <b>API REST</b> desenvolvida à fim de aprender conceitos relacionados à webservice na linguagem PHP.
     <br>
     Foi utilizado durante o desenvolvimento: Orientação à objetos juntamente com o padrão de arquitetura MVC.
</p>
<p align="center">
    <a href="#"><img src="https://img.shields.io/badge/php-8.x+-00ADD8?style=for-the-badge&logo=php"/></a>&nbsp;
</p>

***

### Objetivo da API:

Realizar o gerenciamento de usuários (CRUD) cadastrar, ler, atualizar e deletar registros no banco de dados MySQL.

### Utilização:

Defina a conexão com o banco de dados através do arquivo "[.env](https://github.com/HenriqueCacerez/apirest-mvc-crud-php/blob/main/.env)" que está localizado no diretório raiz do projeto.

Crie um banco de dados chamado **"apirest"** e importe o arquivo "[users.sql](https://github.com/HenriqueCacerez/apirest-mvc-crud-php/blob/main/users.sql)" que também está no diretório raiz.

***

### 📖 Dependências utilizadas:
Execute o comando ``composer install`` para instalar todas as dependências utilizadas neste projeto.

| Nome | Versão |
| --- | --- |
| **[vlucas/phpdotenv](https://packagist.org/packages/vlucas/phpdotenv)** | ^5.5 |
| **[coffeecode/router](https://packagist.org/packages/coffeecode/router)**| ^2.0 |

_____


### 📖 Authorization Bearer:

Para consumir a API, é necessário enviar o **Authorization Bearer** no cabeçalho da requisição. Foi criado dois tokens JWT, onde cada token contém diferentes níveis de acessos.

| Acesso | Token |
| --- | --- |
| 1 | `eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3MiOjF9.F23gElIWUCdtlytwQrLzURNnmv+QSn5G0eoVg+rWfos=` |
| 2 | `eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3MiOjJ9.ZCByDaVvbbFb5TJn91Itbcp6CogMEPpMJWq7QiZvMAo=` |

***

### <img src="https://media2.giphy.com/media/QssGEmpkyEOhBCb7e1/giphy.gif?cid=ecf05e47a0n3gi1bfqntqmob8g9aid1oyj2wr3ds3mg700bl&rid=giphy.gif" width ="25"> Endpoints (6):

https://example.com/ `endpoint`

| Endpoint | Acesso | Método       | Descrição |
| ---      | ---    | ---        | --- |
| `/users/{id}`  | >= `1` | **GET**    | _Obtém os detalhes de um usuário específico com base no seu ID_ |
| `/users`  | >= `1` | **GET**    | _Obtém a lista de todos os usuários cadastrados_ |
| `/users/limit/{limit}`  | >= `1` | **GET**    | _Especifica a quantidade máxima de registros a serem listados_ |
| `/users`  | >= `2` | **POST**    | _Cria um novo usuário_ |
| `/users/{id}`  | >= `2` | **PUT**    | _Atualiza as informações de um usuário com base no seu ID_ |
| `/users/{id}`  | >= `2` | **DELETE**    | _Remove um usuário específico com base no seu ID_ |

****

## Características e tecnologias:

* PHP 8.X
* Modelo REST
* Orientação à Objetos (POO)
* ACL
* MVC
* JSON
* JWT
* Composer
* PSR-4
* PDO
* MySQL
* Bearer Authorization
* Métodos GET, PUT, POST e DELETE
