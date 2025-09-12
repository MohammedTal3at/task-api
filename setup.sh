#!/bin/bash

# A script to set up a Laravel Sail API-ONLY environment on a new machine.

# --- Style Functions ---
print_info() {
    printf "\e[34m\n[INFO] %s\e[0m\n" "$1"
}

print_success() {
    printf "\e[32m\n[SUCCESS] %s\e[0m\n" "$1"
}

print_error() {
    printf "\e[31m\n[ERROR] %s\e[0m\n" "$1" >&2
}

# --- Check for Docker ---
if ! command -v docker &> /dev/null || ! docker info &> /dev/null; then
    print_error "Docker is not installed or not running. Please install and start Docker before running this script."
    exit 1
fi

# --- Main Setup ---
print_info "Starting Laravel Sail API setup..."

# Step 1: Copy environment file
if [ ! -f .env ]; then
    print_info "Creating .env file from .env.example..."
    cp .env.example .env
    if [ $? -ne 0 ]; then
        print_error "Failed to copy .env.example. Please check permissions."
        exit 1
    fi
else
    print_info ".env file already exists. Skipping."
fi

# Step 2: Define Sail alias for this script session
SAIL_CMD="./vendor/bin/sail"

# Step 3: Build and start Sail containers
print_info "Building and starting Sail containers in the background..."
$SAIL_CMD up -d --build
if [ $? -ne 0 ]; then
    print_error "Sail containers failed to start. Check docker-compose.yml and Docker logs."
    exit 1
fi

# Step 4: Wait a moment for services (especially MySQL) to be ready
print_info "Waiting for database container to be ready..."
sleep 15

# Step 5: Install Composer dependencies
print_info "Installing Composer dependencies inside the container..."
$SAIL_CMD composer install --no-interaction
if [ $? -ne 0 ]; then
    print_error "Composer install failed."
    exit 1
fi

# Step 6: Generate Application Key
print_info "Generating application key..."
$SAIL_CMD artisan key:generate
if [ $? -ne 0 ]; then
    print_error "Failed to generate application key."
    exit 1
fi

# Step 7: Run database migrations
print_info "Running database migrations..."
$SAIL_CMD artisan migrate
if [ $? -ne 0 ]; then
    print_error "Database migrations failed. Check your .env database credentials and container logs."
    exit 1
fi


print_success "Setup complete! Your Laravel application is running."
print_info "You can access it at http://localhost:8008". (Replace 8008 with your APP_PORT if different)
print_info "Use './vendor/bin/sail up -d' to start and './vendor/bin/sail stop' to stop."