Pastikan sudah terinstall:
- PHP 8.2 atau lebih baru
- Composer
- Node.js
- MySQL atau SQLite

## Cara Install

### 1. Download/Clone Project

```bash
git clone <url-repository>
cd "Tes Teknis Laravel"
```
### 2. Set up Backend
```bash
cd backend
```
```bash
composer install
```
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```
```bash
touch database/database.sqlite
```
```bash
php artisan migrate
```
```bash
php artisan storage:link
```
```bash
php artisan pokemon:fetch
```
# Jalankan server backend
```bash
php artisan serve
```
### 3. Set up Frontend
```bash
cd frontend
```
```bash
npm install
```
# Jalankan frontend
```bash
ng serve
```

### Bisa juga jalankan lewat folder utama projek ini
```bash
cd Tes Teknis Laravel
```
```bash
npm run dev
```

### Folder gambar berada di storage/app/public/pokemon_images
