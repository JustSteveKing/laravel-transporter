# Changelog

All notable changes to `Transporter` will be documented in this file.

## 0.4.0 - 2021-05-28

This release is a major refactor where a lot of the code has completely disappeared. Instead the Transporter is no longer needed, as we are simply wrapping Laravels PendingRequest in a buildable API - allowing you to still access all of the methods you could do on a PendingRequest such as `withToken` and `asForm`. It also returns an `Illuminate\Http\Client\Response` so you have direct access to `json` `failed` `collect` and other methods available on the Response class.

## 0.3.0 - 2021-05-28

This release moves the artisan make command output from app/Http/API/Requests to app/Transporter/Requests as per a request in [#1](https://github.com/JustSteveKing/laravel-transporter/issues/1) which makes a lot of sense.

## 0.2.0 - 2021-05-27

Forcing the Request::with to return a Transporter.

## 0.1.0 - 2021-05-27

Initial Release of the package.
