# 🚀 Backend del Portafolio 2025

[![PHP](https://img.shields.io/badge/PHP-8.1+-777bb4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)
[![Made with ❤️ in WSL](https://img.shields.io/badge/Made%20with-WSL2-blue?style=flat-square&logo=ubuntu)](https://docs.microsoft.com/en-us/windows/wsl/)

Este es el backend de mi portafolio personal desarrollado en PHP, corriendo sobre Apache en entorno Linux (WSL). Implementa buenas prácticas de seguridad y organización, ideal para entornos productivos y de desarrollo.

---

## 📂 Estructura del Proyecto

| Carpeta / Archivo | Descripción                                     |
| ----------------- | ----------------------------------------------- |
| `api/`            | Endpoints de la API REST                        |
| `config/`         | Configuración (base de datos, constantes, etc.) |
| `helpers/`        | Funciones reutilizables                         |
| `vendor/`         | Dependencias de Composer _(ignorado en Git)_    |
| `.env`            | Variables de entorno _(ignorado en Git)_        |
| `.gitignore`      | Exclusiones para Git                            |
| `composer.json`   | Definición de dependencias PHP                  |
| `index.html`      | Página base o de redirección                    |
| `.htaccess`       | Configuración para Apache                       |

---

## ⚙️ Requisitos

- PHP `8.1` o superior
- Composer
- Apache
- MySQL o MariaDB
- WSL2 (si usás Windows)
- Docker (opcional)

---

## 🛠️ Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/tuusuario/backend-portfolio.git
cd backend-portfolio

# 2. Instalar dependencias
composer install

# 3. Configurar el entorno
cp .env.example .env
# Editá los valores:
# DB_HOST=localhost
# DB_NAME=portfolio
# DB_USER=root
# DB_PASS=secret
# JWT_SECRET=clave_secreta

# 4. Levantar el servidor
sudo service apache2 start
```

Verificá el acceso en [http://localhost](http://localhost)

---

## 🔐 Seguridad

✅ Autenticación por JWT  
✅ Protección de endpoints con API Key  
✅ `.env` y `vendor/` excluidos de Git  
✅ Organización modular y clara

---

## 👨‍💻 Autor

**Ivan Tarquini**  
GitHub: [@OrnluX](https://github.com/OrnluX)

---

## 🧾 Licencia

Distribuido bajo la licencia MIT.  
¡Libre para usar, mejorar y compartir! 👐
