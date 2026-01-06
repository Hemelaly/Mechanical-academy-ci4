<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .page {
            width: 100%;
            height: 100%;
            padding: 60px;
            border: 12px solid #111827;
            position: relative;
        }

        .title {
            font-size: 42px;
            font-weight: 700;
            text-align: center;
            margin-top: 40px;
        }

        .name {
            font-size: 34px;
            font-weight: 700;
            text-align: center;
            margin: 30px 0;
        }

        .meta {
            text-align: center;
            font-size: 16px;
            color: #111827;
        }

        .footer {
            position: absolute;
            bottom: 40px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .badge {
            font-size: 12px;
            padding: 6px 10px;
            border: 1px solid #111827;
            border-radius: 999px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="title">Certificado de Conclusão</div>

        <div class="meta">Certificamos que</div>
        <div class="name"><?= esc($studentName) ?></div>

        <div class="meta">
            concluiu com sucesso o curso <b><?= esc($courseName) ?></b><br>
            em <?= esc($issuedDate) ?>.
        </div>

        <div class="footer">
            <div>
                Nº: <b><?= esc($certificateNumber) ?></b><br>
                Código: <b><?= esc($uuid) ?></b>
            </div>
            <div class="badge">
                Verificar: <?= esc($verifyUrl) ?>
            </div>
        </div>
    </div>
</body>

</html>