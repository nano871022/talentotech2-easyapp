# Language Advisory Platform

This project is a web application for requesting language advisory services. It is built with a microservices architecture, using Angular for the frontend and PHP for the backend.

## Architecture Overview

The application is divided into the following services:

-   **Frontend:** An Angular single-page application that provides the user interface.
-   **Auth Service:** A PHP microservice that handles user authentication and registration.
-   **Advise Service:** A PHP microservice that handles advisory requests.
-   **Nginx Proxy:** An Nginx reverse proxy that routes requests to the appropriate backend service.
-   **MySQL Database:** A MySQL database that stores the application data.
-   **Static Assets Server:** An Nginx server that serves static assets.
-   **phpMyAdmin:** A web-based interface for managing the MySQL database.

## Local Development Setup

### Prerequisites

-   Docker
-   Docker Compose

### Getting Started

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/your-repository.git
    cd your-repository
    ```

2.  **Create a `.env` file:**
    ```bash
    cp .env.example .env
    ```
    Update the `.env` file with your desired configuration.

3.  **Build and run the application:**
    ```bash
    docker-compose up --build -d
    ```

4.  **Access the application:**
    -   **Frontend:** `http://localhost:4200`
    -   **Backend API:** `http://localhost:8090`
    -   **phpMyAdmin:** `http://localhost:8082`

### Testing

-   **Frontend:**
    ```bash
    docker-compose exec app-frontend-nginx npm test
    ```

-   **Backend:**
    There are no automated tests for the backend services yet.

## Deployment

The application is designed to be deployed to a cloud environment, such as AWS. The backend services can be deployed as AWS Lambda functions, and the frontend can be deployed to an S3 bucket.

### CI/CD

The project includes GitHub Actions workflows for continuous integration and deployment. The workflows are configured to be triggered on pushes to the `main` branch.

-   **Frontend:** Deploys the Angular application to an S3 bucket.
-   **Auth Service:** Deploys the authentication service to an AWS Lambda function.
-   **Advise Service:** Deploys the advise service to an AWS Lambda function.

### Kubernetes

The project also includes Kubernetes manifests for deploying the backend services to a Kubernetes cluster. The manifests are located in the `kubernetes` directory.
