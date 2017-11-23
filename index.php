<!DOCTYPE html>
<?php
	session_start();
	include_once("conn.php");
?>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/foundation.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
	<title>Amazonia Book Store</title>
</head>
<body>
	<div class="grid-container fluid bggreen">
		<div class="grid-container">
			<div class="grid-x grid-padding-x">
				<div class="medium-shrink cell">
					<a href="index.php"><img src="img/logo.png"></a>
				</div>
				<div class="auto cell">
					<form class="formsearch mtop20 right">
						<div class="grid-x grid-padding-x small-padding-collapse pright15 pleft15">
							<div class="auto cell">
								<input type="text" name="busca" placeholder="Pesquisar..">
							</div>
							<div class="shrink cell">
								<input type="submit" value="&#57347;" class="button icon">
							</div>
						</div>
					</form>
				</div>
				<div class="shrink cell">
					<div class="grid-x grid-padding-x small-padding-collapse pright15 pleft15">
						<div class="shrink cell">
							<span class="icon green big h80">&#57352;</span>
						</div>
						<div class="shrink cell">
							<div class="grid-y collapse pleft5">
								<div class="cell">
									&nbsp;
								</div>
								<div class="cell small green">
									<?php
										if (isset($_SESSION['usuario'])) {
											echo "<span class='green'>Olá, ".$_SESSION['usuario']."!</span><br>";
											echo "<a href='sair.php'>Sair</a>";
										} else {
											echo "<span class='green'>Olá, visitante!</span><br>";
											echo "<a href='index.php?login'>Entrar ou cadastrar-se</a>";
										}
									?>				
								</div>
								<div class="cell">
									&nbsp;
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="shrink cell">
					<span class="icon green big h80">&#57622;</span>
				</div>
			</div>
		</div>
	</div>
	<div class="grid-container mtop20">
		<div class="grid-x grid-padding-x">
			<div class="small-4 medium-3 cell">
				<div class="grid-x grid-padding-x">
					<div class="cell">
						<h1>Categorias</h1>
						<ul>
							<?php
								if ($result = $conn->query("SELECT classificacao FROM classificacao ORDER BY classificacao")) {
									while ($row = $result->fetch_assoc()) {
										echo "<li><a href='index.php?categoria=".utf8_encode($row['classificacao'])."'>".utf8_encode($row['classificacao'])."</a></li>";
									}
								}
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="small-8 medium-9 cell">
				<div class="grid-x grid-margin-x">
					<?php
						if (isset($_GET['categoria'])) {
							echo "<div class='cell center'><h1>".$_GET['categoria']."</h1></div>";
							$pg = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
							$ini = ($pg - 1) * 6;
							$tr = $conn->query("SELECT * FROM livro l INNER JOIN livro_classificacao lc ON l.id_livro = lc.livro INNER JOIN classificacao c ON lc.classificacao = c.id_classificacao WHERE c.classificacao = '".utf8_decode($_GET['categoria'])."'")->num_rows;
							$tp = ceil($tr / 6);
							$prev = $pg - 1;
							$next = $pg + 1;
							if ($result = $conn->query("SELECT * FROM livro l INNER JOIN livro_classificacao lc ON l.id_livro = lc.livro INNER JOIN classificacao c ON lc.classificacao = c.id_classificacao WHERE c.classificacao = '".utf8_decode($_GET['categoria'])."' ORDER BY l.id_livro DESC LIMIT ".$ini.",6")) {
								while ($row = $result->fetch_assoc()) {
									echo "<div class='medium-6 large-4 cell center item'><img src='img/capa/".$row['isbn'].".jpg'><br /><h3>".utf8_encode($row['titulo'])."</h3><h4>";
									if ($result2 = $conn->query("SELECT a.autor FROM livro_autor la INNER JOIN autor a ON la.autor = a.id_autor WHERE livro = '".$row['id_livro']."'")) {
										$c = $result2->num_rows;
										$i=1;
										while ($row2 = $result2->fetch_assoc()) {
											echo utf8_encode($row2['autor']).($c > $i ? " / " : "");
											$i++;
										}
									}
									echo "</h4><h5>R$ ".$row['valor']."</h5></div>";
								}
							}
							if (($prev >= 1) || ($pg < $tp)) {
								echo "<div class='cell center mbottom30'><div class='grid-x'><div class='auto cell'></div>";
								if ($prev >= 1) {
									echo "<div class='shrink cell box'><a href='index.php?categoria=".$_GET['categoria']."&pagina=1'><span class='icon'>&#57449;</span></a></div><div class='shrink cell box'><a href='index.php?categoria=".$_GET['categoria']."&pagina=$prev'><span class='icon'>&#57937;</span></a></div>";
								}
								for ($g=1; $g<=$tp; $g++) {
									if ($g == $pg) {
										echo "<div class='shrink cell box active'><span class='icon2'>".$g."</span></div>";
									} else {
										echo "<div class='shrink cell box'><a href='index.php?categoria=".$_GET['categoria']."&pagina=".$g."'><span class='icon2'>".$g."</span></a></div>";
									}
								}
								if ($pg < $tp) {
									echo "<div class='shrink cell box'><a href='index.php?categoria=".$_GET['categoria']."&pagina=$next'><span class='icon'>&#57936;</span></a></div><div class='shrink cell box'><a href='index.php?categoria=".$_GET['categoria']."&pagina=$tp'><span class='icon'>&#57463;</span></a></div>";
								}
								echo "<div class='auto cell'></div></div></div>";
							}
						} else if (isset($_GET['login'])) {
							echo "
								<div class='medium-6 cell'>
									<div class='grid-x'>
										<div class='cell center item'>
											<h1>Já sou cadastrado</h1>
											<form method='POST' action='entrar.php'>
												<label class='left'>E-mail</label>
												<input type='email' name='email' required='required'>
												<label class='left'>Senha</label>
												<input type='password' name='senha' required='required'>
												<input type='submit' name='entrar' class='button' value='Entrar'>
											</form>
							";
							if (isset($_GET['erro'])) {
								if ($_GET['erro'] == 1) {
									echo "<h6>E-mail ou senha incorretos!</h6>";
								}
							}
							echo "

										</div>
									</div>
								</div>
							";
							echo "
								<div class='medium-6 cell center item'>
									<h1>Quero me cadastrar</h1>
									<form method='POST' action='cadastrar.php'>
										<label class='left'>Nome Completo</label>
										<input type='text' name='nome' required='required'>
										<label class='left'>Tipo de pessoa:</label>
										<div class='grid-x grid-margin-x radiobox'>
											<div class='shrink cell left'>
												<input type='radio' name='tipo' value='f' id='pf' onchange='trocaPessoa()' checked>Física
											</div>
											<div class='auto cell left'>
												<input type='radio' name='tipo' value='j' id='pj' onchange='trocaPessoa()' > Jurídica
											</div>
										</div>
										<label class='left' id='lp'>CPF:</label>
										<input type='text' name='cpfcnpj' id='tp' required='required'>
										<label class='left'>E-mail:</label>
										<input type='email' name='email' required='required'>
										<label class='left'>Senha</label>
										<input type='password' name='senha' required='required'>
										<input type='submit' name='cadastrar' class='button' value='Criar cadastro'>
									</form>
							";
							if (isset($_GET['erro'])) {
								if ($_GET['erro'] == 2) {
									echo "<h6>Não foi possível fazer o cadastro!</h6>";
								}
							}
							echo "
								</div>
							";
						} else if (isset($_GET['busca'])) {
							echo "<div class='cell center'><h1>RESULTADO DA BUSCA: ".$_GET['busca']."</h1></div>";
							$pg = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
							$ini = ($pg - 1) * 6;
							$tr = $conn->query("SELECT DISTINCT l.isbn, l.titulo, l.id_livro, l.valor FROM livro l INNER JOIN livro_autor la ON l.id_livro = la.livro INNER JOIN autor a ON la.autor = a.id_autor WHERE l.titulo LIKE '%".utf8_decode($_GET['busca'])."%' UNION SELECT l.isbn, l.titulo, l.id_livro, l.valor FROM autor a INNER JOIN livro_autor la ON a.id_autor = la.autor INNER JOIN livro l ON la.livro = l.id_livro WHERE a.autor LIKE '%".utf8_decode($_GET['busca'])."%'")->num_rows;
							$tp = ceil($tr / 6);
							$prev = $pg - 1;
							$next = $pg + 1;
							if ($result = $conn->query("SELECT DISTINCT l.isbn, l.titulo, l.id_livro, l.valor FROM livro l INNER JOIN livro_autor la ON l.id_livro = la.livro INNER JOIN autor a ON la.autor = a.id_autor WHERE l.titulo LIKE '%".utf8_decode($_GET['busca'])."%' UNION SELECT l.isbn, l.titulo, l.id_livro, l.valor FROM autor a INNER JOIN livro_autor la ON a.id_autor = la.autor INNER JOIN livro l ON la.livro = l.id_livro WHERE a.autor LIKE '%".utf8_decode($_GET['busca'])."%' LIMIT ".$ini.",6")) {
								while ($row = $result->fetch_assoc()) {
									echo "<div class='medium-6 large-4 cell center item'><img src='img/capa/".$row['isbn'].".jpg'><br /><h3>".utf8_encode($row['titulo'])."</h3><h4>";
									if ($result2 = $conn->query("SELECT a.autor FROM livro_autor la INNER JOIN autor a ON la.autor = a.id_autor WHERE livro = '".$row['id_livro']."'")) {
										$c = $result2->num_rows;
										$i=1;
										while ($row2 = $result2->fetch_assoc()) {
											echo utf8_encode($row2['autor']).($c > $i ? " / " : "");
											$i++;
										}
									}
									echo "</h4><h5>R$ ".$row['valor']."</h5></div>";
								}
							}
							if (($prev >= 1) || ($pg < $tp)) {
								echo "<div class='cell center mbottom30'><div class='grid-x'><div class='auto cell'></div>";
								if ($prev >= 1) {
									echo "<div class='shrink cell box'><a href='index.php?busca=".$_GET['busca']."&pagina=1'><span class='icon'>&#57449;</span></a></div><div class='shrink cell box'><a href='index.php?busca=".$_GET['busca']."&pagina=$prev'><span class='icon'>&#57937;</span></a></div>";
								}
								for ($g=1; $g<=$tp; $g++) {
									if ($g == $pg) {
										echo "<div class='shrink cell box active'><span class='icon2'>".$g."</span></div>";
									} else {
										echo "<div class='shrink cell box'><a href='index.php?busca=".$_GET['busca']."&pagina=".$g."'><span class='icon2'>".$g."</span></a></div>";
									}
								}
								if ($pg < $tp) {
									echo "<div class='shrink cell box'><a href='index.php?busca=".$_GET['busca']."&pagina=$next'><span class='icon'>&#57936;</span></a></div><div class='shrink cell box'><a href='index.php?busca=".$_GET['busca']."&pagina=$tp'><span class='icon'>&#57463;</span></a></div>";
								}
								echo "<div class='auto cell'></div></div></div>";
							}
						} else if (isset($_POST['cadastrar'])) {
							if ($result = $conn->query("INSERT INTO cliente(nome, tipo, ".(($_POST['tipo'] == 'f') ? "cpf" : "cnpj").", email, senha) VALUES('".utf8_decode($_POST['nome'])."', '".$_POST['tipo']."', '".utf8_decode($_POST['cpfcnpj'])."', '".utf8_decode($_POST['email'])."', '".utf8_decode($_POST['senha'])."')")) {
								echo "cadastrado";
							}
						} else {
							echo "<div class='cell center'><h1>Lançamentos</h1></div>";
							if ($result = $conn->query("SELECT * FROM livro ORDER BY id_livro DESC LIMIT 6")) {
								while ($row = $result->fetch_assoc()) {
									echo "<div class='medium-6 large-4 cell center item'><img src='img/capa/".$row['isbn'].".jpg'><br /><h3>".utf8_encode($row['titulo'])."</h3><h4>";
									if ($result2 = $conn->query("SELECT a.autor FROM livro_autor la INNER JOIN autor a ON la.autor = a.id_autor WHERE livro = '".$row['id_livro']."'")) {
										$c = $result2->num_rows;
										$i=1;
										while ($row2 = $result2->fetch_assoc()) {
											echo utf8_encode($row2['autor']).($c > $i ? " / " : "");
											$i++;
										}
									}
									echo "</h4><h5>R$ ".$row['valor']."</h5></div>";
								}
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>