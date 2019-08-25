<!DOCTYPE html>
<?php
  date_default_timezone_set('America/Sao_Paulo');

  include 'funcoes.php'; //função usada: printHeader();
  $tipo_usuario = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : '';
?>
<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<title>Cadastrar - Prove</title>
</head>

<body>
  <?php printHeader(); /*include 'funcoes.php'; lá em cima*/ ?>

  <main>

    <center>

      <div class="container text-center"><br><br>
            <h1 class="display-4">Cadastre-se</h1><br><br>

          <div class="col s12 container">
            <p class="lead">Desejo me cadastrar como: </p>
            <form action="" method="post">
              <button type="submit" class="btn btn-outline-dark btn-lg" name="tipo_usuario" value="aluno"
              <?php if($tipo_usuario == 'aluno') echo 'disabled' ?>
              >Aluno</button>

              <button type="submit" class="btn btn-outline-dark btn-lg" name="tipo_usuario" value="professor"
              <?php if($tipo_usuario == 'professor') echo 'disabled' ?>
              >Professor</button>
            </form>
          </div>
          <br><br>
          <?php if($tipo_usuario != '') { ?>

          <div class="col s12 container">
            <?php
              if($tipo_usuario == 'aluno') echo "<form action='alunos_pdo.php' method='post'>";
              else echo "<form action='professores_pdo.php' method='post'>";
            ?>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="matricula">Nome</label>
                  <input type="text" class="form-control" name="nome" id="nome" placeholder="Exemplo: Mickey Mouse">
                </div>
                <div class="form-group col-md-6">
                  <label for="senha">Senha</label>
                  <input type="password" class="form-control" name="senha" id="senha" placeholder="Exemplo: ********">
                </div>
              </div>
              <div class="form-group">
                <label for="email">Matrícula</label>
                <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Exemplo: 2017306663">
              </div>
              <div class="form-group">
                <label for="nome">E-mail</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Exemplo: email@exemplo.com">
              </div>
              <div class="form-group">
                <label for="example-date-input" class="col-2 col-form-label">Data de Nascimento</label>
                <input class="form-control" id="data_nascimento" name="data_nascimento" type="date" value="2001-08-19" id="example-date-input">
              </div>
              <input type="hidden" name="ultimo_login" id="ultimo_login" value="<?php echo date('Y-m-d H:i:s'); ?>">
              <input type="hidden" name="tipo_usuario" value="<?php echo $tipo_usuario; ?>">
              <button type="submit" name="acao" value="cadastrar" class="btn btn-primary">Cadastrar</button>
              <br><br>
              <p>Já é cadastrado? <a href="entrar.php">Entre aqui</a></p>
            </form>
          <?php } ?>

        </div>
      </div>

    </center>
  </main>


  <!--  Scripts-->
  <script src="assets/js/jquery.mask.min.js"></script>
  <script src="assets/js/jquery-2.1.1.min.js"></script>
  <script src="assets/js/materialize.min.js"></script>
  <script src="assets/js/init.js"></script>

  </body>

</html>
