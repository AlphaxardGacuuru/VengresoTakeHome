<!-- @format -->

<p align="center">
		<img src="react-vengreso/public/logo512.png" width="400">
</p>

## About Vengreso Chrome Extension

This is a chrome extension that counts divs on specific websites. A sticky element hovers on websites that the extension is setup to count. It is built with React 18 (Extension), Angular 11 (Dashboard displaying data about number of divs collected per site) and Laravel 9 API for the backend and websockets (laravel websockets via Pusher)

## Laravel 9 Setup

In one terminal once in the project root folder

```
- •	cd Laravel
- •	composer install
- •	cp .env.example to .env
- •	change username and password in .env
- •	php artisan key:generate
- •	php artisan migrate
- •	php artisan db:seed // Important for creating user and data for graphs
- •	php artisan serve
```

The Laravel API will run on localhost:8000.
Next run the laravel's queue worker, in another terminal

```
- •	php artisan queue:listen -v
```

Next run laravel websockets

```
- •	php artisan websockets:serve
```

The websocket will run on localhost:6001

## Angular 11 Setup

```
- •	npm install
- •	ng serve
```

Should run on localhost:4200.
A login popup will appear with prefilled login credential (Email and Password - johndoe@gmail.com), login is per tab session, the moment the tab is closed the session ends. Login is need for Websocket Authentication, the app uses websockets to enable realtime data relay.

## Extension installation

The build folder contains the chrome extension, simply extract it, go to chrome extensions with developer mode set, then click on load unpacked and select the build folder. The extension is meant to work on ("https://developer.chrome.com/docs/extensions/*",
"https://developer.chrome.com/docs/webstore/*",
"https://x.com/*").
Once installed, visit the included websites, a hovering element should appear displaying the number of divs on the page, click the extension icon, a popup will appear with a button "Count Divs", click on it to count and save the data.

## Testing Laravel API

In another terminal inside the Laravel directory

```
- •	created database with name "vengreso_chrome_extension_test"
- •	cp .env.testing.example .env.testing
- •	change username and password in .env
- •	php artisan test
```
