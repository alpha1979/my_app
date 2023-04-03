## Symfony 6 Project
## Database on docker
. Run the symfony project ``` symfony serve -d ``` <br />
. Run the docker ``` docker compose up -d ``` <br />
. Run migration ``` symfony console make:migration   ``` <br />
. Run ``` symfony console doctrine:migrations:migrate ``` <br />
. Run ``` symfony console doctrine:fixtures:load ``` <br />
route:-  /micro-post <br />