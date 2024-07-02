# Proyecto Club Deportivo "LooneyTunes"

Este proyecto fue desarrollado para el club deportivo "LooneyTunes" de la ciudad de Ibarra. Se trabajó bajo cuatro módulos diferentes, cada uno diseñado para un tipo específico de usuario. La metodología de desarrollo utilizada fue Scrum.

## Módulos

### Administrador
Este módulo permite a los administradores del club gestionar todos los aspectos del mismo, incluyendo la administración de usuarios, eventos y recursos del club.

### Entrenador
El módulo de entrenador está diseñado para que los entrenadores puedan planificar y seguir las sesiones de entrenamiento, así como evaluar el rendimiento de los deportistas.

### Representante
Este módulo permite a los representantes de los deportistas (por ejemplo, padres o tutores) monitorear el progreso y la participación de los deportistas en el club.

### Deportista
El módulo de deportista proporciona a los miembros del club acceso a su programación de entrenamientos, su rendimiento, y otros recursos del club.

## Metodología

El desarrollo del proyecto se realizó utilizando la metodología Scrum, la cual se basa en iteraciones cortas llamadas sprints, permitiendo una mejor organización y adaptación a los cambios en los requerimientos del proyecto.

## Instalación

1. Clonar el repositorio:
    ```sh
    git clone https://github.com/tuusuario/proyecto-looneytunes.git
    ```

2. Navegar al directorio del proyecto:
    ```sh
    cd proyecto-looneytunes
    ```

3. Instalar las dependencias:
    ```sh
    composer install
    npm install
    ```

4. Configurar el archivo `.env` con los detalles de la base de datos y otras configuraciones necesarias.

5. Ejecutar las migraciones y sembradores de base de datos:
    ```sh
    php artisan migrate --seed
    ```

6. Iniciar el servidor de desarrollo:
    ```sh
    php artisan serve
    npm run dev
    ```

## Uso

Para acceder a los diferentes módulos, los usuarios deben iniciar sesión con sus credenciales correspondientes. Dependiendo del rol asignado, se les otorgará acceso a las funcionalidades específicas de su módulo.

## Contribuciones

Las contribuciones al proyecto son bienvenidas. Por favor, sigue los siguientes pasos:

1. Haz un fork del repositorio.
2. Crea una nueva rama (`git checkout -b feature/nueva-funcionalidad`).
3. Realiza tus cambios y haz commits (`git commit -m 'Agrega nueva funcionalidad'`).
4. Sube tu rama (`git push origin feature/nueva-funcionalidad`).
5. Abre un Pull Request.

## Licencia

Este proyecto está licenciado bajo la [MIT License](LICENSE).
