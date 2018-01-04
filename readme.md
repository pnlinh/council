# Council - OSS Forum

> Larcasts OSS Demo

## Installation

### Step 1.

> To run this project, you must have PHP 7 installed as a prerequisite.

Begin by cloning this repository to your machine, and installing all Composer dependencies.

```bash
git clone git@github.com:cashlion/council.git
cd council && composer install
mv .env.example .env
php artisan key:generate
```

### Step 2.

Next, create a new database and reference its name and username/password within the project's `.env` file. In the example below, we've named the database, "council."

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=council
DB_USERNAME=root
DB_PASSWORD=
```

Then, migrate your database to create tables.

```
php artisan migrate
```

### Step 3.

reCAPTCHA is a Google tool to help prevent forum spam. You'll need to create a free account (don't worrry, it's easy).

https://www.google.com/recaptcha/intro/

Choose reCAPTCHA V2, and specify your local (and eventually production) domain name, as illustrated in the image below.

![](https://i.imgur.com/2fhwUBJ.png)

Once submitted, you'll see two important keys that should be reference in your **.env** file.

```
RECAPTCHA_KEY=
RECAPTCHA_SECRET=
```

### Step 4.

Until an administration portal is available, manually insert any number of "channels" (think of these as forum categories) into the "channels" table in your database.

Once finished, clear your server cache, and you're all set to go!

```
php artisan cache:clear
```

### Step 5.

Use your forum! Visit `http://council.test/threads` to create a new account and publish your first thread.