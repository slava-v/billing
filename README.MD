Billing project
===============

## Description

Purpose of the project is to demonstrate the creation of API service
and collection of API endpoints responsible for adding invoices for debtors/creditors

To be able to create the functionality we would need to have several api endpoints and a ready database.

In database, to store the debtors, creditors, and invoice we need to have at least the following entities:

* company - _will act as debtor or creditor_
* invoice - _the actual invoice that keeps the totals and link between debtor and creditor_


The following `invoice_items` entity wasn't created. It can be added later. Would keep the invoice items and link to invoice
More details about the structure, see please the diagram1-tables.png and diagram2-flow.png

The [swagger-php](#Environment) interface would be available to list all the endpoints


## Environment

`APP_ENV=test`

Issue the `docker-compose up -d --build` to build the images and 
run the docker containers.

The following containers would be available:
* php-fpm
* database with Postgres
* mail-catcher
* swagger-ui, available at port `18080`

After having the containers , the commands under `./bin` folder would be 
able to execute the commands in container. 
Commands like: php, phpstan, phpunit phpcs

## Initialize database

Docker containers are up ? 

Then you're good to go with other commands, in this case initialize the database 
and seed it with data fixtures

- `bin/console doctrine:database:create`
- `bin/console doctrine:migrations:migrate`
- `bin/console doctrine:fixtures:load`

## Execute phpunit & static analysis

 `bin/phpunit`
 `bin/phpcs`
 `bin/phpstan`

## Testing

The API service, unfortunately, doesn't have a UI, there is swagger-ui present to give a glimpse of
how the api-documentation would look like

## The application defines the following endpoints

| Name                   | Method    | Scheme   | Host   | Path                    | description                                              |
|------------------------|-----------|----------|--------|-------------------------|----------------------------------------------------------|
| get_company_by_name_v1 | GET       | ANY      | ANY    | /v1/company/{name}/name | Gets details about a company by its name                 |
| get_company_by_id_v1   | GET       | ANY      | ANY    | /v1/company/{id}        | get details about a company by its ID                    |
| add_company_v1         | PUT       | ANY      | ANY    | /v1/company             | Adds new "company" entry                                 |
| _modify_company_v1_    | _PATCH_   | _ANY_    | _ANY_  | _/v1/company{id}_       | Modifies a "company" entry. NOT IMPLEMENTED              |
| get_invoice_by_id_v1   | GET       | ANY      | ANY    | /v1/invoice/{id}        | Get details about a invoice by its ID                    |
| add_invoice_v1         | PUT       | ANY      | ANY    | /v1/invoice             | Add new "invoice" entry                                  |
| _modify_invoice_v1_    | _PATCH_   | _ANY_    | _ANY_  | _/v1/invoice{id}_       | Modifies a "invoice" entry. NOT IMPLEMENTED              |
| _delete_invoice_v1_    | _DELETE_  | _ANY_    | _ANY_  | _/v1/invoice{id}_       | ?? (soft deletes) a invoice entry. NOT IMPLEMENTED       |

Initial state of the API Service is without authentication that could be achieved by adding the symfony/security packages and configure firewalls.

The authentication would be possible through Firewalls and Authenticators using JWT Tokens that would match
a value in the company `accessToken` field.
This , potentially, could be extended into another table to manage different users and permissions.


In the code you may find comments with explanations and `@todo` entries. The todo entries are reminders or
possible options of different type of implementations.


## Partial implementations:

#### First and important thing is the Authentication , which is missing atm.
  
  I have skipped its implementation, but
  
  - I would've achieved this by adding the `symfony/security` package and `lexik/jwt-authentication-bundle` 
  - By Configuring the firewalls and adding the custom Authenticators to use the `company` entity.accessToken


#### The openapi json specifications were partially done 
  I did it for the CompanyController partially, to give an idea of how I see it implemented.

  After defining the specifications in the controllers and used entities, the command
    `vendor/bin/openapi --output ./openapi.v1.json --format json ./src`
    would re-generate the json specifications, which would be used by the swagger-ui container to render the API endpoint descriptions
  
#### The `invoice_items` entity isn't present. 
  I was planning to keep invoice item details in this table and link it with the invoice
  More details on the structure please see the diagram1 and diagram2


In conclusion
-------------
With more time, of course, better implementation can be achieved. I tried to stay focused on the task
at hand and the written implementations were to demonstrate my experience.

One might argue on one type of implementations or another, but I think there is always place for improvement.
