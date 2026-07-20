# 🌍 PortWatch AI

### Global Supply Chain Intelligence Dashboard

A modern web-based platform for monitoring global logistics, trade, weather, currency exchange, international ports, and supply chain risks using real-time APIs.

---

<p align="center">

Laravel • PHP • MySQL • Bootstrap • JavaScript • AJAX • Chart.js • Leaflet • REST API

</p>
# 🌍 PortWatch AI – Global Supply Chain Intelligence Dashboard

PortWatch AI adalah aplikasi web berbasis Laravel yang dirancang untuk membantu memantau kondisi rantai pasok (Supply Chain) secara global melalui integrasi berbagai API eksternal.

Website ini menyediakan informasi negara, pelabuhan internasional, cuaca, nilai tukar mata uang, berita logistik global, hingga analisis risiko yang ditampilkan dalam sebuah dashboard interaktif.

---

## 📌 Fitur Utama

### 🌎 Country Intelligence
- Data negara seluruh dunia
- Bendera negara
- Mata uang
- Bahasa
- Region
- Populasi
- Pencarian negara

---

### 🚢 Global Port Monitoring
- Dataset lebih dari **14.000 pelabuhan**
- Pencarian pelabuhan
- Informasi lokasi pelabuhan
- Negara
- Kode pelabuhan

---

### 🌤 Weather Monitor
- Informasi cuaca real-time
- Temperatur
- Kecepatan angin
- Kelembaban
- Kondisi cuaca

---

### 💱 Currency Monitor
- Nilai tukar mata uang dunia
- Perubahan kurs
- Monitoring ekonomi global

---

### 📰 News Intelligence
Mengambil berita logistik dunia secara real-time menggunakan **GNews API**

Menampilkan:

- Thumbnail berita
- Judul
- Media
- Tanggal
- Sentimen berita
- Link menuju artikel asli

---

### 📊 AI Risk Intelligence

Dashboard analisis yang menampilkan:

- Risk Score
- Supply Chain Signal
- Weather Impact
- Currency Impact
- Economic Indicator
- Timeline Monitoring

---

### ⭐ Watchlist

Pengguna dapat:

- Menyimpan negara favorit
- Monitoring lebih cepat
- Menghapus watchlist

---

### 👨‍💼 Admin Panel

Administrator dapat:

- Kelola User
- Kelola Artikel Analisis
- Kelola Watchlist
- Kelola Dataset Pelabuhan

---

## 🛠 Tech Stack

Backend

- Laravel 12
- PHP 8.2
- MySQL

Frontend

- Bootstrap 5
- JavaScript (ES6)
- AJAX
- Blade Template

Library

- Chart.js
- Leaflet.js

---

## 🌐 API yang Digunakan

- REST Countries API
- GNews API
- Open-Meteo API
- ExchangeRate API
- World Bank API

---

## 📸 Screenshot

*(Tambahkan screenshot dashboard di sini nanti setelah hosting.)*

---

## 🚀 Cara Menjalankan Project

Clone repository

```bash
git clone https://github.com/faharni0777-sys/portwatch-ai.git
```

Masuk folder project

```bash
cd portwatch-ai
```

Install dependency

```bash
composer install
```

Install Node

```bash
npm install
```

Copy environment

```bash
cp .env.example .env
```

Generate key

```bash
php artisan key:generate
```

Migrasi database

```bash
php artisan migrate
```

Build Vite

```bash
npm run build
```

Jalankan

```bash
php artisan serve
```

---

## 📂 Struktur Fitur

```
Dashboard
│
├── Country Intelligence
├── Port Monitor
├── Weather Monitor
├── Currency Monitor
├── News Intelligence
├── Risk Intelligence
├── Watchlist
└── Admin Panel
```

---

## 👩‍💻 Developer

**Riezky Maharani**

Sistem Informasi

Universitas Malikussaleh

2026

---

## 📄 License

Project ini dibuat sebagai tugas Ujian Akhir Semester (UAS) Mata Kuliah Pemrograman Web.
![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple)
![License](https://img.shields.io/badge/License-UAS-green)