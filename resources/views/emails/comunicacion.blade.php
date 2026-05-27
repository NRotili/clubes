<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body { margin: 0; padding: 0; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
  .wrapper { max-width: 560px; margin: 32px auto; }
  .header { background: #1e3a5f; border-radius: 12px 12px 0 0; padding: 28px 32px; text-align: center; }
  .header-nombre { color: #fff; font-size: 20px; font-weight: 700; margin: 0; }
  .header-sub { color: #93c5fd; font-size: 12px; margin: 4px 0 0; }
  .body { background: #fff; padding: 32px; }
  .saludo { font-size: 15px; color: #475569; margin: 0 0 20px; }
  .contenido { font-size: 15px; color: #0f172a; line-height: 1.7; white-space: pre-line; }
  .footer { background: #f8fafc; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 12px 12px; padding: 20px 32px; text-align: center; }
  .footer p { margin: 0; font-size: 12px; color: #94a3b8; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <p class="header-nombre">{{ $clubNombre }}</p>
    <p class="header-sub">Comunicación oficial del club</p>
  </div>
  <div class="body">
    <p class="saludo">Hola, <strong>{{ $nombreDestinatario }}</strong>:</p>
    <div class="contenido">{{ $cuerpo }}</div>
  </div>
  <div class="footer">
    <p>Este mensaje fue enviado por {{ $clubNombre }}.</p>
    <p style="margin-top:4px">Si tenés dudas, comunicate con la administración del club.</p>
  </div>
</div>
</body>
</html>
