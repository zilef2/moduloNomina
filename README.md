# Screenshots
<p align="center">
  <!-- <img alt="Light" src="https://i.postimg.cc/HLjcWFM4/Snipaste-2022-12-26-13-47-15.png" width="45%">&nbsp; -->
  <img alt="Light" src="https://i.postimg.cc/Bbn8MZnv/carlos-preview-app1.png" width="95%">&nbsp;

</p>

# Features
- Role Based Access Control
- Responsive Design
- Modal Form
- Bulk Action
- Light/Dark Mode
- Toast Notification
- Rich Feature Datatable Serverside
- Tooltip
- Localization (EN/ID)
- SSR (Server Side Rendering)
# Requirements
- Php 8
- Composer
- Mysql
- Apache
# Installation
``` bash
git clone https://github.com/erikwibowo/Laravel-Brive.git
cd Laravel-Brive
composer update
npm install
cp .env.example .env
php artisan key:generate

SETTING UP DB CONNECTION IN .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=brive
DB_USERNAME=root
DB_PASSWORD=

php artisan migrate:fresh --seed

START THE SERVER
npm run dev
php artisan serve
```
## Login With
### Superadmin
``` bash
email : superadmin@superadmin.com
password : superadmin
```
### Admin
``` bash
email : admin@admin.com
password : admin
```
### Operator
``` bash
email : operator@operator.com
password : operator
```
# Packages
- [Vue](https://vuejs.org/)
- [Inertia](https://inertiajs.com/)
- [Tailwind](https://tailwindcss.com/)
- [Spatie](https://spatie.be/docs/laravel-permission/v5/introduction)
- [Floating Vue](https://floating-vue.starpad.dev/)
- [VueUse](https://vueuse.org/)
- [Hero Icons](https://heroicons.com/)
- [HeadlessUI](https://headlessui.com/)
# Build With
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>