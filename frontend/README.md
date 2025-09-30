# Frontend - Language Advisory Angular SPA

This directory contains the Angular Single Page Application (SPA) for the Language Advisory Platform. It is built with a modern, modular architecture and uses Bootstrap and Angular Material for styling.

## Architecture

The application is structured into feature modules to support lazy loading and a clean separation of concerns.

-   **`core/`**: Contains singleton services and models that are used across the entire application, such as `AuthService` and `RequestService`.
-   **`home/`**: The public-facing module. It includes the `LandingPageComponent` and the `RegisterPageComponent` for new user sign-ups.
-   **`auth/`**: The authentication module. It handles the `LoginComponent` for administrator access.
-   **`dashboard/`**: The main application module for authenticated users. It contains the `DashboardLayoutComponent` which displays the list of advisory requests.
-   **`environments/`**: Contains environment-specific configuration, such as the API base URLs.

## Key Components

-   **`LandingPageComponent`**: The main welcome page for visitors.
-   **`RegisterPageComponent`**: The form where prospective clients can request an advisory session.
-   **`LoginComponent`**: The form for administrators to log in.
-   **`DashboardLayoutComponent`**: The main view for administrators after logging in, displaying a table of all advisory requests.

---

## Communication with Backend and Static Assets

The frontend is completely decoupled from the backend and static assets, simulating a modern cloud environment.

-   **Backend API Communication:**
    -   All HTTP requests to the backend are managed by services located in `src/app/core/services/`.
    -   The base URL for the API is configured in the `src/environments/` files (`API_BASE_URL`). This allows for easy switching between development (`http://localhost:8080/api/v1`) and production environments.

-   **Static Assets (CDN/Bucket Simulation):**
    -   All static assets (images, global CSS, fonts) should be placed in the `static-assets/` directory in the project root.
    -   These assets are served by a dedicated NGINX container (`static-server`) on port `8081`.
    -   The URL for these assets is configured in `src/environments/` (`STATIC_ASSETS_URL`), allowing components to reference them dynamically (e.g., `<img [src]="staticAssetsUrl + 'logo.png'">`).

---

## Local Execution & Development

To run the frontend for local development, you have two options:

### 1. Using Docker (Recommended for Full System)

Running the frontend via the root `docker-compose.yml` is the best way to test the full application, as it orchestrates all services together.

1.  **Build the Angular App:** Before starting the containers, you must build the Angular application so the `app-frontend` NGINX container has files to serve. Run the following command from the **root directory**:
    ```bash
    (cd frontend && npm install && npm run build)
    ```
2.  **Start Docker Compose:** From the **root directory**, run:
    ```bash
    docker-compose up -d
    ```
3.  **Access the Application:** The frontend will be available at `http://localhost:4200`.

### 2. Using Angular CLI (For Frontend-Only Development)

If you are only working on the frontend and the backend is already running, you can use the standard Angular CLI development server for a better hot-reloading experience.

1.  **Navigate to the Frontend Directory:**
    ```bash
    cd frontend
    ```
2.  **Install Dependencies:**
    ```bash
    npm install
    ```
3.  **Start the Development Server:**
    ```bash
    npm start
    ```
4.  **Access the Application:** The frontend will be available at `http://localhost:4200`. The development server will automatically proxy API requests if a `proxy.conf.json` file is configured.