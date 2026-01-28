# Guía Claude Code - Organización

## Archivos en Raíz
```
proyecto/
├── CLAUDE.md           ← Principal (contexto + tareas)
├── DATASET-LINKS.md    ← URLs y APIs
├── TECHNICAL-SPECS.md  ← Requisitos técnicos
├── README.md           ← Documentación usuarios
└── docs/               ← Memoria, presentación, vídeo
```

## CLAUDE.md - Contenido
1. Estado actual del proyecto
2. Objetivo y stack
3. Referencias a otros .md
4. Checklist tareas pendientes
5. Comandos útiles

## Uso en Claude Code

### Opción 1: Mensajes
```
Lee CLAUDE.md, DATASET-LINKS.md y TECHNICAL-SPECS.md.
Ayúdame a crear el script de importación de datos.
```

### Opción 2: Referencias @
```
@CLAUDE.md @DATASET-LINKS.md
Crea el endpoint API para nacimientos.
```

## Lectura Automática
- Claude Code lee `CLAUDE.md` automáticamente
- Otros archivos: referenciar explícitamente

## Tips
- Actualiza "Estado actual" en cada sesión
- Incluye enlaces rápidos en CLAUDE.md
- Detalle técnico en archivos separados
- Commits frecuentes con cambios
