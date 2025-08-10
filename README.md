
# Workcity Chat

Workcity Chat is a real-time chat application built with **Laravel 11** and **Laravel Echo** for real-time communication, enabling users to create conversations, send messages, and manage profiles.

## Features

- **Real-time messaging** using Laravel Echo and Pusher
- **User authentication** (sign up, login, and logout)
- **Profile management** (update user details, password, and avatar)
- **Conversation management** (create and manage conversations)
- **File uploads** (share files within conversations)
- **Typing indicators** for real-time updates
- **Search functionality** to find conversations and messages

---

## Installation

Follow these steps to set up the project locally.

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js (for managing frontend assets)
- MySQL or another database

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/workcity-chat.git
cd workcity-chat
```

### 2. Install Backend Dependencies

Run the following command to install all PHP dependencies using **Composer**:

```bash
composer install
```

### 3. Set Up Environment File

Duplicate the `.env.example` file and rename it to `.env`:

```bash
cp .env.example .env
```

Then, open the `.env` file and configure the following:

- **APP_NAME**, **APP_URL**: Set your application name and URL.
- **DB_CONNECTION**, **DB_HOST**, **DB_PORT**, **DB_DATABASE**, **DB_USERNAME**, **DB_PASSWORD**: Set your database credentials.
- **BROADCAST_DRIVER**: Set to `pusher` for real-time broadcasting.
- **PUSHER_APP_ID**, **PUSHER_APP_KEY**, **PUSHER_APP_SECRET**, **PUSHER_APP_CLUSTER**: Set your Pusher credentials (you can get these from Pusher's dashboard).

### 4. Generate Application Key

Run the following command to generate a new application key:

```bash
php artisan key:generate
```

### 5. Set Up the Database

Run the migration command to create the necessary tables:

```bash
php artisan migrate
```

You can also seed the database with test data:

```bash
php artisan db:seed
```

### 6. Set Up File Storage

Create a symbolic link to store uploaded files:

```bash
php artisan storage:link
```

### 7. Install Frontend Dependencies

Navigate to the `frontend` folder and run the following commands to install Node.js dependencies:

```bash
cd frontend
npm install
```

Then, compile the assets:

```bash
npm run dev
```

### 8. Run the Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at **http://127.0.0.1:8000**.

---

## Usage

### Authentication

- **Sign Up**: Users can create an account by providing their name, email, and password.
- **Login**: Users can log in using their email and password.
- **Logout**: Users can log out from the application.

### Messaging

- **Conversations**: Users can create new conversations and send messages to each other.
- **File Uploads**: Users can upload files (images, documents) in a conversation.
- **Typing Indicators**: When a user is typing, other users in the conversation will see a real-time typing indicator.

### Profile Management

- **View Profile**: Users can view their profile and update their information (name, email, and password).
- **Avatar**: Users can upload or update their profile picture.

### Real-time Updates

- **Laravel Echo and Pusher**: The chat system uses **Pusher** for broadcasting messages and typing indicators in real time.

---

## File Structure

```
/app
    /Models                # Eloquent models for the application
    /Http
        /Controllers       # Controllers for handling requests
        /Middleware        # Custom middleware (e.g., for role-based access)
    /Notifications         # Notification classes for sending messages and updates
    /Events                # Events for broadcasting real-time actions (e.g., new message)
    /Broadcasting          # Channels for broadcasting events (e.g., chat channels)
/resources
    /views                 # Blade templates for frontend (e.g., chat view, profile page)
/routes
    web.php                # All application routes
```

---

## Environment Configuration

Make sure to configure the following environment variables in your `.env` file:

```
APP_NAME=Workcity Chat
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=workcity_chat
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_KEY=your-pusher-app-key
PUSHER_APP_SECRET=your-pusher-app-secret
PUSHER_APP_CLUSTER=mt1
```

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---
