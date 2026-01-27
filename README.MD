## Start development

```bash
cp ./php/.env.local ./php/.env \
&& cp ./docker/docker-compose.local.yml ./docker-compose.yml \
&& make build \
&& make up \
&& ./composer install \
&& ./artisan key:generate
```