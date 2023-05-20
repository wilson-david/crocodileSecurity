<?php session_start();
$nombre = $_SESSION['nombre'];
$rol = $_SESSION['rol'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Menu Principal</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link href="../css/tbls_descarga.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

    <header>
        <!-- ======================================Navigation Bar================================================= -->
        <nav class="navbar navbar-expand-lg navStyle">
            <!--   <a class="brand-navbar" href="#"><img src="image/logo.png" alt="Responsive image" height="60px"></a> -->
            <button class="navbar-toggler" data-toggle="collapse" data-target="#mainMenu">
                <span><i class="fas fa-align-right iconStyle"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="mainMenu">
            <?php if($rol == 1){?>
                <ul class="navbar-nav ml-auto navList">
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-user"></i>Empleados<span
                                class="sr-only">(current)</span></a></li>
                    <li class="nav-item">
                        <a href="services.html" class="nav-link"><i class="fas fa-address-card"></i>Horarios</a>
                    </li>
                    <li class="nav-item">
                        <a href="services.html" class="nav-link"><i class="fas fa-solid fa-file"></i>Certificadas</a>
                    </li>
                    
                </ul>
                <?php }?>
            </div>
        </nav>


        <h3 style="text-align: left;">Bienvenido: <?php echo $nombre;?></h3>
        <?php if($rol == 3){?>
    <table>
        <thead>
            <tr>
                <th>Acciones</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Tipo doc</th>
                <th>#Documento</th>
            </tr>
        </thead>
        <tbody id="contenido_tabla">

        </tbody>
    </table>

    <?php }?>
    </header>

</body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="../js/jquery-3.6.4.js"></script>
<script>

consultar();
        function consultar(){
        $.ajax({
            url: "../controllers/empleado/getUserNomina.php",
            dataType: 'json',
            success: function(data) {
                $("#contenido_tabla").append(data.CONTENIDO);
            },
            error: function(xhr, status, error) {
                console.log("Error al procesar la solicitud:", error);
            }
        });
    }
    
    function pagoNomina(nombre,apellido,salario,aux_transporte){
        $.ajax({
                //url: "user/desc_nomina.php",
                url: "user/desc_nomina.php",
                type: 'POST',
                data: {
                    nombre: nombre,
                    apellido: apellido,
                    salario: salario,
                    aux_transporte: aux_transporte
                },
                dataType: 'json',
                success: function(data) {

                },
            
            });
    }

    function certificado_labo(nombre,apellido,salario,aux_transporte){
        $.ajax({
                url: "user/certificado_labo.php",
                type: 'POST',
                data: {
                    nombre: nombre,
                    apellido: apellido,
                    salario: salario,
                    aux_transporte: aux_transporte
                },
                dataType: 'json',
                success: function(data) {

                },
            
            });
    }
</script>
</html>