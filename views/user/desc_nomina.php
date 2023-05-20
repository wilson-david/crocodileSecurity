<?php
require '../../vendor/autoload.php';

$nombre = $_GET["nombre"];
$apellido = $_GET["apellido"];
$salario = $_GET["salario"];
$aux_transporte = $_GET["aux_transporte"];

$total =  ((int)$salario + (int)$aux_transporte ) -100;
$html = "
<html>
  <head>
    <meta charset='UTF-8'>
    <title>Formato de Pago de Nómina</title>
    
  <style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

h1,
h2 {
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th,
td {
    padding: 8px;
    border: 1px solid #ccc;
}

th {
    background-color: #f2f2f2;
    font-weight: bold;
}

.total {
    text-align: right;
    font-weight: bold;
}

.signature {
    text-align: right;
    margin-top: 40px;
}
  </style>
  </head>
    <body>
      <div class='container'>
        <h1>Formato de Pago de Nómina</h1>
        <h2>Información del Empleado</h2>
        <table>
          <tr>
            <th>Nombre:</th>
            <td><$nombre</td>
          </tr>
          <tr>
            <th>Apellido:</th>
            <td>$apellido</td>
          </tr>
        </table>
        
        <h2>Detalle del Pago</h2>
        <table>
          <tr>
            <th>Concepto</th>
            <th>Monto</th>
          </tr>
          <tr>
            <td>Salario Base</td>
            <td>$salario</td>
          </tr>
          <tr>
            <td>Auxilio de transporte</td>
            <td>$aux_transporte</td>
          </tr>
          <tr>
            <td>Descuento de Seguridad Social</td>
            <td>100.000</td>
          </tr>
          <tr class='total'>
            <td>Total</td>
            <td>$total</td>
          </tr>
        </table>
        
        <div class='signature'>
          <p>Firma del Empleado: ________________________</p>
        </div>
      </div>
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
$filename = 'Nomina.pdf';

// Descarga el PDF en el navegador
$dompdf->stream($filename, $options);
?>

<!-- <!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Formato de Pago de Nómina</title>
  
  <link href="../../css/des_nomina.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.10.2/jspdf.umd.min.js"></script>

</head>
<body>
  <div class="container">
    <h1>Formato de Pago de Nómina</h1>
    <h2>Información del Empleado</h2>
    <table>
      <tr>
        <th>Nombre:</th>
        <td><?php echo($_GET['nombre']) ?></td>
      </tr>
      <tr>
        <th>Apellido:</th>
        <td><?php echo($_GET['apellido']) ?></td>
      </tr>
    </table>
    
    <h2>Detalle del Pago</h2>
    <table>
      <tr>
        <th>Concepto</th>
        <th>Monto</th>
      </tr>
      <tr>
        <td>Salario Base</td>
        <td><?php echo($_GET['salario']) ?></td>
      </tr>
      <tr>
        <td>Bonificación</td>
        <td>$500</td>
      </tr>
      <tr>
        <td>Descuento de Seguridad Social</td>
        <td>-$100</td>
      </tr>
      <tr class="total">
        <td>Total</td>
        <td>$5,400</td>
      </tr>
    </table>
    
    <div class="signature">
      <p>Firma del Empleado: ________________________</p>
      <p>Fecha: 18 de mayo de 2023</p>
    </div>
  </div>
  <button type="button" onclick="generarPDF();">asd</button>
</body>




</html>
 -->