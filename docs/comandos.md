# Hoja de comandos 🐳

## Imágenes
| Comando | Qué hace |
|---|---|
| `docker pull mariadb:11` | Descarga una imagen |
| `docker images` | Lista imágenes locales |
| `docker build -t nombre .` | Construye una imagen desde el `Dockerfile` |
| `docker rmi nombre` | Elimina una imagen |

## Contenedores
| Comando | Qué hace |
|---|---|
| `docker run -d -p 8080:80 --name app imagen` | Crea y arranca un contenedor |
| `docker ps` / `docker ps -a` | Contenedores activos / todos |
| `docker logs -f app` | Ver logs en vivo |
| `docker exec -it app bash` | Entrar al contenedor |
| `docker stop app` / `docker start app` | Detener / iniciar |
| `docker rm -f app` | Eliminar (aunque esté corriendo) |

## Docker Compose
| Comando | Qué hace |
|---|---|
| `docker compose up -d` | Levanta todos los servicios |
| `docker compose up -d --build` | Reconstruye la imagen y levanta |
| `docker compose ps` | Estado de los servicios |
| `docker compose logs -f app` | Logs de un servicio |
| `docker compose exec db mariadb -uroot -psecreto` | Consola de la BD |
| `docker compose down` | Apaga y elimina contenedores (los volúmenes quedan) |
| `docker compose down -v` | Lo mismo, pero borra también los volúmenes (datos) |

## Limpieza
| Comando | Qué hace |
|---|---|
| `docker system df` | Cuánto espacio usa Docker |
| `docker system prune` | Borra lo que no se usa (con cuidado) |
