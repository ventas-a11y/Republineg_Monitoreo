# 03: Diseño del panel según el mockup

**What to build:** Al entrar al panel, el usuario ve la apariencia y la navegación del mockup (https://claude.ai/artifact/WNNqTGzFbU3zsacWLvDHjV): paleta cálida, fuente Inter, modo claro y oscuro, y el menú lateral con Resumen, Equipos, Actividad, Capturas, Alertas, Reportes y Configuración. Por ahora las pantallas están vacías.

**Blocked by:** None (can start immediately)

**Status:** ready-for-human

- [x] Los tokens de color oklch del mockup reemplazan a los del starter kit, en claro y en oscuro (incluye colores de estado y de la cinta de jornada)
- [x] Fuente Inter, con su fallback (servida desde el propio build, sin llamadas externas en tiempo de ejecución)
- [x] Navegación lateral del mockup sobre el layout del starter kit, responsive, con menú en móvil
- [x] El selector de apariencia existente sigue funcionando
- [x] Los textos usan el vocabulario del [GLOSSARY](../../../GLOSSARY.md): "equipo" solo para la computadora (D40)
- [x] Se revisa con `web-design-guidelines` y accesibilidad (foco visible, contraste); hallazgos corregidos el 2026-10-16
- [ ] Revisión visual en el navegador (claro/oscuro, móvil/escritorio)

## Notas (2026-10-16)

- **Menú:**
    - grupo **Monitoreo**: Resumen, Equipos, Actividad (Línea de tiempo, Apps y sitios), Capturas, Alertas y Reportes;
    - grupo **Configuración**: Cuentas y Áreas, solo para Administradores;
    - el Empleado solo ve Resumen.
- **Pantallas de monitoreo:** son estados vacíos que indican qué ticket las completa. Las protege el permiso `viewMonitoring` (Revisor y Administrador; 404 para un Empleado) y están en la matriz de autorización.
- **Logo:** se reemplazó el de Laravel por uno propio (pantalla con pulso).
- **Aviso:** el menú incluye "Monitoreo transparente", como en el mockup.
- **Idioma:** la interfaz quedó en español (`lang/es` y pantallas del starter kit).
- **Tema (2026-10-17):** el usuario eligió el preset de shadcn `b2CjejDL0` (neutros oliva, primario ámbar, fuente Montserrat, radio 10 px).
  - Se aplicó solo el tema: los componentes siguen en estilo `new-york`, sin dependencias nuevas, y Montserrat se sirve desde el build.
  - Los colores de estado y de la cinta de jornada se mantienen del mockup, porque el preset no los define.
  - La interfaz se compactó: la escala base pasó al 93,75 % (15 px).
  - El mockup sigue siendo la referencia de estructura y pantallas.
