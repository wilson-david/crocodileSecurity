<?php
require '../../vendor/autoload.php';

$nombre = $_POST['nombre'];
$num_doc = $_POST['num_doc'];
$fecha = $_POST['fecha'];
$salario = $_POST['salario'];

$fecha_sistema = date('Y-m-d');
$html = "
<html>
<head>
  <meta charset='UTF-8'>
  <title>Document</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    .container {
      width: 600px;
      margin: 0 auto;
      padding: 20px;
      border: 1px solid #ccc;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    p {
      margin-bottom: 10px;
    }

    .details {
      margin-bottom: 30px;
    }

    .details p {
      display: inline-block;
      margin-right: 10px;
    }

    .signature {
      text-align: right;
      margin-top: 30px;
    }

    .signature p {
      margin-bottom: 5px;
    }
  </style>
</head>

<body>

  <body>
    <div class='container'>
      <h1>Certificado Laboral</h1>

      <div class='details'>
        <p>Fecha: $fecha_sistema</p>
        <br>
        <p>Nombre del Empleado: John Doe</p>
        <br>
        <p>Apellido: Desarrollador Web</p>
        <br>
        <p>Empresa: XYZ Corporation</p>
      </div>

      <p>Por medio de la presente, certificamos que el $nombre Identificado con cédula de ciudadania $num_doc de Cali</p>

      <p>se encuentra vinculado a esta empresa desde  $fecha</p>

      <p>El señor $nombre, emplea un cargo de VIGILANTE el cual devenga un salario de $salario, pesos mas de </p>

      </div>
    </div>
  </body>
</body>

</html>
";

use Dompdf\Dompdf;

// Crea una instancia de Dompdf
$dompdf = new Dompdf();

// Carga el HTML a convertir en PDF
$dompdf->loadHtml($html);

// Renderiza el PDF
$dompdf->render();

// Establece las opciones de descarga
$options = array(
    'Attachment' => true, // Establece 'Attachment' en true para descargar el archivo
);

// Genera el nombre del archivo
$filename = 'Certificado.pdf';

// Descarga el PDF en el navegador
$dompdf->stream($filename, $options);
?>
?>


