# Game Library Tracker

A personal game library tracker built with CakePHP. Search for games via the
[RAWG.io](https://rawg.io/apidocs) API, add them to your library, and keep
track of what you're playing, what you've finished, and what's on your
wishlist.

Built as a portfolio project to demonstrate a full CRUD app with
authentication and external API integration.

## Tech stack

- PHP 8.2
- CakePHP 5.4
- SQLite (local development)
- RAWG.io API for game data

## Setup

1. Install dependencies:

   ```bash
   composer install
   ```

2. Copy the env example and add your RAWG API key (get one for free at
   [rawg.io/apidocs](https://rawg.io/apidocs)):

   ```bash
   cp config/.env.example config/.env
   ```

   Then edit `config/.env` and set `RAWG_API_KEY`.

3. Start the built-in dev server:

   ```bash
   bin/cake server -p 8765
   ```

4. Visit `http://localhost:8765`.

The SQLite database lives at `tmp/db/cakephp-portfolio.sqlite` and is created
automatically — no separate database server needed.

## Project board

Development is tracked on the `cakephp-portfolio` project board, organized
into epics: Project Setup & Auth, RAWG API Integration, Game Library CRUD,
UI/UX & Portfolio Polish, and Deployment.
