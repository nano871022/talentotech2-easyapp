# talentotech2-easyapp

Proyecto curto talento tech2 para arquitectura en la nube taller1

## Docker Container Services

This project uses Docker Compose to manage its services. Below is a description of each container, what it does, and how to interact with it.

### 1. `db` (MySQL Database)

-   **Image:** `mysql:8.0`
-   **Container Name:** `mysql-db`
-   **Purpose:** This service runs the MySQL database, which stores all the application data.
-   **How to Interact:**
    -   **Connect via CLI:** You can access the MySQL command line within the container using the following command:
        ```bash
        docker exec -it mysql-db mysql -u${DB_USER} -p${DB_PASS} ${DB_NAME}
        ```
    -   **View Logs:** To see the database logs, run:
        ```bash
        docker logs mysql-db
        ```

### 2. `app-backend` (PHP-FPM Application)

-   **Container Name:** `app-backend-php`
-   **Purpose:** This is the backend application server running PHP-FPM. It executes the application logic and connects to the database.
-   **How to Interact:**
    -   **Access Shell:** To get a shell inside the container (e.g., to run Composer or other commands), use:
        ```bash
        docker exec -it app-backend-php /bin/bash
        ```
    -   **View Logs:** The logs for this service are typically viewed through the `nginx-backend` service, which proxies requests to it.

### 3. `nginx-backend` (NGINX Reverse Proxy for Backend)

-   **Image:** `nginx:alpine`
-   **Container Name:** `app-backend-nginx`
-   **Purpose:** This NGINX server acts as a reverse proxy for the PHP backend. It receives requests from the outside world (on port `${BACKEND_PORT}`) and forwards them to the `app-backend` (PHP-FPM) service for processing.
-   **How to Interact:**
    -   **Make API Requests:** You can send API requests to `http://localhost:${BACKEND_PORT}`. For example, using `curl`:
        ```bash
        curl http://localhost:8080/api/v1/requests
        ```
    -   **View Logs:** To check for request logs or errors, run:
        ```bash
        docker logs app-backend-nginx
        ```

### 4. `app-frontend` (Angular Frontend)

-   **Container Name:** `app-frontend-nginx`
-   **Purpose:** This service serves the compiled Angular single-page application (SPA). An NGINX server is used to deliver the static HTML, CSS, and JavaScript files to the user's browser.
-   **How to Interact:**
    -   **Access the App:** Open your web browser and navigate to `http://localhost:${FRONTEND_PORT}` (e.g., `http://localhost:4200`).
    -   **View Logs:** To see the NGINX access logs for the frontend, use:
        ```bash
        docker logs app-frontend-nginx
        ```

### 5. `static-server` (Static Assets Server)

-   **Image:** `nginx:alpine`
-   **Container Name:** `static-assets-server`
-   **Purpose:** This container simulates a Content Delivery Network (CDN) by serving static assets like images, documents, or other files located in the `./static-assets` directory.
-   **How to Interact:**
    -   **Access Assets:** Files can be accessed in your browser at `http://localhost:${STATIC_ASSETS_PORT}/<filename>`.
    -   **View Logs:**
        ```bash
        docker logs static-assets-server
        ```

### 6. `phpmyadmin` (Database Management)

-   **Image:** `phpmyadmin/phpmyadmin`
-   **Container Name:** `phpmyadmin-container`
-   **Purpose:** This service provides a web-based interface (phpMyAdmin) for managing the MySQL database. It is configured to connect directly to the `db` service.
-   **How to Interact:**
    -   **Access Web UI:** Open your browser and go to `http://localhost:${PHPMYADMIN_PORT}` (e.g., `http://localhost:8082`).
    -   **Login:** Use the database credentials from your `.env` file to log in (`DB_USER` and `DB_PASS`).
    -   **View Logs:**
        ```bash
        docker logs phpmyadmin-container
        ```