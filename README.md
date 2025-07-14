# Srvkit Auth

![Latest Version](https://img.shields.io/github/v/release/rahulsharmagg/srvkit-auth?label=version)
![CI4 Compatibility](https://img.shields.io/badge/CodeIgniter4-%5E4.4-blue?logo=codeigniter)
[![License: MIT](https://img.shields.io/badge/License-MIT-orange.svg)](https://opensource.org/licenses/MIT)
![Issues](https://img.shields.io/github/issues/rahulsharmagg/srvkit-auth)
![Stars](https://img.shields.io/github/stars/rahulsharmagg/srvkit-auth?style=social)

**Srvkit Auth** is a plug-and-play authentication system for CodeIgniter 4, designed to be developer-friendly, cleanly structured, and easy to extend.  
It supports login, registration, password reset, token-based auth, and is flexible enough for JWT or session-based use.

---

## ✨ Features

- 🚀 Simple & **advance** plug-in for CI4 (Composer-ready)
- 🔐 Login / Register / Forgot Password / Reset Password
- 🔁 Refresh token + Access token logic
- 📱 Multi-device refresh token handling
- 🪪 JWT and opaque token support
- 📩 Magic Link support (Forgot Password, Email Login)
- 🧱 Modular: Easily extensible via services, traits, and config
- 🌗 API and web (form) both supported

---

## 📦 Installation

1. Insall by composer:
```bash
composer require srvkit/auth
```

2. Publish config and views:
```bash
php spark auth:setup
```

3. Run migrations:
```bash
php spark migrate --all
```