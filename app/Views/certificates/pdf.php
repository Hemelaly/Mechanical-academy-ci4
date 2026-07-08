<!doctype html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <style>
        <?php if (!empty($studentNameFontCssUrl)): ?>
        @import url('<?= esc($studentNameFontCssUrl) ?>');
        <?php endif; ?>

        @page {
            margin: 0;
            size: 210mm 148mm landscape;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            width: 210mm;
            height: 148mm;
            font-family: DejaVu Sans, sans-serif;
            color: #1a1a1a;
        }

        .sheet {
            position: relative;
            width: 210mm;
            height: 148mm;
            overflow: hidden;
        }

        .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 148mm;
        }

        .field {
            position: absolute;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
            line-height: 1;
        }

        .field-date {
            left: 10mm;
            top: 27mm;
            width: 46mm;
            height: 4.5mm;
            font-size: 8.5pt;
        }

        .field-student {
            left: 37mm;
            top: 72mm;
            width: 136mm;
            height: 12.5mm;
            font-size: 20pt;
            font-family: "Playwrite England Joined", "Great Vibes", "Ms Madi", DejaVu Sans, cursive;
            overflow: visible;
        }

        .field-course {
            left: 34mm;
            top: 96mm;
            width: 142mm;
            height: 12.5mm;
            font-size: 15pt;
            font-family: "Playwrite England Joined", "Great Vibes", "Ms Madi", DejaVu Sans, cursive;
            overflow: visible;
        }

        .field-instructor {
            left: 44mm;
            top: 123mm;
            width: 52mm;
            height: 7.5mm;
            font-size: 11pt;
            font-family: "Ms Madi", "Great Vibes", DejaVu Sans, cursive;
            overflow: visible;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <img class="bg" src="<?= esc(base_url('assets/certificado/certificado-bg.png')) ?>" alt="">

        <div class="field field-date"><?= esc($issuedDate ?? '') ?></div>
        <div class="field field-student"><?= esc($studentName ?? '') ?></div>
        <div class="field field-course"><?= esc($courseName ?? '') ?></div>
        <?php if (!empty($instructorName)): ?>
            <div class="field field-instructor"><?= esc($instructorName) ?></div>
        <?php endif; ?>
    </div>
</body>

</html>
