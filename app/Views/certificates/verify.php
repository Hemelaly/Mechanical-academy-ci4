<!doctype html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>Verificar Certificado</title>
</head>

<body>
    <h2>Verificação do Certificado</h2>

    <?php if (!$isValid): ?>
        <p style="color:red;">Certificado inválido (hash não confere).</p>
    <?php else: ?>
        <p style="color:green;">Certificado válido ✅</p>
    <?php endif; ?>

    <ul>
        <li>Número: <b><?= esc($cert['number_certificate']) ?></b></li>
        <li>UUID: <b><?= esc($cert['uuid_certificate']) ?></b></li>
        <li>Utilizador: <b><?= esc($cert['id_user_cerificate']) ?></b></li>
        <li>Curso: <b><?= esc($cert['id_course_cerificate']) ?></b></li>
        <li>Emitido em: <b><?= esc($cert['issued_at_cerificate']) ?></b></li>
    </ul>

    <p>
        <?php if (empty($cert['pdf_path_cerificate'])): ?>
            <a href="<?= site_url('certificados/gerar/' . $cert['uuid_certificate']) ?>">Gerar PDF</a>
        <?php else: ?>
            <a href="<?= site_url('certificados/download/' . $cert['uuid_certificate']) ?>">Download PDF</a>
        <?php endif; ?>

    </p>
</body>

</html>