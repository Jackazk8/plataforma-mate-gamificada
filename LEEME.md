# MathApp - Paso 1: arreglar la base

## Estructura de carpetas
```
mathapp/
├── index.php            (login único)
├── php/
│   ├── conexion.php     (NO se sube a GitHub; usa conexion.example.php como modelo)
│   ├── dashboard.php
│   ├── juego.php
│   ├── responder.php    (nuevo: valida la respuesta y guarda los puntos)
│   └── logout.php
├── css/   estilos.css, juego.css
├── js/    juego.js
└── sql/   migracion_01.sql
```

## Cómo instalarlo
1. Copia estos archivos a tu carpeta del proyecto (en XAMPP: `htdocs/mathapp`). **Borra `php/login.php`**, ya no se usa.
2. En phpMyAdmin, base `math_app` > pestaña **SQL** > pega `sql/migracion_01.sql` y ejecuta.
3. Abre `http://localhost/mathapp/` y entra con `alumno@mathapp.com` / `123456`.
   La primera vez, tu contraseña se guarda automáticamente cifrada.
