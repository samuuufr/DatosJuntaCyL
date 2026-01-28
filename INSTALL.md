# Guía de Instalación Rápida

## ✅ Checklist de Instalación

Marca cada paso conforme lo completes:

- [ ] 1. Clonar repositorio
- [ ] 2. Instalar composer (`composer install`)
- [ ] 3. Instalar npm (`npm install`)
- [ ] 4. Copiar `.env.example` a `.env`
- [ ] 5. Configurar base de datos en `.env`
- [ ] 6. Generar app key (`php artisan key:generate`)
- [ ] 7. Crear base de datos en MySQL
- [ ] 8. Ejecutar migraciones (`php artisan migrate`)
- [ ] 9. Ejecutar seeders (`php artisan db:seed`)
- [ ] 10. Importar datos MNP (año por año)
- [ ] 11. Compilar assets (`npm run build`)
- [ ] 12. Iniciar servidor (`php artisan serve`)

## 🚀 Script de Instalación Automática

### Windows (PowerShell)

```powershell
# 1-3. Dependencias
composer install
npm install

# 4-6. Configuración
Copy-Item .env.example .env
# ⚠️ EDITAR .env MANUALMENTE con credenciales de BD
php artisan key:generate

# 7-9. Base de datos
# ⚠️ CREAR BD MANUALMENTE: CREATE DATABASE datosjuntacyl;
php artisan migrate
php artisan db:seed

# 10. Importar datos (15-20 minutos)
foreach ($year in 2020..2023) {
    Write-Host "Importando año $year..."
    php artisan mnp:import --ano-inicio=$year --ano-fin=$year
}

# 11. Assets
npm run build

# 12. Servidor
php artisan serve
```

### Linux/Mac (Bash)

```bash
#!/bin/bash

# 1-3. Dependencias
composer install
npm install

# 4-6. Configuración
cp .env.example .env
# ⚠️ EDITAR .env MANUALMENTE con credenciales de BD
php artisan key:generate

# 7-9. Base de datos
# ⚠️ CREAR BD MANUALMENTE: CREATE DATABASE datosjuntacyl;
php artisan migrate
php artisan db:seed

# 10. Importar datos (15-20 minutos)
for year in 2020 2021 2022 2023; do
    echo "Importando año $year..."
    php artisan mnp:import --ano-inicio=$year --ano-fin=$year
done

# 11. Assets
npm run build

# 12. Servidor
php artisan serve
```

## ⏱️ Tiempos Estimados

| Paso | Tiempo |
|------|--------|
| Instalación dependencias | 2-3 min |
| Migraciones y seeders | 10-20 seg |
| Importación MNP | 15-20 min |
| Compilación assets | 30 seg |
| **TOTAL** | **~20 minutos** |

## 🔍 Verificación Post-Instalación

```bash
# Verificar datos importados
php artisan tinker --execute="
echo 'Nacimientos: ' . \App\Models\DatoMnp::where('tipo_evento', 'nacimiento')->count() . PHP_EOL;
echo 'Defunciones: ' . \App\Models\DatoMnp::where('tipo_evento', 'defuncion')->count() . PHP_EOL;
echo 'Matrimonios: ' . \App\Models\DatoMnp::where('tipo_evento', 'matrimonio')->count() . PHP_EOL;
echo 'Total: ' . \App\Models\DatoMnp::count();
"
```

**Esperado:**
```
Nacimientos: 3976
Defunciones: 6763
Matrimonios: 2723
Total: 13462
```

## ❌ Solución de Problemas

### "Class 'PDO' not found"
```bash
# Verificar extensión PDO en php.ini
php -m | grep PDO
```

### "npm ERR! ERESOLVE"
```bash
npm install --legacy-peer-deps
```

### "SQLSTATE[HY000] [1049]"
```bash
# Crear base de datos manualmente
mysql -u root -p -e "CREATE DATABASE datosjuntacyl;"
```

## 📞 Soporte

Si encuentras problemas no listados aquí, consulta el [README.md](README.md) completo.
