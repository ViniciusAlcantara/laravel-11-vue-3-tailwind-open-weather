# Weather App

A web application built with Vue.js, Laravel, Tailwind and SQLite that allows users to fetch and save weather forecasts for their favorite locations.

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Requirements](#requirements)
- [Getting Started](#getting-started)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Database Setup](#database-setup)
  - [Running the Application](#running-the-application)
- [Usage](#usage)
- [Testing](#testing)
- [API Reference](#api-reference)
- [License](#license)

## Features

1. **User Authentication**: Pre-registered users can log in using their email and password.
2. **Weather Search**: Users can input a location to fetch the weather forecast.
3. **Save Locations**: Users can save up to three locations, which are stored in the database along with the forecast.
4. **Manage Locations**: Users can remove saved locations.
5. **Personalized Data**: Each user's saved locations and forecasts are isolated from other users.
6. **Persistent Forecasts**: Saved forecasts are loaded upon login.
7. **Weather Icons**: The app displays icons representing the current weather conditions.
8. **Automated Testing**: Backend endpoints are tested using PHPUnit.

## Technologies Used

- **Frontend**: Vue.js 3
- **Backend**: PHP 8+, Laravel 11+
- **Database**: SQLite
- **API**: OpenWeatherMap API
- **Testing**: PHPUnit

## Requirements

- PHP 8.0 or higher
- Composer
- Node.js and npm
- SQLite
- Git
- OpenWeatherMap API Key (free account)

## Getting Started

### Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/ViniciusAlcantara/laravel-11-vue-3-tailwind-open-weather.git
   cd weather-app
   ```

2. **Install Backend Dependencies**

   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**

   ```bash
   npm install
   ```

### Configuration

1. **Environment Variables**

   Copy the `.env.example` file to `.env`:

   ```bash
   cp .env.example .env
   ```

2. **Set Application Key**

   ```bash
   php artisan key:generate
   ```

3. **Set API Key**

   Add your OpenWeatherMap API key to the `.env` file:

   ```env
   OPEN_WEATHER_APPID=your_openweather_api_key
   ```

### Database Setup

1. **Run Migrations**

   ```bash
   php artisan migrate
   ```

2. **Seed the Database**

   The application requires two pre-registered users. Run the seeder to create them:

   ```bash
   php artisan db:seed
   ```

   - **User Credentials**:

     - **User 1**
       - Email: `test-user@test.com`
       - Password: `password`
     - **User 2**
       - Email: `test-user2@test.com`
       - Password: `password`

### Running the Application

1. **Start the Backend Server**

   ```bash
   php artisan serve
   ```

2. **Compile Frontend Assets**

   ```bash
   npm run dev
   ```

## Usage

1. **Access the Application**

   Open your web browser and navigate to `http://localhost:8000`.

2. **Login**

   Use one of the pre-registered user accounts to log in.

3. **Add a Location**

   - Navigate to the "Add Location" form.
   - Input the location.
   - Submit to fetch and save the forecast.

4. **View Saved Locations**

   - Your saved locations and their forecasts will be displayed on the dashboard.
   - Weather icons representing the current conditions will be shown.

5. **Remove a Location**

   - Click the "Remove Location" button next to a saved location to delete it.

## Testing

Automated tests are written using PHPUnit.

1. **Run Tests**

   ```bash
   php artisan test
   ```

2. **Test Coverage**

   The tests cover the weather fetching endpoint and ensure data is correctly saved and associated with users.

Feel free to contribute to this project by opening issues or submitting pull requests.
